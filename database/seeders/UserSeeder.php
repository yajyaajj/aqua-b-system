<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@aquab.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        \App\Models\User::create([
            'name' => 'Staff User',
            'email' => 'staff@aquab.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);
    }
}
