<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['service_name' => 'Potong Rambut Reguler', 'price' => 35000, 'duration' => 30, 'description' => 'Potong rambut standar sesuai model yang diminta.'],
            ['service_name' => 'Potong + Cuci Rambut', 'price' => 50000, 'duration' => 45, 'description' => 'Potong rambut dilengkapi cuci rambut.'],
            ['service_name' => 'Cukur Jenggot', 'price' => 25000, 'duration' => 20, 'description' => 'Perapian dan pencukuran jenggot.'],
            ['service_name' => 'Hair Coloring', 'price' => 150000, 'duration' => 90, 'description' => 'Pewarnaan rambut sesuai pilihan warna.'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['service_name' => $service['service_name']], $service + ['status' => 'active']);
        }
    }
}
