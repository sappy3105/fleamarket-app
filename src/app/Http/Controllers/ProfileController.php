<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // 編集画面表示
    public function edit()
    {
        // 1. 今ログインしている人の情報を「usersテーブル」から取ってくる
        $user = Auth::user();

        // 2. その人に紐づく「profilesテーブル」の情報を取る。
        // もし初めてでプロフィールがまだ無いなら、空の入れ物(new Profile)を準備する
        $profile = $user->profile ?? new \App\Models\Profile;

        // 3. 画面(view)に、取ってきた$userと$profileのデータを渡して表示する
        return view('profile', compact('user', 'profile'));
    }

    // 保存ボタンが押された時の処理
    public function update(ProfileRequest $request)
    {
        // 1. ログイン中のユーザーを特定する
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 3. usersテーブルの「name」を、画面から送られてきた名前に書き換える
        $user->update(['name' => $request->name]);
        // 4. profilesテーブルに保存したい項目（郵便番号・住所・建物名）だけを抜き出す
        $profileData = $request->only(['postcode', 'address', 'building']);

        // 5. 画像が送られてきた場合の特別処理
        if ($request->hasFile('image_path')) {
            // storage/app/public/profiles というフォルダに画像を保存し、その名前（パス）を$pathに入れる
            $path = $request->file('image_path')->store('profiles', 'public');
            // 保存するデータリストに「画像の場所」を追加する
            $profileData['image_path'] = $path;
        }

        // 6. 仕上げ：profilesテーブルに保存する。
        // 「すでにデータがあれば上書き(update)、なければ新しく作成(create)」してくれる便利な命令
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        // 7. 前の画面に戻って「更新したよ！」と表示する
        return redirect()->route('item.index', ['tab' => 'mylist']);
    }
}
