<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'David',
            'last_name' => 'Dima',
            'email' => 'dima@gmail.com',
            'password' => Hash::make('dima'),
            'is_admin' => true,
            'is_active' => true
        ]);
    }
}
