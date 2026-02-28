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

        // 1. セッションに「変更後の住所」があるか確認
        if (session()->has("shipping_address_{$item_id}")) {
            $address = session("shipping_address_{$item_id}");
        } else {
            // 2. なければプロフィールの住所を初期値にする
            $address = [
                'postcode' => $user->profile->postcode ?? '',
                'address'  => $user->profile->address ?? '',
                'building' => $user->profile->building ?? '',
            ];
        }

        return view('purchase.index', compact('item', 'address'));
    }

    // 住所変更画面の表示
    public function editAddress(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 1. もしURLに支払い方法が含まれていたらセッションに入れる
        if ($request->has('payment_method')) {
            session(["payment_method_{$item_id}" => $request->payment_method]);
        }

        // 2. 配送先情報の取得
        // すでにセッションに変更後の住所があるか確認
        if (session()->has("shipping_address_{$item_id}")) {
            $address = session("shipping_address_{$item_id}");
        } else {
            // なければプロフィールの住所を初期値にする
            $address = [
                'postcode' => $user->profile->postcode ?? '',
                'address'  => $user->profile->address ?? '',
                'building' => $user->profile->building ?? '',
            ];
        }

        return view('purchase.address', compact('item', 'address'));
    }

    // 住所をセッションに一時保存
    public function updateAddress(AddressRequest $request, $item_id)
    {
        // バリデーション済みデータを取得
        $validated = $request->validated();

        // 購入確定までセッションに保持
        session(["shipping_address_{$item_id}" => $validated]);

        return redirect()->route('purchase.show', ['item_id' => $item_id]);
    }

    // 購入後Stripeの決済ページへ
    public function storePurchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 1. Stripeの設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // 2. 支払い方法の判定 (1:コンビニ, 2:カード)
        $payment_method_types = ($request->payment_method == 1) ? ['konbini'] : ['card'];

        // 3. Stripe Checkoutセッションの作成
        $checkout_session = Session::create([
            'payment_method_types' => $payment_method_types,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item_id]),
            'cancel_url' => route('purchase.show', ['item_id' => $item_id]),
        ]);

        // 4. DBに「未払い(pending)」状態で保存
        DB::transaction(function () use ($item, $user, $request, $checkout_session, $item_id) {
            $soldItem = SoldItem::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
                'payment_method' => $request->payment_method,
                'stripe_checkout_id' => $checkout_session->id,
                'status' => 'pending',
            ]);

            $sessionAddress = session("shipping_address_{$item_id}");
            $addressData = $sessionAddress ?? [
                'postcode' => $user->profile->postcode ?? '',
                'address'  => $user->profile->address ?? '',
                'building' => $user->profile->building ?? '',
            ];

            ShippingAddress::create([
                'sold_item_id' => $soldItem->id,
                'postcode' => $addressData['postcode'],
                'address'  => $addressData['address'],
                'building' => $addressData['building'],
            ]);
        });

        // 5. Stripeの決済画面へリダイレクト
        return redirect($checkout_session->url);
    }

    // Stripeの決済処理後
    public function successPurchase($item_id)
    {
        // セッションをクリア
        session()->forget([
            "pending_purchase_{$item_id}",
            "shipping_address_{$item_id}",
            "payment_method_{$item_id}"
        ]);

        // マイリストタブに戻る
        return redirect('/?tab=mylist');
    }
}
