<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // 編集画面表示
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. profilesテーブルの情報を取得
        // もし初めてでプロフィールがまだ無いなら、空の入れ物(new Profile)を準備する
        $profile = $user->profile ?? new \App\Models\Profile;

        // 2. viewに、取得した$userと$profileのデータを渡して表示する
        return view('profile', compact('user', 'profile'));
    }

    // 保存ボタンが押された時の処理
    public function update(ProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. バリデーション済みデータを取得
        $validated = $request->validated();

        // 2. usersテーブルの更新
        $user->update(['name' => $validated['name']]);

        // 3. profilesテーブル用の準備
        $profileData = [
            'postcode' => $validated['postcode'],
            'address'  => $validated['address'],
            'building' => $validated['building'] ?? null,
        ];

        // 4. 画像処理
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('profiles', 'public');
            $profileData['image_path'] = $path;
        }

        // 5. 保存
        // すでにデータがあれば上書き(update)、なければ新しく作成(create)
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // 6. 前の画面に戻る
        return redirect()->route('item.index', ['tab' => 'mylist']);
    }
}
