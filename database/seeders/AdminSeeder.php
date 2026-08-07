<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@boerjo.test'],
            [
                'name' => 'Admin Boerjo',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone_number' => '081234567890',
            ]
        );
    }
}
