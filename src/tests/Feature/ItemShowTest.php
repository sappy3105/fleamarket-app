<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * 4-1 全商品を取得できる
     */
    public function test_can_get_all_items()
    {
        // ① 準備：商品を10個ランダムに作成する
        Item::factory()->count(10)->create();

        // ② 実行：トップページ（商品一覧）にアクセスする
        $response = $this->get('/');

        // ③ 検証：画面が正常に開いたか？（200 OKか）
        $response->assertStatus(200);

        // ④ 検証：ビュー（Blade）に 'items' という変数が送られているか？
        $response->assertViewHas('items');

        // ⑤ 証拠の特定：DBにある1番目の商品データを取得する
        $item = Item::first();

        // ⑥ 決定的な証拠：その商品の名前が、画面のHTMLの中に書き込まれているか？
        $response->assertSee($item->name);
    }
}
