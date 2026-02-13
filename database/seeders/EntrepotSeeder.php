<?php

namespace Database\Seeders;

use App\Models\Entrepot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntrepotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entrepot::create([
            'ent_nom' => 'EntrepÃ´t Principal',
            'ent_code' => 'ENT-MAIN',
            'ent_localisation' => 'BÃ¢timent A, Zone Industrielle'
        ]);

        Entrepot::create([
            'ent_nom' => 'Annexe Magasin',
            'ent_code' => 'ANN-MAG',
            'ent_localisation' => 'BÃ¢timent C, Ã  cÃ´tÃ© de la production'
        ]);
    }
}

