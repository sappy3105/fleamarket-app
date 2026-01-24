<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Authを使うために必要
use App\Models\Item;                // Itemモデルを使うために必要

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');

        if ($tab === 'mylist') {
            // マイリスト：ログイン中ならお気に入り商品を取得（機能未実装なら空配列）
            $items = Auth::check()
                ? Auth::user()->favoriteItems()->get()
                : collect();
        } else {
            // おすすめ：自分以外の全商品、または全商品（未ログイン含む）
            $items = Item::all();
        }

        return view('index', compact('items', 'tab'));
    }

    public function show($item_id)
    {
        // 1. 指定されたIDの商品を取得。カテゴリーとコメント（ユーザー情報込）を一度に読み込む
        $item = Item::with(['categories', 'comments.user.profile'])->findOrFail($item_id);

        // 2. 詳細画面 (item_detail.blade.php) にデータを渡して表示
        return view('item_detail', compact('item'));
    }

    public function mypage()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // ユーザー自身が出品した商品を取得
        $items = Item::where('user_id', $user->id)->get();

        return view('mypage', compact('user', 'profile', 'items'));
    }
}
