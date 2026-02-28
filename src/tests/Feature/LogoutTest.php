<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 3 ログアウトができる
     */
    public function test_user_can_logout()
    {
        // 1. テストユーザーを作成してログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. ログアウト処理（POST /logout）を実行
        $response = $this->post('/logout');

        // 3. 未認証状態になっているか確認
        $this->assertGuest();

        // 4. ログイン画面（/login）へリダイレクトされるか確認
        $response->assertRedirect('/login');
    }
}
