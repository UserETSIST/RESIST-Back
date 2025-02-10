<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            DB::table('events')->insert([
                'lat' => $faker->latitude(-90, 90),                      // Random latitude
                'lon' => $faker->longitude(-180, 180),                  // Random longitude
                'flightlevel' => $faker->numberBetween(1, 400),         // Random flight level between 1 and 400
                'lastdetectiontimestamp' => Carbon::now()->subDays(rand(0, 30)), // Random timestamp within the last 30 days
                'jamming' => $faker->boolean,                           // Random boolean (0 or 1)
                'spoofing' => $faker->boolean,                          // Random boolean (0 or 1)
                'strength' => $faker->numberBetween(1, 2),            // Random strength between 1 and 100
                'pfa' => $faker->randomFloat(6, 0, 1),                  // Random float between 0 and 1 with 2 decimal places
                'datum' => $faker->randomElement(['WGS84', 'NAD83', 'ETRS89']), // Random datum type
                'sat_ua' => $faker->numberBetween(0, 30),               // Random satellite count between 0 and 30
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
