<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 9-1 ログイン済みのユーザーはコメントを送信できる
     */
    public function test_logged_in_user_can_send_comment()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. 実行：ログインしてコメントを投稿（POSTリクエスト）
        $commentData = [
            'content' => 'これはテストコメントです。',
        ];

        $response = $this->actingAs($user)
            ->post(route('comment.store', ['item_id' => $item->id]), $commentData);

        // 3. 検証：データベースに保存されているか
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'これはテストコメントです。',
        ]);

        // 4. 検証：投稿後、詳細ページにリダイレクトされ、コメントが表示されているか
        $response = $this->actingAs($user)->get(route('item.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('これはテストコメントです。');

        // 5. コメントカウントが増えていることを確認
        $response->assertSee('コメント(1)');
    }

    /**
     * 9-2 ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_send_comment()
    {
        // 1. 準備：商品のみ作成（ユーザーはログインさせない）
        $item = Item::factory()->create();

        $commentData = [
            'content' => 'ログイン前ユーザーのコメントです。',
        ];

        // 2. 実行：未ログイン状態でコメント投稿（POSTリクエスト）
        $response = $this->post(route('comment.store', ['item_id' => $item->id]), $commentData);

        // 3. 検証：ログイン画面へリダイレクトされること
        $response->assertRedirect(route('login'));

        // 4. 検証：データベースにコメントが保存されていないこと
        $this->assertDatabaseMissing('comments', [
            'content' => 'ログイン前ユーザーのコメントです。',
        ]);
    }

    /**
     * ID: 9-3 コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_is_required()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. 実行：空のコメントを送信（POSTリクエスト）
        $response = $this->actingAs($user)
            ->from(route('item.show', ['item_id' => $item->id]))
            ->post(route('comment.store', ['item_id' => $item->id]), [
                'content' => '', // 未入力
            ]);

        // 3. 検証：バリデーションエラーが発生し、元のページにリダイレクトされるか
        $response->assertStatus(302);
        $response->assertRedirect(route('item.show', ['item_id' => $item->id]));

        // 4. 検証：エラーメッセージがセッションに保持されているか
        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください',
        ]);

        // 5. 検証：データベースに保存されていないこと
        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * 9-4 コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_max_length_validation()
    {
        // 1. 準備：ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. 実行：256文字のコメントを生成して送信（POSTリクエスト）
        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->from(route('item.show', ['item_id' => $item->id]))
            ->post(route('comment.store', ['item_id' => $item->id]), [
                'content' => $longComment,
            ]);

        // 3. 検証：バリデーションエラーによるリダイレクト
        $response->assertStatus(302);
        $response->assertRedirect(route('item.show', ['item_id' => $item->id]));

        // 4. 検証：エラーメッセージがセッションに保持されているか
        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください',
        ]);

        // 5. 検証：データベースに保存されていないこと
        $this->assertDatabaseMissing('comments', [
            'content' => $longComment,
        ]);
    }
}
