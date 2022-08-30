<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->words(3, true),
            'manufacturer' => fake()->company(),
            'description' => fake()->realText(),
            'serial_number' => Str::random(mt_rand(6, 15)).fake()->numberBetween(1000, 9999),
            'model_number' => Str::random(mt_rand(10, 20)),
        ];
    }
}
