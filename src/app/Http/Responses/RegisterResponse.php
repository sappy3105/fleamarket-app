<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // もしユーザーがまだメール認証を終えていないなら
        if (! Auth::user()->hasVerifiedEmail()) {
            // 誘導画面（verify-email）を表示するルートへ飛ばす
            return redirect()->route('verification.notice');
        }

        // すでに認証済み（通常はあり得ませんが）ならプロフィールへ
        return redirect()->route('profile.edit');
    }
}
