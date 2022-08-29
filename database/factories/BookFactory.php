<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->unique()->sentence(),
            'author' => fake()->name(),
            'isbn' => fake()->isbn10(),
            'description' => fake()->realText(),
            'publisher' => fake()->company(),
            'published_on' => fake()->dateTimeThisCentury(),
        ];
    }
}
