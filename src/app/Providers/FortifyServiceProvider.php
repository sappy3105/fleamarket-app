<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\LoginRequest as MyLoginRequest; // 自作のログインリクエスト
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse; // 新しくResponses/RegisterResponse.phpを作成
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;



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
        $this->app->afterResolving(RegisterRequest::class, function ($request, $app) {
            // ここは空でも、型を解決させることでLaravelに認識させます
        });

        // 新規登録後の遷移先をカスタムクラスに紐付け
        $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
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

        // ログイン制限（RateLimiter）
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

        // ログイン後の遷移先を HOME 定数に従わせる（または直接パスを書く）
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                // RouteServiceProvider::HOME の値（/?tab=mylist）へリダイレクト
                return redirect(\App\Providers\RouteServiceProvider::HOME);
            }
        });

        // ログアウト後の遷移先
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect('/login');
            }
        });
    }
}
