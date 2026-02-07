<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * 12-1 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_updated_address_is_reflected_on_purchase_page()
    {
        // 1. ユーザーにログインする
        $user = User::factory()->hasProfile()->create();
        $item = Item::factory()->create();

        // 2. 送付先住所変更画面で住所を登録する
        // 住所更新ルート（purchase.address.update）へPOSTリクエストを送信
        $newAddress = [
            'postcode' => '999-8888',
            'address'  => '大阪府大阪市中央区',
            'building' => 'テストビル101',
        ];

        $this->actingAs($user)
            ->post(route('purchase.address.update', ['item_id' => $item->id]), $newAddress)
            ->assertRedirect(route('purchase.show', ['item_id' => $item->id]));

        // 3. 商品購入画面を再度開く
        $response = $this->get(route('purchase.show', ['item_id' => $item->id]));

        // 検証：登録した住所が正しく反映されている
        $response->assertStatus(200);
        $response->assertSee('999-8888');
        $response->assertSee('大阪府大阪市中央区');
        $response->assertSee('テストビル101');
    }

    /**
     * 12-2 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_purchased_item_is_linked_to_updated_shipping_address()
    {
        // 1. ユーザーにログインし、商品を準備
        $user = User::factory()->hasProfile()->create();
        $item = Item::factory()->create();

        // Stripeのモック
        $mockSession = (object)['id' => 'test_id', 'url' => 'https://example.com/checkout'];
        $this->mock('alias:Stripe\Checkout\Session', function ($mock) use ($mockSession) {
            $mock->shouldReceive('create')->andReturn($mockSession);
        });

        // 2. 送付先住所変更画面で住所を登録する
        $updatedAddress = [
            'postcode' => '111-2222',
            'address'  => '東京都新宿区',
            'building' => 'テストビル102',
        ];
        $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), $updatedAddress);

        // 3. 商品を購入する（ID10-1と同様の流れ）
        // 購入リクエスト
        $this->post(route('purchase.store', ['item_id' => $item->id]), ['payment_method' => 1]);

        // 決済完了コールバックのシミュレート
        $this->withSession([
            "pending_purchase_{$item->id}" => [
                'payment_method' => 1,
                'stripe_checkout_id' => 'test_id',
            ],
            // 変更した住所がセッションに残っていることを想定
            "shipping_address_{$item->id}" => $updatedAddress
        ])->get(route('purchase.success', ['item_id' => $item->id]));

        // 検証：正しく送付先住所（shipping_addressesテーブル）が紐づいているか
        $this->assertDatabaseHas('shipping_addresses', [
            'postcode' => '111-2222',
            'address'  => '東京都新宿区',
            'building' => 'テストビル102',
        ]);
    }
}
