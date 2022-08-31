<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RentalStatsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testRentalStats()
    {
        $start = fake()->dateTimeBetween('-1 year', 'today');
        $end = now()->endOfDay()->toDateTimeString();

        $this->getJson(route('admin.rentals.stats', compact('start', 'end')))
            ->assertOk()
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => [
                        'rentedBooksCount', 'rentedEquipmentCount', 'returnedBooksCount', 'returnedEquipmentCount',
                    ],
                ]);
    }
}
