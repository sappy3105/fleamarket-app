<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LikeController extends Controller
{
    // いいね登録
    public function store($item_id)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->likedItems()->syncWithoutDetaching([$item_id]);
        return back();
    }

    // いいね解除
    public function destroy($item_id)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->likedItems()->detach($item_id);
        return back();
    }
}
