<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Models\SoldItem;

class ShippingAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::where('email', 'test1@example.com')->first();
        $profile1 = $user1 ? $user1->profile : null;

        $itemIds = [6, 9, 3, 10, 4, 7];

        foreach ($itemIds as $itemId) {
            $soldItem = SoldItem::where('item_id', $itemId)->first();
            if (!$soldItem) continue;

            if (in_array($itemId, [6, 9]) && $profile1) {
                // ユーザー1のプロフィール住所を明示的に指定
                $addressData = [
                    'postcode' => $profile1->postcode,
                    'address'  => $profile1->address,
                    'building' => $profile1->building,
                ];
            } else {
                // それ以外はFactoryでランダム生成した配列を取得
                $addressData = ShippingAddress::factory()->raw();
            }

            // sold_item_id をセットして保存
            ShippingAddress::updateOrCreate(
                ['sold_item_id' => $soldItem->id],
                $addressData
            );
        }
    }
}
