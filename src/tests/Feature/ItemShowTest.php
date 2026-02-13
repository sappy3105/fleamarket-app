<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
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

    /**
     * 4-2 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_display_sold_label()
    {
        // 1. 商品を2つ作成
        $soldItem = Item::factory()->create(['name' => '売却済みの商品名']);
        $availableItem = Item::factory()->create(['name' => '販売中の商品名']);

        // 2. 「売却済みの商品名」に対して、SoldItemレコードを作成（これで売れた状態になる）
        SoldItem::factory()->create([
            'item_id' => $soldItem->id,
            'status'  => 'paid',
        ]);

        // 3. 一覧ページにアクセス
        $response = $this->get('/');

        // 4. 検証
        $response->assertStatus(200);

        $response->assertSee('売却済みの商品名');

        // 売れた商品の名前に「Sold」が表示されているか
        $response->assertSeeInOrder(['売却済みの商品名', 'Sold']);

        // 販売中の商品の名前は見える
        $response->assertSee('販売中の商品名');

        // 画面全体のどこにも「販売中の商品名Sold」という塊が存在しないことを確認
        // (Blade側で隣接して書いている場合、この文字列で検索できます)
        $response->assertDontSee('販売中の商品名Sold');
    }

    /**
     * 4-3 自分が出品した商品は表示されない
     */
    public function test_cannot_see_own_items_on_index()
    {
        // 1. ログインユーザー（自分）を作成
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. 自分が出品した商品を作成
        $myItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'これは私の出品物です',
        ]);

        // 3. 他人が出品した商品を作成
        $othersItem = Item::factory()->create([
            'name' => 'これは他人の商品です',
        ]);

        // 4. トップページにアクセス
        $response = $this->get('/');

        // 5. 検証
        $response->assertStatus(200);
        $response->assertSee('これは他人の商品です');      // 他人のは見える
        $response->assertDontSee('これは私の出品物です'); // 自分のは見えない
    }
}
