<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // ログインしたユーザーがまだメール認証を終えていない場合
        if (! Auth::user()->hasVerifiedEmail()) {
            // 認証誘導画面（FN012-4-b）へ強制リダイレクト
            return redirect()->route('verification.notice');
        }

        // 認証済みなら、本来の目的地（HOME）へ
        return redirect(\App\Providers\RouteServiceProvider::HOME);
    }
}
