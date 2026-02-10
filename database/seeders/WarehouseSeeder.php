<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'name' => 'Entrepôt Principal',
            'code' => 'ENT-MAIN',
            'location' => 'Bâtiment A, Zone Industrielle'
        ]);

        Warehouse::create([
            'name' => 'Annexe Magasin',
            'code' => 'ANN-MAG',
            'location' => 'Bâtiment C, à côté de la production'
        ]);
    }
}
