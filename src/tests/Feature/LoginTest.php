<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 2-0: ログイン画面が正しく表示されるか
     */

    public function test_register_view_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * 2-1: メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * 2-2: パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 2-3: 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrong-password',
        ]);

        // Fortifyのデフォルトメッセージ、またはLoginRequestで設定したメッセージを確認
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    /**
     * 2-4: 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_user_can_login_with_correct_credentials()
    {
        // テスト用のユーザーを作成
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // ログイン状態の確認
        $this->assertAuthenticatedAs($user);

        // 指定のリダイレクト先へ遷移しているか
        $response->assertRedirect('/?tab=mylist');
    }
}
