<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $keyword = $request->query('keyword');

        // おすすめかマイリストか分ける
        if ($tab === 'mylist') {
            if (!Auth::check()) {
                return view('index', [
                    'items' => collect(),
                    'tab' => $tab,
                    'keyword' => $keyword,
                ]);
            }
            $query = Auth::user()->likedItems();
        } else {
            // おすすめタブ
            $query = Item::query();

            // ログインしている場合、自分が出品した商品を除外
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

        // scopeKeywordSearch で絞り込む
        if (!empty($keyword)) {
            $query->keywordSearch($keyword);
        }

        // データを取得
        $items = $query->with('soldItem')->get();

        return view('index', compact('items', 'tab'));
    }

    public function show($item_id)
    {
        // 1. 指定されたIDの商品を取得。カテゴリーとコメントを一度に読み込む
        $item = Item::with(['categories', 'comments.user.profile'])->findOrFail($item_id);

        // 2. 詳細画面 (item_detail.blade.php) にデータを渡して表示
        return view('item_detail', compact('item'));
    }
}
