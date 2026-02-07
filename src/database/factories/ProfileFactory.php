<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'user_id' => User::factory(),
            'image_path' => 'item_images/' . $this->faker->word() . $this->faker->randomElement(['.png', '.jpeg']),
            'postcode' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(),
        ];
    }
}
