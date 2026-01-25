<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Authを使うために必要
use App\Models\Item;                // Itemモデルを使うために必要

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $keyword = $request->query('keyword');

        // おすすめかマイリストか分ける
        if ($tab === 'mylist') {
            //未認証だったら、ログイン画面へ
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // ログイン中ならマイリストへ
            $query = Auth::user()->favoriteItems();
        } else {
            // おすすめタブ
            $query = Item::query();
        }

        // scopeKeywordSearch で絞り込む
        if (!empty($keyword)) {
            $query->keywordSearch($keyword);
        }

        // データを取得
        $items = $query->get();
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
