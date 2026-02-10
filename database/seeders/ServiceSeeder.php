<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'name' => 'Service Commercial',
            'code' => 'COM',
            'type' => 'Administratif'
        ]);

        Service::create([
            'name' => 'Service Technique',
            'code' => 'TECH',
            'type' => 'Opérationnel'
        ]);

        Service::create([
            'name' => 'Service Production',
            'code' => 'PROD',
            'type' => 'Opérationnel'
        ]);
    }
}
