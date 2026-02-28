<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class CategoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assignments = [
            1 => [1, 5, 12],
            2 => [2],
            3 => [10, 11],
            4 => [1, 5,],
            5 => [2, 8, 11],
            6 => [2, 13],
            7 => [1, 4, 12],
            8 => [10],
            9 => [3, 10, 11],
            10 => [6],
        ];

        foreach ($assignments as $itemId => $categoryIds) {
            $item = Item::find($itemId);
            if ($item) {
                $item->categories()->syncWithoutDetaching($categoryIds);
            }
        }
    }
}
