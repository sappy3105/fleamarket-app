<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. 外部キー制約を一時的に無効化（関連があるテーブルも強制的に操作可能にする）
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. データを空にするテーブルを指定
        DB::table('users')->truncate();
        DB::table('categories')->truncate();
        DB::table('items')->truncate();
        // 今後テーブルが増えたらここに追記していく

        // 3. 外部キー制約を元に戻す
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. 各シーダーを実行してデータを注入
        $this->call([
            UserSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class, // 追加
        ]);
    }
}
