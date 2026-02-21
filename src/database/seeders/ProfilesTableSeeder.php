<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. テストユーザー1を取得する
        $user1 = User::where('email', 'test1@example.com')->first();

        // 2. ユーザーが存在し、かつまだプロフィールを持っていない場合のみ作成（または更新）
        if ($user1) {
            // updateOrCreateを使って、何度実行しても1つだけに保つ
            // Factoryのraw()メソッドを使うと、定義済みのダミーデータ配列を取得できます
            Profile::updateOrCreate(
                ['user_id' => $user1->id],
                [
                    'image_path' => 'profiles/sample_user.png', // 固定にしたい場合
                    'postcode'   => '123-4567',
                    'address'    => '東京都てすと区',
                    'building'   => 'テストビル101',
                ]
            );
        }
    }
}
