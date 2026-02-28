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
        // $validated = $request->validated();

        // 2. usersテーブルの更新
        $user->update(['name' => $request->name]);

        // 3. profilesテーブル用の準備
        $profileData = [
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
        ];

        // 4. 画像処理
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('profiles', 'public');
            $profileData['image_path'] = $path;
        } elseif ($request->filled('image_preview_data')) {
            // ★追加: ファイルはないが、バリデーションエラー復元用のBase64データがある場合
            $base64Image = $request->input('image_preview_data');

            // Base64文字列からデータを抽出して保存する
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $data = base64_decode($data);

                $extension = strtolower($type[1]); // png, jpg etc
                $fileName = 'profiles/' . uniqid() . '.' . $extension;

                // Storageに保存
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $data);
                $profileData['image_path'] = $fileName;
            }
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
