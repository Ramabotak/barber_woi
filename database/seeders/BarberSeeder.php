<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Budi Santoso', 'email' => 'budi@boerjo.test', 'experience' => '5 tahun pengalaman potong rambut pria & styling.'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@boerjo.test', 'experience' => '3 tahun pengalaman, spesialis fade & cukur jenggot.'],
        ];

        foreach ($data as $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make('password'),
                    'role' => 'barber',
                    'phone_number' => '08' . rand(1000000000, 9999999999),
                ]
            );

            Barber::firstOrCreate(
                ['user_id' => $user->id],
                ['experience' => $item['experience'], 'status' => 'aktif']
            );
        }
    }
}
