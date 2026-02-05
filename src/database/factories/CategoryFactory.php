<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 重複しないように unique() を使うのがおすすめです
            'name' => $this->faker->unique()->word(),
        ];
    }
}
