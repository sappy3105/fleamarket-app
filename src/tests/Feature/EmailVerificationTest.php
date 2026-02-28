<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 16-1 会員登録後、認証メールが送信される
     */
    public function test_verification_email_is_sent_after_registration()
    {
        // 1. イベントをモック化（実際にメールを送らずに送信処理が走ったかを検証）
        Event::fake([Registered::class]);

        // 2. 会員登録を実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 3. 登録後にメール認証誘導画面（/email/verify）にリダイレクトされることを確認
        // ※FortifyServiceProviderの設定（RegisterResponse）に依存します
        $response->assertRedirect('/email/verify');

        // 4. メール送信（Registeredイベント）がディスパッチされたことを検証
        Event::assertDispatched(Registered::class, function ($event) {
            return $event->user->email === 'test@example.com';
        });
    }

    /**
     * 16-2 メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function test_user_can_navigate_to_mail_client_from_verify_prompt()
    {
        // 1. 未認証ユーザーを作成してログイン
        $user = User::factory()->unverified()->create();

        // 2. テスト用にURLをセット
        $expectedUrl = 'https://mailtrap.io/inboxes';
        config(['services.mail_dashboard' => $expectedUrl]);

        // 3. メール認証誘導画面を表示
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertStatus(200);
        $response->assertSee('href="' . $expectedUrl . '"', false);
        $response->assertSee('認証はこちらから');

        // 4. 画面内にMailtrapへのリンク（aタグ）が存在することを確認
        $response->assertSee('href="https://mailtrap.io/inboxes"', false);
    }

    /**
     * 16-3 メール認証を完了すると、プロフィール設定画面に遷移する
     */
    public function test_user_is_redirected_to_profile_setup_after_verification()
    {
        // 1. 未認証ユーザーを作成
        $user = User::factory()->unverified()->create();

        // 2. ユーザー用の署名付き認証URL（Fortifyが発行するものと同じ形式）を生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 3. 生成した認証URLにアクセスして認証を完了させる
        $response = $this->actingAs($user)->get($verificationUrl);

        // 4. FortifyServiceProviderで設定した VerifyEmailResponse により、
        // プロフィール設定画面（/mypage/profile）へリダイレクトされることを確認
        $response->assertRedirect(route('profile.edit')); // 実際のパスに合わせて調整してください

        // 5. ユーザーのメールが認証済み（email_verified_at に値が入っている）ことを確認
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
