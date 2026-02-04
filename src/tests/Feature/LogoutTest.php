<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 3 ログアウトができる
     */
    public function test_user_can_logout()
    {
        // 1. まずテストユーザーを作成してログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. ログアウト処理（POST /logout）を実行
        // Fortifyのデフォルト設定では POST 送信が必要です
        $response = $this->post('/logout');

        // 3. 未認証状態になっているか確認
        $this->assertGuest();

        // 4. 期待挙動：ログイン画面（/login）へリダイレクトされるか確認
        // FortifyServiceProvider で設定した LogoutResponse に従います
        $response->assertRedirect('/login');
    }
}
