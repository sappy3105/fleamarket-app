<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 13 ユーザー情報取得
     * プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
     */
    public function test_user_can_see_profile_info_and_item_lists()
    {
        // 1. 準備：ユーザーとプロフィールを作成
        $user = User::factory()
            ->hasProfile(['image_path' => 'profiles/test_user.png'])
            ->create(['name' => 'テスト太郎']);

        // 2. 準備：出品した商品を作成
        $exhibitionItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '私が出品した商品'
        ]);

        // 3. 準備：購入した商品を作成
        $boughtItem = Item::factory()->create(['name' => '私が購入した商品']);
        // 実際に購入された状態（SoldItem）をDBに作成して紐づける
        SoldItem::create([
            'item_id' => $boughtItem->id,
            'user_id' => $user->id,
            'payment_method' => 1,
        ]);

        // 4. 実行：ログインしてプロフィールページを開く
        $response = $this->actingAs($user)->get(route('mypage'));

        // 5. 検証
        $response->assertStatus(200);

        // ユーザー情報と画像パスの確認
        $response->assertSee('テスト太郎');
        $response->assertSee('profiles/test_user.png');

        // 出品した商品が表示されているか
        $response->assertSee('私が出品した商品');

        // 購入した商品一覧（タブを切り替えた状態）を確認
        // ID 10-3と同様に ?page=buy にアクセスして確認
        $responseBuy = $this->actingAs($user)->get(route('mypage', ['page' => 'buy']));
        $responseBuy->assertStatus(200);
        $responseBuy->assertSee('私が購入した商品');
    }
}
