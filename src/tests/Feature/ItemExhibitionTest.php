<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 15 出品商品情報登録
     * 商品出品画面にて必要な情報が保存できること
     */
    public function test_user_can_list_item_with_categories_and_it_is_saved_correctly()
    {
        // 1. 準備
        Storage::fake('public'); // ファイルアップロードを擬似化
        $user = User::factory()->hasProfile()->create();

        // テスト用のカテゴリーを3つ作成
        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        // 送信データ（Bladeのname属性に合わせる）
        $exhibitionData = [
            'image_path' => UploadedFile::fake()->create('test_item.png', 100),
            'category_ids' => $categoryIds, // 配列形式
            'condition'    => 1,            // 1:良好
            'name'         => 'テスト商品名',
            'brand_name'   => 'テストブランド',
            'description'  => 'テスト商品の説明文です。',
            'price'        => 12000,
        ];

        // 2. 実行
        // 出品画面の表示確認
        $this->actingAs($user)
            ->get(route('exhibition.create'))
            ->assertStatus(200);

        // 出品リクエスト
        $response = $this->post(route('exhibition.store'), $exhibitionData);

        // 保存後のリダイレクト先（実装に合わせて /mypage 等に変更してください）
        $response->assertRedirect(route('mypage'));

        // 3. 検証
        // 3-1. itemsテーブルにデータが保存されているか
        $this->assertDatabaseHas('items', [
            'user_id'    => $user->id,
            'name'       => 'テスト商品名',
            'brand_name' => 'テストブランド',
            'condition'  => 1,
            'price'      => 12000,
        ]);

        // 保存されたアイテムを取得
        $item = Item::where('name', 'テスト商品名')->first();

        // 3-2. 画像の保存検証
        // image_path カラムが空（null）でないことを確認
        $this->assertNotNull($item->image_path);

        // 実際にファイルがストレージに存在するか確認
        Storage::disk('public')->assertExists($item->image_path);

        // 3-3. 中間テーブル (category_item) の検証
        foreach ($categoryIds as $id) {
            $this->assertDatabaseHas('category_item', [
                'item_id'     => $item->id,
                'category_id' => $id,
            ]);
        }

        // 実際に紐づいているレコード数をチェック
        $this->assertEquals(3, $item->categories()->count());
    }
}
