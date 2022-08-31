<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->uuid,
            'equipment_id' => Equipment::inRandomOrder()->first()->uuid,
            'book_id' => Book::inRandomOrder()->first()->uuid,
            'due_date' => $this->faker->dateTimeBetween('-1 week', '+3 months')->format('Y-m-d'),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'book_returned_at' => null,
            'equipment_returned_at' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'today')->format('Y-m-d H:i:s'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'today')->format('Y-m-d H:i:s'),
        ];
    }

    public function onlyBookRented()
    {
        return $this->state(function (array $attributes) {
            return [
                'book_id' => Book::inRandomOrder()->first()->uuid,
                'equipment_id' => null,
            ];
        });
    }

    public function onlyEquipmentRented()
    {
        return $this->state(function (array $attributes) {
            return [
                'equipment_id' => Equipment::inRandomOrder()->first()->uuid,
                'book_id' => null,
            ];
        });
    }

    public function returnedOnlyBook()
    {
        return $this->state(function (array $attributes) {
            return [
                'book_returned_at' => $this->faker->dateTimeBetween('-1 year', 'today')->format('Y-m-d'),
                'equipment_returned_at' => null,
            ];
        });
    }

    public function returnedOnlyEquipment()
    {
        return $this->state(function (array $attributes) {
            return [
                'equipment_returned_at' => $this->faker->dateTimeBetween('-1 year', 'today')->format('Y-m-d'),
                'book_returned_at' => null,
            ];
        });
    }

    /**
     * Indicate that both the book and equipment were returned.
     *
     * @return RentalFactory|m.\Database\Factories\RentalFactory.state
     */
    public function returnedBoth()
    {
        return $this->state(function (array $attributes) {
            return [
                'book_returned_at' => $this->faker->dateTimeBetween('-1 year', 'today')->format('Y-m-d'),
                'equipment_returned_at' => $this->faker->dateTimeBetween('-1 year', 'today')->format('Y-m-d'),
            ];
        });
    }
}
