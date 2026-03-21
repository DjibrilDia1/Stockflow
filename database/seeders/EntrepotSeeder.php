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
            'ent_nom' => 'Entrepot Principal',
            'ent_code' => 'ENT-MAIN',
            'ent_localisation' => 'Batiment A, Zone Industrielle'
        ]);

        Entrepot::create([
            'ent_nom' => 'Annexe Magasin',
            'ent_code' => 'ANN-MAG',
            'ent_localisation' => 'Batiment C, a cote de la production'
        ]);
    }
}

