<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

use App\Http\Requests\LoginRequest as MyLoginRequest; // 自作のログインリクエスト
use App\Http\Requests\RegisterRequest as MyRegisterRequest;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Http\Requests\RegisterRequest as FortifyRegisterRequest;
// use Laravel\Fortify\Contracts\RegisterRequest as FortifyRegisterRequest;

use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

use App\Http\Responses\LoginResponse;
use App\Http\Responses\LogoutResponse;
use App\Http\Responses\RegisterResponse;
use App\Http\Responses\VerifyEmailResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ログインリクエストの差し替え
        $this->app->singleton(FortifyLoginRequest::class, MyLoginRequest::class);

        // 新規登録リクエストの差し替え
        $this->app->singleton(FortifyRegisterRequest::class, MyRegisterRequest::class);

        // 新規登録後のレスポンス（メール認証誘導画面へ飛ばす設定）
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);

        // ログイン後のレスポンス（未認証なら誘導画面、済みならHOMEへ）
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        // メール認証完了後のレスポンス（プロフィール編集画面へ）
        $this->app->singleton(VerifyEmailResponseContract::class, VerifyEmailResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ユーザー作成
        Fortify::createUsersUsing(CreateNewUser::class);

        // 各種ビューの設定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        //メール認証機能
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        // ログイン制限（RateLimiter）
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

        // ログアウト後の遷移先
        $this->app->instance(LogoutResponseContract::class, new class implements LogoutResponseContract {
            public function toResponse($request)
            {
                return redirect('/login');
            }
        });


    }
}
