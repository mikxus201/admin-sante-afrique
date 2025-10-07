<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            ['name' => 'Moderator', 'password' => Hash::make('password'), 'role' => 'moderator']
        );
    }
}
