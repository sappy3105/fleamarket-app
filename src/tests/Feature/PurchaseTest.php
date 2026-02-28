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
            'building' => 'テストビル101'
        ])->create();
        $item = Item::factory()->create(['price' => 1000]);

        // 2. Stripeのモック作成（Session IDを固定する）
        $stripeSessionId = 'cs_test_random_string';
        $mockSession = (object)[
            'id' => $stripeSessionId,
            'url' => 'https://example.com/checkout'
        ];

        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 3. 実行：購入リクエストを送信
        $response = $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 1,
            ]);

        // 4. 検証：StripeへのリダイレクトとPending保存
        $response->assertRedirect('https://example.com/checkout');

        $this->assertDatabaseHas('sold_items', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => 1,
            'stripe_checkout_id' => $stripeSessionId,
            'status' => 'pending', // 最初はPending
        ]);

        // 配送先情報の保存確認
        $this->assertDatabaseHas('shipping_addresses', [
            'postcode' => $user->profile->postcode,
            'address'  => $user->profile->address,
        ]);

        // 5. 実行：Webhookのシミュレート
        // WebhookControllerに送るダミーのJSONデータ（Stripeからの通知を模倣）
        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $stripeSessionId,
                    'payment_status' => 'paid'
                ]
            ]
        ];

        // 署名検証（constructEvent）をスキップするため、モック化する
        $this->mock('alias:Stripe\Webhook', function ($mock) use ($payload) {
            $mock->shouldReceive('constructEvent')->andReturn((object)[
                'type' => $payload['type'],
                'data' => (object)['object' => (object)$payload['data']['object']]
            ]);
        });

        // Webhookエンドポイントを叩く
        $webhookResponse = $this->postJson('/api/webhook', $payload);
        $webhookResponse->assertStatus(200);

        // 6. 検証：データベースに保存されているか
        $this->assertDatabaseHas('sold_items', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => 1,
            'stripe_checkout_id' => $stripeSessionId,
            'status' => 'paid',
        ]);

        // 7. 実行：決済成功後のリダイレクト画面（successPurchase）
        $response = $this->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]));
        $response->assertRedirect('/?tab=mylist');
    }

    /**
     * 10-2 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function test_purchased_item_shows_sold_label_on_index_after_purchase()
    {
        // 1. 準備：ユーザー、プロフィール、商品を作成
        $user = User::factory()->hasProfile()->create();
        $item = Item::factory()->create(['name' => 'テスト商品A']);

        // 2. Stripeのモック作成
        $stripeSessionId = 'cs_test_102';
        $mockSession = (object)['id' => $stripeSessionId, 'url' => 'https://example.com/checkout'];
        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 3. 実行：商品購入画面から「購入する」ボタンを押下 (POSTリクエスト)
        $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 1,
            ]);

        // 4. 実行：Webhookをシミュレートしてステータスを 'paid' に更新
        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $stripeSessionId,
                    'payment_status' => 'paid'
                ]
            ]
        ];

        $this->mock('alias:Stripe\Webhook', function ($mock) use ($payload) {
            $mock->shouldReceive('constructEvent')->andReturn((object)[
                'type' => $payload['type'],
                'data' => (object)['object' => (object)$payload['data']['object']]
            ]);
        });

        $this->postJson('/api/webhook', $payload)->assertStatus(200);

        // 5. 検証：DBのステータスが 'paid' になっていることを念のため確認
        $this->assertDatabaseHas('sold_items', [
            'item_id' => $item->id,
            'status'  => 'paid',
        ]);

        // 6. 実行：商品一覧画面を表示する
        $response = $this->get(route('item.index'));

        // 7. 検証：一覧画面で「Sold」ラベルが表示されているか
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
        $stripeSessionId = 'cs_test_103';
        $mockSession = (object)['id' => $stripeSessionId, 'url' => 'https://example.com/checkout'];
        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 3. 実行：商品購入画面から「購入する」ボタンを押下
        $this->actingAs($user)
            ->post(route('purchase.store', ['item_id' => $item->id]), [
                'payment_method' => 1,
            ]);

        // 4. 実行：Webhookをシミュレートしてステータスを 'paid' に更新
        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $stripeSessionId,
                    'payment_status' => 'paid'
                ]
            ]
        ];

        $this->mock('alias:Stripe\Webhook', function ($mock) use ($payload) {
            $mock->shouldReceive('constructEvent')->andReturn((object)[
                'type' => $payload['type'],
                'data' => (object)['object' => (object)$payload['data']['object']]
            ]);
        });

        $this->postJson('/api/webhook', $payload)->assertStatus(200);

        // 5. 実行：決済成功後の処理（セッションクリア等）
        $this->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]));

        // 6. 実行：プロフィール画面の「購入した商品」一覧（?page=buy）を表示する
        // クエリパラメータ ?page=buy を付与してアクセス
        $response = $this->actingAs($user)->get(route('mypage', ['page' => 'buy']));

        // 7. 検証：プロフィール画面で購入した商品名と画像が表示されているか
        $response->assertStatus(200);
        $response->assertSee('プロフィール確認用商品');
        $response->assertSee('item_images/test_image.jpg');
    }
}
