<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\Profile;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID: 7-1 全ての商品詳細情報が表示される
     */
    public function test_can_view_all_item_details()
    {
        // 1. ストレージのフェイクを宣言
        Storage::fake('public');

        // 2. データの準備
        $category = Category::factory()->create(['name' => 'テストカテゴリ']);

        // 3. プロフィール付きのユーザーを作成
        $commentUser = User::factory()->create(['name' => 'コメントした人']);
        $imagePath = 'profiles/profile.png';
        Profile::factory()->create([
            'user_id'    => $commentUser->id,
            'image_path' => $imagePath,
        ]);

        // 4. フェイクストレージに空のファイルを置く
        Storage::disk('public')->put($imagePath, 'dummy content');

        // 5. 商品の作成
        $item = Item::factory()->create([
            'name' => 'テスト商品名',
            'brand_name' => 'テストブランド',
            'price' => 2500,
            'description' => 'テスト用の商品説明文です。',
            'condition' => 2, // 2: 目立った傷や汚れなし
            'image_path' => 'item_images/item_image.png',
        ]);

        // 6. リレーションの作成
        $item->categories()->attach($category->id);

        // 7. Factoryを使って「いいね」と「コメント」を作成
        Like::factory()->count(2)->create(['item_id' => $item->id]);
        Comment::factory()->count(1)->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => 'これはテストコメントです',
        ]);

        // 8. 実行：詳細ページへアクセス
        $response = $this->get(route('item.show', ['item_id' => $item->id]));

        // 9. 検証（全12項目）
        $response->assertStatus(200);

        // 項目1: 商品画像
        $response->assertSee('item_images/item_image.png');

        // 項目2: 商品名
        $response->assertSee('テスト商品名');

        // 項目3: ブランド名
        $response->assertSee('テストブランド');

        // 項目4: 価格 (カンマ区切りと税込表記)
        $response->assertSee('¥2,500');
        $response->assertSee('(税込)');

        // 項目5&6: いいね数2とコメント数1を確認
        $response->assertSeeInOrder([
            'heartlogo_default.png', // いいねアイコン
            '2',                     // いいね数（2つ）
            'comment.png',           // コメントアイコン
            '1',                     // コメント数（1つ）
        ]);

        // 項目7: 商品説明
        $response->assertSee('テスト用の商品説明文です。');

        // 項目8: カテゴリ名
        $response->assertSee('テストカテゴリ');

        // 項目9: 商品の状態
        $response->assertSee('目立った傷や汚れなし');

        // 項目10: コメント数
        $response->assertSee('コメント(1)');

        // 項目11: コメントしたユーザー情報 (名前と画像)
        $response->assertSee('コメントした人');
        $response->assertSee('profiles/profile.png');

        // 項目12: コメント内容
        $response->assertSee('これはテストコメントです');
    }

    /**
     * 7-2 複数選択されたカテゴリが商品詳細ページに表示されている
     */
    public function test_multiple_categories_are_displayed_on_item_detail_page()
    {
        // カテゴリを3つ作成
        $categories = Category::factory()->count(3)->create();

        // 商品を作成し、同時に作成したカテゴリを紐付ける
        $item = Item::factory()
            ->hasAttached($categories)
            ->create();

        $response = $this->get(route('item.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
