<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                // 'id' => 1,
                'name' => 'テストユーザー1（認証済み）',
                'email' => 'test1@example.com',
                'password' => Hash::make('password'),
                'verified' => true, // 認証済みにするフラグ
            ],
            [
                // 'id' => 2,
                'name' => 'テストユーザー2（未認証・メールあり）',
                'email' => 'test2@example.com',
                'password' => Hash::make('password'),
                'verified' => false, // 未認証のままにする
                'send_mail' => true, // シーダ実行時にメールを送るフラグ
            ],
            [
                // 'id' => 3,
                'name' => 'テストユーザー3（未認証・メールなし）',
                'email' => 'test3@example.com',
                'password' => Hash::make('password'),
                'verified' => false,
                'send_mail' => false, // 何もしない（ログイン後に再送ボタンでテスト用）
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'email_verified_at' => $userData['verified'] ? Carbon::now() : null, // 認証済みフラグがtrueなら現在時刻を入れ、そうでなければNULLにする
                ]
            );

            // 未認証かつ、メール送信フラグが立っている場合のみ認証メールを送る
            if (!$userData['verified'] && ($userData['send_mail'] ?? false)) {
                $user->sendEmailVerificationNotification();
            }
        }
    }
}
