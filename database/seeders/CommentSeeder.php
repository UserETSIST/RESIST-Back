<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Ensure you have some tickets and users already seeded
        $tickets = Ticket::all();
        $users = User::all();

        if ($tickets->isEmpty() || $users->isEmpty()) {
            $this->command->info('No tickets or users found. Please seed tickets and users first.');
            return;
        }

        // Create 100 comments with random tickets and users
        for ($i = 0; $i < 100; $i++) {
            Comment::create([
                'comment' => $faker->sentence(10),         // Random sentence for comment
                'ticket_id' => $tickets->random()->id,    // Random ticket ID
                'user_id' => $users->random()->id,        // Random user ID
                'created_at' => now()->subDays(rand(0, 30)), // Random date within the last 30 days
                'updated_at' => now(),
            ]);
        }
    }
}
