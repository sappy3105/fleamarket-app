<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // ここで新規登録後のリダイレクト先を決定
        return redirect()->route('profile.edit');
    }
}
