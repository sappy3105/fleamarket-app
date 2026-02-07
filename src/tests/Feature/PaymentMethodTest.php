<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 11 支払い方法選択機能
     * 小計画面で変更が反映される
     */
    public function test_payment_method_selection_is_reflected_correctly()
    {
        // --- 準備 ---
        $user = User::factory()->hasProfile()->create();
        $item = Item::factory()->create(['price' => 5000]);

        // 手順1. 支払い方法選択画面（購入画面）を開く
        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        // 初期状態：右側の小計テーブルの「支払い方法」の次に「選択してください」が来るか
        // プルダウン側の「選択してください」との混同を防ぐため
        $response->assertSeeInOrder(['支払い方法', '選択してください']);

        // 手順2. プルダウンメニューから支払い方法を選択する
        // ※Feature TestではJSを動かせないため、JSによって「選択・保存された後」の状態をシミュレートします

        // ケースA: コンビニ払い(1) を選択した場合
        $response = $this->actingAs($user)
            ->withSession(["payment_method_{$item->id}" => 1]) // 選択された値をセッションにセット
            ->get(route('purchase.show', ['item_id' => $item->id]));

        // 検証：小計画面への反映
        $response->assertSeeInOrder(['支払い方法', 'コンビニ払い']);

        // ケースB: カード払い(2) を選択した場合
        $response = $this->actingAs($user)
            ->withSession(["payment_method_{$item->id}" => 2])
            ->get(route('purchase.show', ['item_id' => $item->id]));

        // 検証：小計画面への反映
        $response->assertSeeInOrder(['支払い方法', 'カード払い']);
    }
}
