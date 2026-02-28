<?php

namespace Database\Factories;

use App\Models\SoldItem;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoldItemFactory extends Factory
{
    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'user_id' => User::factory(),
            'payment_method' => $this->faker->randomElement([1, 2]), // 1:コンビニ払い 2:カード払い
            'status' => $this->faker->randomElement(['pending', 'paid']),
            'stripe_checkout_id' => 'txt_' . $this->faker->uuid(),
        ];
    }
}
