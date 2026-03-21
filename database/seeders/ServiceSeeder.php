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
            'ser_nom' => 'Service Commercial',
            'ser_code' => 'COM',
            'ser_type' => 'Administratif'
        ]);

        Service::create([
            'ser_nom' => 'Service Technique',
            'ser_code' => 'TECH',
            'ser_type' => 'Operationnel'
        ]);

        Service::create([
            'ser_nom' => 'Service Production',
            'ser_code' => 'PROD',
            'ser_type' => 'Operationnel'
        ]);
    }
}
