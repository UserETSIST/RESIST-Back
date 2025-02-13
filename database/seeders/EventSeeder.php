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

    for ($i = 0; $i < 500; $i++) {
        $jamming = $faker->boolean;  // Generate a random boolean for jamming
        $spoofing = !$jamming;       // Set spoofing to the opposite of jamming

        DB::table('events')->insert([
            'latitude' => $faker->latitude(-90, 90),               // Random latitude with precision for latitude
            'longitude' => $faker->longitude(-180, 180),           // Random longitude with precision for longitude
            'flightlevel' => $faker->randomFloat(4, 1, 400),       // Random flight level between 1 and 400
            'last_detection' => Carbon::now()->subDays(rand(0, 30)), // Random timestamp within the last 30 days
            'jamming' => $jamming,                                // Random boolean for jamming
            'spoofing' => $spoofing,                              // Opposite of jamming
            'strength' => $faker->randomFloat(4, 0, 5),           // Random strength between 0 and 5 with 4 decimal places
            'pfa' => $faker->randomFloat(10, 0, 1),               // Random float between 0 and 1 with 10 decimal places
            'datum' => $faker->randomElement(['WGS84', 'NAD83', 'ETRS89']), // Random datum type
            'sat_ua' => $faker->numberBetween(0, 30),             // Random satellite count between 0 and 30
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

}
