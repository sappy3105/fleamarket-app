<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ExhibitionController extends Controller
{
    public function create()
    {
        // 出品画面の表示
        $categories = Category::all();
        return view('exhibition', compact('categories'));
    }

    // 【新規】商品出品の保存処理
    public function store(ExhibitionRequest $request)
    {
        // 【1】画像のアップロード処理（ProfileControllerと同じ書き方に統一）
        // 第2引数に 'public' を指定することで、 storage/app/public/item_images に保存され、
        // 戻り値 $imagePath には自動的に 'item_images/ハッシュ値.jpg' が入ります。
        $imagePath = $request->file('image_path')->store('item_images', 'public');


        // 【2】商品情報のDB保存
        // createメソッドを使って itemsテーブルにデータを保存します。
        $item = Item::create([
            // Auth::id()で現在ログインしているユーザーのIDを取得して保存
            'user_id' => Auth::id(),

            // フォームから送信された商品名
            'name' => $request->name,

            // フォームから送信された商品説明
            'description' => $request->description,

            // フォームから送信された価格
            'price' => $request->price,

            // フォームから送信された商品の状態（1〜4の数値）
            'condition' => $request->condition,

            // 先ほど生成した画像のパス
            'image_path' => $imagePath,

            // フォームから送信されたブランド名（nullableなので空の場合もあり）
            'brand_name' => $request->brand_name,
        ]);


        // 【4】カテゴリーの紐付け保存（中間テーブルへの保存）
        // category_ids は配列で送られてきます（例: [1, 3]）。
        // attachメソッドを使うと、category_itemsテーブルに「この商品IDと、カテゴリーID 1」「この商品IDと、カテゴリーID 3」というデータを自動で作成してくれます。
        $item->categories()->attach($request->category_ids);


        // 【5】リダイレクト
        // 保存が完了したら、トップページ（商品一覧）や出品完了画面などへ遷移させます。
        // ここではルート名 'item.index'（商品一覧）に戻るとします。
        return redirect()->route('item.index');
    }
}
