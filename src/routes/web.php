<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 商品一覧（おすすめ・マイリスト共通）
Route::get('/', [ItemController::class, 'index'])->name('item.index');

// 商品詳細（未認証でも閲覧可能）
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// ログイン済みの会員のみプロフィール編集画面へアクセス
Route::middleware('auth')->group(function () {
    // マイページ（プロフィールと自分の商品一覧）
    Route::get('/mypage', [ItemController::class, 'mypage'])->name('mypage');

    //プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    //いいねの保存・解除
    Route::post('/item/{item_id}/like', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/item/{item_id}/like', [LikeController::class, 'destroy'])->name('like.destroy');

    //コメント機能
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])
        ->name('comment.store');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchase'])->name('purchase.show');

    // 住所変更画面の表示
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');

    // 住所変更の保存（セッションへ）
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 購入処理（Stripe決済へ）
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'storePurchase'])->name('purchase.store');

    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'successPurchase'])->name('purchase.success');
});
