<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo users for each role
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@pos.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Waiter User',
            'email' => 'waiter@pos.com',
            'password' => bcrypt('password'),
            'role' => 'waiter',
        ]);

        User::factory()->create([
            'name' => 'Kitchen User',
            'email' => 'kitchen@pos.com',
            'password' => bcrypt('password'),
            'role' => 'kitchen',
        ]);

        User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@pos.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);
    }
}
