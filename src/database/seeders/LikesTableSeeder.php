<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Like;

class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $likes = [
            ['item_id' => 2,  'user_id' => 1], // パターン2: 出品者が自分の出品物にいいね
            ['item_id' => 3,  'user_id' => 2], // パターン3: 購入者ともう1人がいいね
            ['item_id' => 3,  'user_id' => 3], // パターン3: 他人がいいね
            ['item_id' => 6,  'user_id' => 1], // パターン6: 購入者がいいね
            ['item_id' => 7,  'user_id' => 2], // パターン7: 出品者がいいね
            ['item_id' => 8,  'user_id' => 1], // パターン8: 出品者以外の2人がいいね
            ['item_id' => 8,  'user_id' => 2], // パターン8: 出品者以外の2人がいいね
            ['item_id' => 9,  'user_id' => 2], // パターン9: 出品者・購入者以外がいいね
            ['item_id' => 10, 'user_id' => 1], // パターン10: 他人がいいね
            ['item_id' => 10, 'user_id' => 2], // パターン10: 購入者がいいね
        ];

        foreach ($likes as $like) {
            Like::updateOrCreate($like);
        }
    }
}
