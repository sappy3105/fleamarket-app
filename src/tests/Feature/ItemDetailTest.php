<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID: 7-1 全ての商品詳細情報が表示される
     */
    public function test_can_view_all_item_details()
    {
        // 1. データの準備
        // カテゴリとユーザー（コメント主）を作成
        $category = Category::factory()->create(['name' => 'テストカテゴリ']);
        $commentUser = User::factory()->create(['name' => 'コメントした人']);

        // プロフィール画像の設定（項目11の検証用）
        $commentUser->profile()->create(['image_path' => 'profile.png']);

        // 商品の作成
        $item = Item::factory()->create([
            'name' => 'テスト商品名',
            'brand_name' => 'テストブランド',
            'price' => 2500,
            'description' => 'テスト用の商品説明文です。',
            'condition' => 2, // 2: 目立った傷や汚れなし
            'image_path' => 'item_image.png',
        ]);

        // リレーションの作成
        $item->categories()->attach($category->id);

        // コメントの作成
        $item->comments()->create([
            'user_id' => $commentUser->id,
            'content' => 'これはテストコメントです',
        ]);

        // 2. 実行：詳細ページへアクセス
        $response = $this->get(route('item.show', ['item_id' => $item->id]));

        // 3. 検証（全12項目）
        $response->assertStatus(200);

        // 項目1: 商品画像 (imgタグのsrcに含まれているか)
        $response->assertSee('item_image.png');

        // 項目2: 商品名
        $response->assertSee('テスト商品名');

        // 項目3: ブランド名
        $response->assertSee('テストブランド');

        // 項目4: 価格 (カンマ区切りと税込表記)
        $response->assertSee('¥2,500');
        $response->assertSee('(税込)');

        // 項目5: いいね数 (初期値0を確認)
        $response->assertSee('<span class="icon-count">0</span>', false);

        // 項目6 & 10: コメント数
        $response->assertSee('コメント(1)');

        // 項目7: 商品説明
        $response->assertSee('テスト用の商品説明文です。');

        // 項目8: カテゴリ名
        $response->assertSee('テストカテゴリ');

        // 項目9: 商品の状態 (@switchの判定結果)
        $response->assertSee('目立った傷や汚れなし');

        // 項目11: コメントしたユーザー情報 (名前と画像)
        $response->assertSee('コメントした人');
        $response->assertSee('profile.png');

        // 項目12: コメント内容
        $response->assertSee('これはテストコメントです');
    }
}
