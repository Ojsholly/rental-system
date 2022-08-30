<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(100)->create();

        $this->call(BookSeeder::class);

        $this->call(EquipmentSeeder::class);

        $this->call(RentalSeeder::class);
    }
}
