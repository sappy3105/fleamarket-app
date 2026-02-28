<?php

namespace Database\Factories;

use App\Models\ShippingAddress;
use App\Models\SoldItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingAddressFactory extends Factory
{
    protected $model = ShippingAddress::class;

    public function definition()
    {
        return [
            // 本来は外部キーが必要ですが、シーダー側で指定するため
            // ここではデータの「形」だけを定義します
            // 'sold_item_id' => SoldItem::factory(),削除
            'postcode' => $this->faker->postcode(), // 郵便番号 (例: 123-4567)
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(), // 住所
            'building' => $this->faker->secondaryAddress(), // 建物名・部屋番号
        ];
    }
}
