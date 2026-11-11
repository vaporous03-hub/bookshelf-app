<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GenreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['技術書', '小説', 'ビジネス', '自己啓発', '歴史', '科学']),
        ];
    }
}
