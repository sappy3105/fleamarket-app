<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 8-1 いいねアイコンを押下して、正しく登録されカウントが増加する
     */
    public function test_user_can_like_an_item_and_count_increments()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. ログインして商品詳細ページを開く（初期状態の確認）
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        // 最初はカウントが 0 であることを確認
        $response->assertSeeInOrder([
            'heartlogo_default.png', // いいねアイコン
            '0',                     // いいね数（0つ）
        ]);

        // 3. アクション：いいね登録フォームの送信（POSTリクエスト）
        $response = $this->actingAs($user)->post(route('like.store', $item->id));

        // 4. 検証：データベースに登録されているか
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 5. 検証：詳細ページを再度開き、カウントが「1」に増えているか
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSeeInOrder([
            'heartlogo_pink.png', // いいねアイコン
            '1',                     // いいね数（1つ）
        ]);
    }

    /**
     * 8-2 追加済みのアイコンは色が変化する
     */
    public function test_already_liked_icon_is_pink()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. あらかじめ「いいね」を登録する
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 3. 実行：ログインして商品詳細ページを開く
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));

        // 4. 検証：最初からアイコンがピンク（追加済み状態）であることを確認
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'heartlogo_pink.png',
            '1',
        ]);
    }

    public function test_user_can_unlike_an_item_and_count_decrements()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. あらかじめ「いいね」を登録しておく
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 3. ログインして商品詳細ページを開く（初期状態の確認）
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        // 4. 最初はピンクのアイコンで、カウントが 1 であることを確認
        $response->assertSeeInOrder([
            'heartlogo_pink.png', // いいねアイコン（ピンク）
            '1',                     // いいね数（1つ）
        ]);

        // 5. アクション：いいね解除フォームの送信（POSTリクエスト）
        $response = $this->actingAs($user)->delete(route('like.destroy', $item->id));

        // 6. DBから実際にレコードが消えているかを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 7. 検証：詳細ページを再度開き、デフォルトアイコンで、カウントが「0」に減っているか
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSeeInOrder([
            'heartlogo_default.png', // いいねアイコン（デフォルト）
            '0',                     // いいね数（0つ）
        ]);
    }
}
