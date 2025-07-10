<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Agent',
            'email' => 'agent@example.com',
            'role' => 'agent',
            'password' => 'password',
        ]);
        User::factory()->create([
            'name' => 'Agent 1',
            'email' => 'agent1@example.com',
            'role' => 'agent',
            'password' => 'password',
        ]);
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'role' => 'customer',
            'password' => 'password',
        ]);
        User::factory()->create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'role' => 'customer',
            'password' => 'password',
        ]);
    }
}