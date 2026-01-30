<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $page = $request->query('page', 'sell');

        if ($page === 'buy') {
            $items = $user->purchasedItems; // 購入した商品
        } else {
            $items = Item::where('user_id', $user->id)->get(); // 出品した商品
        }

        return view('mypage', compact('user', 'profile', 'items', 'page'));
    }

}
