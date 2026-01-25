<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;

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

//検索機能
// Route::get('/search', [ItemController::class, 'search'])->name('item.search');

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
});
