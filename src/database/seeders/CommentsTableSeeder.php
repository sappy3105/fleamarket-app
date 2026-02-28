<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comments = [
            // パターン4: 購入者がコメ
            ['item_id' => 4,  'user_id' => 3, 'content' => 'こちらの商品はまだ購入可能でしょうか？'],

            // パターン5: 出品者以外の2人がコメ
            ['item_id' => 5,  'user_id' => 1, 'content' => 'スペックの詳細を教えてください。'],
            ['item_id' => 5,  'user_id' => 3, 'content' => '値下げは可能ですか？'],

            // パターン6: 購入者がコメ
            ['item_id' => 6,  'user_id' => 1, 'content' => '動作確認は取れていますか？'],

            // パターン7: 購入者がコメ
            ['item_id' => 7,  'user_id' => 3, 'content' => '肩紐の長さを教えてください。'],

            // パターン8: 他人がコメ
            ['item_id' => 8,  'user_id' => 1, 'content' => '保温性はどのくらいありますか？'],

            // パターン9: 出品者以外の2人がコメ
            ['item_id' => 9,  'user_id' => 1, 'content' => '素敵なミルですね。'],
            ['item_id' => 9,  'user_id' => 2, 'content' => '私も気になります。'],

            // パターン10: 購入者ともう1人がコメ
            ['item_id' => 10, 'user_id' => 1, 'content' => 'セット内容を詳しく知りたいです。'],
            ['item_id' => 10, 'user_id' => 2, 'content' => '使用頻度はどのくらいですか？'],
        ];

        foreach ($comments as $comment) {
            Comment::updateOrCreate($comment);
        }
    }
}
