<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 6-1 商品名で部分一致検索ができる
     */
    public function test_can_search_items_by_name()
    {
        // 1. 準備：対象となる商品と対象外の商品を作成
        Item::factory()->create(['name' => '黒色のキーボード']);
        Item::factory()->create(['name' => '白色のマウス']);

        // 2. 実行：検索クエリを投げる
        $response = $this->get('/?keyword=黒色');

        // 3. 検証
        $response->assertStatus(200);
        $response->assertSee('黒色のキーボード');
        $response->assertDontSee('白色のマウス');
    }

    /**
     * ID: 6-2 検索キーワードがマイリストタブのリンクにも保持されている
     */
    public function test_search_keyword_is_kept_in_mylist_tab_link()
    {
        // 1. 適当な検索キーワードを決める
        $keyword = '魔法';

        // 2. キーワード付きでトップページ（おすすめタブ）にアクセス
        $response = $this->get('/?keyword=' . urlencode($keyword));

        $response->assertStatus(200);

        // 3. 検証
        $expectedUrl = str_replace('&', '&amp;', route('item.index', [
            'tab' => 'mylist',
            'keyword' => $keyword
        ]));

        // href属性として存在するかチェック
        $response->assertSee('href="' . $expectedUrl . '"', false);
    }
}
