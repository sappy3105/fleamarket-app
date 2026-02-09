<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    public function toResponse($request)
    {
        // 認証完了後、プロフィール設定画面（profile.edit）へリダイレクト
        return redirect()->route('profile.edit');
    }
}
