<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_name' => 'watch.jpg',
                'condition' => 1, // 良好
                'brand_name' => 'Rolax',
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_name' => 'hdd.jpg',
                'condition' => 2, // 目立った傷や汚れなし
                'brand_name' => '西芝',
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_name' => 'onion.jpg',
                'condition' => 3, // やや傷や汚れあり
                'brand_name' => 'なし',
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_name' => 'leather_shoes.jpg',
                'condition' => 4, // 状態が悪い
                'brand_name' => '',
            ],
            [
                'user_id' => 2,
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_name' => 'laptop.jpg',
                'condition' => 1, // 良好
                'brand_name' => '',
            ],
            [
                'user_id' => 2,
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_name' => 'music_mic.jpg',
                'condition' => 2, // 目立った傷や汚れなし
                'brand_name' => 'なし',
            ],
            [
                'user_id' => 2,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_name' => 'shoulder_bag.jpg',
                'condition' => 3, // やや傷や汚れあり
                'brand_name' => '',
            ],
            [
                'user_id' => 3,
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image_name' => 'tumbler.jpg',
                'condition' => 4, // 状態が悪い
                'brand_name' => 'なし',
            ],
            [
                'user_id' => 3,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image_name' => 'coffee_grinder.jpg',
                'condition' => 1, // 良好
                'brand_name' => 'Starbacks',
            ],
            [
                'user_id' => 3,
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_name' => 'makeup_set.jpg',
                'condition' => 2, // 目立った傷や汚れなし
                'brand_name' => '',
            ],
        ];

        foreach ($items as $itemData) {
            $fileName = $itemData['image_name'];
            $storagePath = 'item_images/' . $fileName;

            // 1. database/seeders/images から storage/app/public/item_images へコピー
            $seedImagePath = database_path('seeders/images/' . $fileName);
            if (File::exists($seedImagePath)) {
                Storage::disk('public')->put($storagePath, File::get($seedImagePath));
            }

            // 2. DB保存
            Item::updateOrCreate(
                [
                    'user_id' => $itemData['user_id'],
                    'name'    => $itemData['name']
                ],
                [
                    'price'       => $itemData['price'],
                    'description' => $itemData['description'],
                    'image_path'  => $storagePath,
                    'condition'   => $itemData['condition'],
                    'brand_name'  => $itemData['brand_name'],
                ]
            );
        }
    }
}
