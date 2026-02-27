<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user()->load('profile');
        $profile = $user->profile;
        $page = $request->query('page', 'sell');
        $keyword = $request->query('keyword');

        if ($page === 'buy') {
            $query = $user->purchasedItems(); // 購入した商品
        } else {
            $query = $user->items(); // 出品した商品
        }

        // キーワードがあれば商品名で絞り込む
        $query->keywordSearch($keyword);

        $items = $query->with('soldItem')->get();

        return view('mypage', compact('user', 'profile', 'items', 'page'));
    }
}
