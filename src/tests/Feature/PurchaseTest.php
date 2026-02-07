<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use App\Models\ShippingAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 10-1 「購入する」ボタンを押下すると購入が完了する
     */
    public function test_user_can_complete_purchase()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->hasProfile([
            'postcode' => '123-4567',
            'address'  => '東京都新宿区',
        ])->create();
        $item = Item::factory()->create(['price' => 1000]);

        // 2. Stripeのモック作成
        // 外部APIを叩かずに「成功した」というレスポンスを返すように身代わりを作ります
        $mockSession = (object)['id' => 'test_id', 'url' => 'https://example.com/checkout'];
        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 3. 実行：購入リクエストを送信
        // バリデーションエラーを避けるため、必要な項目を全て送る
        $response = $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 1,
            ]);

        // 4. Stripe画面へのリダイレクト確認（storePurchaseの戻り値）
        $response->assertRedirect('https://example.com/checkout');

        /// 5. 実行：決済成功後のコールバック（successPurchase）をシミュレート
        // session() メソッドを使って、コントローラーが期待するセッション状態を作る
        $response = $this->withSession([
            "pending_purchase_{$item->id}" => [
                'payment_method' => 1,
                'stripe_checkout_id' => 'test_id',
            ]
        ])->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]));

        // 6. 検証：データベースに保存されているか
        $this->assertDatabaseHas('sold_items', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => 1,
        ]);

        $this->assertDatabaseHas('shipping_addresses', [
            'postcode' => $user->profile->postcode,
            'address'  => $user->profile->address,
        ]);

        // 7. 検証：最終的に「マイリスト（トップページ）」へリダイレクトされるか
        $response->assertRedirect('/?tab=mylist');
    }

    /**
     * ID: 10-2 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function test_purchased_item_shows_sold_label_on_index_after_purchase()
    {
        // 1. 準備：ユーザー、プロフィール、商品を作成
        $user = User::factory()->hasProfile([
            'postcode' => '123-4567',
            'address'  => '東京都新宿区',
        ])->create();

        $item = Item::factory()->create(['name' => 'テスト商品A']);

        // 2. Stripeのモック作成
        $mockSession = (object)['id' => 'test_id', 'url' => 'https://example.com/checkout'];
        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 3. 実行：商品購入画面から「購入する」ボタンを押下 (POSTリクエスト)
        $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 1,
            ]);

        // 4. 実行：決済成功後のコールバック処理 (DB保存の実行)
        // ここで SoldItem が保存されます
        $this->withSession([
            "pending_purchase_{$item->id}" => [
                'payment_method' => 1,
                'stripe_checkout_id' => 'test_id',
            ]
        ])->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]));

        // 5. 実行：商品一覧画面を表示する
        $response = $this->get(route('item.index'));

        // 6. 検証：一覧画面で「Sold」ラベルが表示されているか
        $response->assertStatus(200);

        // Bladeの @if ($item->isSold()) が正しく機能しているか確認
        $response->assertSeeInOrder(['テスト商品A', 'Sold']);
    }

    /**
     * 10-3 プロフィール画面の「購入した商品」一覧に購入した商品が表示される
     */
    public function test_purchased_item_is_displayed_in_user_profile_buy_list()
    {
        // 1. 準備：ユーザー、プロフィール、商品を作成
        $user = User::factory()->hasProfile([
            'postcode' => '123-4567',
            'address'  => '東京都新宿区',
        ])->create();

        $item = Item::factory()->create([
            'name' => 'プロフィール確認用商品',
            'image_path' => 'item_images/test_image.jpg' // テスト用の画像パス
        ]);

        // 2. Stripeのモック作成
        $mockSession = (object)['id' => 'test_id', 'url' => 'https://example.com/checkout'];
        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 3. 実行：商品購入画面から「購入する」ボタンを押下
        $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 1,
            ]);

        // 4. 実行：決済成功後のコールバック処理（DB保存の実行）
        $this->withSession([
            "pending_purchase_{$item->id}" => [
                'payment_method' => 1,
                'stripe_checkout_id' => 'test_id',
            ]
        ])->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]));

        // 5. 実行：プロフィール画面の「購入した商品」一覧（?page=buy）を表示する
        // クエリパラメータ ?page=buy を付与してアクセス
        $response = $this->actingAs($user)->get(route('mypage', ['page' => 'buy']));

        // 6. 検証：プロフィール画面で購入した商品名と画像が表示されているか
        $response->assertStatus(200);
        $response->assertSee('プロフィール確認用商品');
        $response->assertSee('item_images/test_image.jpg');
    }
}
