<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Ticket::create([
                'subject' => $faker->sentence(6), // Random sentence for subject
                'description' => $faker->paragraph(3), // Random paragraph for description
                'status' => $faker->randomElement(['open', 'closed']), // Random status
                'user_id' => $faker->numberBetween(1, 3), // Assuming user IDs between 1 and 10 exist
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
