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
                // 未認証でも、受け取ったキーワードがあれば「空の結果」として表示を維持
                return view('index', [
                    'items' => collect(),
                    'tab' => $tab,
                    'keyword' => $keyword // キーワードをViewに渡して検索窓に残す
                ]);
            }
            // ログイン中なら $query を作成
            $query = Auth::user()->likedItems();
        } else {
            // おすすめタブ
            $query = Item::query();

            // ログインしている場合、自分が出品した商品 (user_id が自分のID) を除外
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
        // 1. 指定されたIDの商品を取得。カテゴリーとコメント（ユーザー情報込）を一度に読み込む
        $item = Item::with(['categories', 'comments.user.profile'])->findOrFail($item_id);

        // 2. 詳細画面 (item_detail.blade.php) にデータを渡して表示
        return view('item_detail', compact('item'));
    }
}
