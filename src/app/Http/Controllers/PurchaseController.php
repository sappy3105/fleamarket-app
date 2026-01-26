<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\SoldItem;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    // 商品購入画面の表示
    public function showPurchase($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // セッションに変更後の住所があればそれを使う、なければプロフィールの住所を使う
        $address = session("shipping_address_{$item_id}", [
            'postcode' => $user->profile->postcode ?? '',
            'address'  => $user->profile->address ?? '',
            'building' => $user->profile->building ?? '',
        ]);

        return view('purchase.index', compact('item', 'address'));
    }

    // 住所変更画面の表示
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('purchase.address', compact('item'));
    }

    // 住所をセッションに一時保存
    public function updateAddress(AddressRequest $request, $item_id)
    {
        // 購入確定までセッションに保持
        session(["shipping_address_{$item_id}" => $request->only(['postcode', 'address', 'building'])]);

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    // 購入後Stripeの決済ページへ
    public function storePurchase(PurchaseRequest $request, $item_id)
    {
        // dd($request->all());

        $item = Item::findOrFail($item_id);

        // 2. Stripeの設定（.envから読み込み）
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // 3. 支払い方法の判定 (1:コンビニ, 2:カード)
        $payment_method_types = ($request->payment_method == 1) ? ['konbini'] : ['card'];

        // 4. Stripe Checkoutセッションの作成
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $payment_method_types,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item_id]),
            'cancel_url' => route('purchase.show', ['item_id' => $item_id]),
        ]);

        // 5. 支払い方法を一時的にセッションへ保存
        session(["pending_purchase_{$item_id}" => [
            'payment_method' => $request->payment_method,
        ]]);

        // 6. Stripeの決済画面へリダイレクト
        return redirect($checkout_session->url);
    }

    // Stripeの決済処理後
    public function successPurchase($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // セッションから保留中の購入情報と住所を取得
        $pending = session("pending_purchase_{$item_id}");
        $sessionAddress = session("shipping_address_{$item_id}");

        // もしセッションに住所がなければ、プロフィールの住所を使う
        $addressData = $sessionAddress ?? [
            'postcode' => $user->profile->postcode,
            'address'  => $user->profile->address,
            'building' => $user->profile->building,
        ];

        // データベースへの保存（2つのテーブルに保存するためトランザクションを使う）
        DB::transaction(function () use ($item, $user, $pending, $addressData) {
            // 1. sold_items テーブルに保存
            $soldItem = SoldItem::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
                'payment_method' => $pending['payment_method'],
                // StripeのIDを保存する場合はここに入れる（今回は簡易化）
                'stripe_checkout_id' => $pending['stripe_checkout_id'] ?? null,
            ]);

            // 2. shipping_addresses テーブルに保存
            ShippingAddress::create([
                'sold_item_id' => $soldItem->id,
                'postcode' => $addressData['postcode'],
                'address'  => $addressData['address'],
                'building' => $addressData['building'],
            ]);
        });

        // 最後にセッションをクリア
        session()->forget(["pending_purchase_{$item_id}", "shipping_address_{$item_id}"]);

        // 完了画面へ（まだ作っていなければトップへリダイレクトなど）
        return redirect('/?tab=mylist');
    }
}
