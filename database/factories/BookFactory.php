<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => '9784' . $this->faker->numerify('#########'),
            'published_date' => $this->faker->date(),
            'description' => $this->faker->paragraph(),
        ];
    }
}