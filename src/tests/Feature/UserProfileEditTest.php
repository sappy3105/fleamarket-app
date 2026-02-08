<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 14 ユーザー情報変更
     * 変更項目が初期値として過去設定されていること
     * （プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function test_profile_edit_page_shows_current_user_data_as_initial_values()
    {
        // 1. 準備：詳細な情報を持つユーザーとプロフィールを作成
        $user = User::factory()
            ->hasProfile([
                'image_path' => 'profiles/initial_image.png',
                'postcode'   => '123-4567',
                'address'    => '大阪府大阪市',
                'building'   => 'テストビル101'
            ])
            ->create(['name' => '既存ユーザー名']);

        // 2. 実行：ログインしてプロフィール編集画面を開く
        $response = $this->actingAs($user)->get(route('profile.edit'));

        // 3. 検証
        $response->assertStatus(200);

        // 各項目が存在するか
        $response->assertSee('既存ユーザー名');
        $response->assertSee('profiles/initial_image.png');
        $response->assertSee('123-4567');
        $response->assertSee('大阪府大阪市');
        $response->assertSee('テストビル101');
    }
}
