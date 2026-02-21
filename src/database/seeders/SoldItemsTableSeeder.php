<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoldItem;

class SoldItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $soldItems = [
            // テストユーザー1 (user_id: 1)
            [
                'item_id' => 9,
                'user_id' => 1,
                'payment_method' => 2, // カード払い
                'status' => 'paid',
            ],
            [
                'item_id' => 6,
                'user_id' => 1,
                'payment_method' => 1, // コンビニ払い
                'status' => 'paid',
            ],
            // テストユーザー2 (user_id: 2)
            [
                'item_id' => 3,
                'user_id' => 2,
                'payment_method' => 2, // カード払い
                'status' => 'paid',
            ],
            [
                'item_id' => 10,
                'user_id' => 2,
                'payment_method' => 1, // コンビニ払い
                'status' => 'paid',
            ],
            // テストユーザー3 (user_id: 3)
            [
                'item_id' => 7,
                'user_id' => 3,
                'payment_method' => 2, // カード払い
                'status' => 'paid',
            ],
            [
                'item_id' => 4,
                'user_id' => 3,
                'payment_method' => 1, // コンビニ払い
                'status' => 'paid',
            ],

        ];

        foreach ($soldItems as $data) {
            // item_id をキーにして重複をチェック
            SoldItem::updateOrCreate(
                ['item_id' => $data['item_id']],
                [
                    'user_id' => $data['user_id'],
                    'payment_method' => $data['payment_method'],
                    'status' => $data['status'],
                    'stripe_checkout_id' => null, // シーダーでは一旦null
                ]
            );
        }
    }
}
