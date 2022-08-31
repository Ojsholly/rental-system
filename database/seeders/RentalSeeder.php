<?php

namespace Database\Seeders;

use App\Models\Rental;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rental::factory()->times(50)->create();                         // Books and Equipment were rented but none were returned.

        Rental::factory()->times(50)->onlyBookRented()->create();       // Only books were rented but none were returned.

        Rental::factory()->times(50)->onlyEquipmentRented()->create();  // Only equipment was rented but none were returned.

        Rental::factory()->times(50)->returnedOnlyBook()->create();     // Books and equipment were rented but only books were returned.

        Rental::factory()->times(50)->returnedOnlyEquipment()->create();    // Books and equipment were rented but only equipment was returned.

        Rental::factory()->times(50)->returnedBoth()->create();          // Books and equipment were rented and returned.
    }
}
