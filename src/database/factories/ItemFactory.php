<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'image_path' => 'item_images/' . $this->faker->word() . $this->faker->randomElement(['.png', '.jpeg']),
            'condition' => $this->faker->randomElement([1, 2, 3, 4]), //1:良好,2:目立った傷や汚れなし,3:やや傷や汚れあり,4:状態が悪い
            // 'condition' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'name'        => $this->faker->name(),
            'brand_name' => $this->faker->optional(0.7)->company(),
            'description' => $this->faker->realText(255),
            'price' => $this->faker->numberBetween(0, 50000),

        ];
    }
}
