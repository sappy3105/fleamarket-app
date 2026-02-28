<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\SoldItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListDisplayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 5-1 いいねした商品だけが表示される
     */
    public function test_only_liked_items_are_displayed_in_mylist()
    {
        // 1. 準備
        $user = User::factory()->create();
        $likedItem = Item::factory()->create(['name' => 'いいねした商品']);
        $notLikedItem = Item::factory()->create(['name' => 'いいねしていない商品']);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // 2. 実行：マイリストタブを表示
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response = $this->actingAs($user)->get(route('item.index', ['tab' => 'mylist']));

        // 3. 検証
        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * 5-2 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_show_sold_label_in_mylist()
    {
        // 1. 準備
        $user = User::factory()->create();
        $soldItem = Item::factory()->create(['name' => '売却済み商品']);

        // 2. いいね登録
        Like::create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        // 3. 購入済み状態にする（SoldItemテーブルにレコード作成）
        SoldItem::create([
            'item_id' => $soldItem->id,
            'user_id' => User::factory()->create()->id, // 自分以外の誰かが購入
            'payment_method' => 1,
            'status'  => 'paid',
        ]);

        // 4. 実行
        $response = $this->actingAs($user)->get(route('item.index', ['tab' => 'mylist']));

        // 5. 検証
        $response->assertStatus(200);
        $response->assertSee('売却済み商品');
        $response->assertSee('Sold'); // 「Sold」という文字列が含まれているか
    }

    /**
     * 5-3 未認証の場合は何も表示されない
     */
    public function test_guest_user_sees_nothing_in_mylist()
    {
        // 1. 準備：商品を作る
        $item = Item::factory()->create(['name' => '商品A']);

        // 2. 実行：ログインせずにマイリストタブへ
        $response = $this->get('/?tab=mylist');

        // 3. 検証
        $response->assertStatus(200);

        // 4. 未認証なら商品名は表示されない
        $response->assertDontSee('商品A');
    }
}
