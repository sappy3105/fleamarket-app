<?php

namespace App\Http\Controllers;

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

    // 出品商品の保存
    public function store(ExhibitionRequest $request)
    {
        // 1. バリデーション済みデータの取得
        $validated = $request->validated();

        // 2. 画像のアップロード処理
        $imagePath = $request->file('image_path')->store('item_images', 'public');

        // 3.商品情報のDB保存
        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'image_path' => $imagePath,
            'brand_name' => $validated['brand_name'] ?? null,
        ]);

        // 4.カテゴリーの紐付け保存
        $item->categories()->attach($validated['category_ids']);

        // 5.リダイレクト
        return redirect()->route('mypage');
    }
}
