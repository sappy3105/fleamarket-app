<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase; // これによりテストごとにメモリDBがクリアされます

    /**
     * 1-0 会員登録画面が正しく表示されるか
     */
    public function test_register_view_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * 1-1 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * 1-2 メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '', // 空にする
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * 1-3 パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '', // 空にする
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 1-4 パスワードが7文字以下の場合、バリデーションメッセージが表示される
     */
    public function test_password_minimum_length()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123', // 7文字
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * 1-5 パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password', // 一致させない
        ]);

        $response->assertSessionHasErrors(['password_confirmation' => 'パスワードと一致しません']);
    }

    /**
     * 1-6 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移する
     */
    public function test_user_can_register_successfully()
    {
        // 1. POST送信
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 2. データベースに保存されているか確認
        $this->assertDatabaseHas('users', [
            'name' => 'テスト太郎',
            'email' => 'newuser@example.com',
        ]);

        // 3. 期待挙動：プロフィール設定画面へのリダイレクト確認
        // CustomRegisterResponse で設定したパス（例: /mypage/profile）を指定してください
        $response->assertRedirect('/mypage/profile');

        // 4. ログイン状態になっていることを確認
        $this->assertAuthenticated();
    }


}
