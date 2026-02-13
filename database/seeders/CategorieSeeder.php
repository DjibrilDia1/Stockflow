<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categorie::create(['cat_nom' => 'Fournitures de bureau', 'cat_code' => 'FDB']);
        Categorie::create(['cat_nom' => 'MatÃ©riel Informatique', 'cat_code' => 'MAT-INF']);
        Categorie::create(['cat_nom' => 'Consommables', 'cat_code' => 'CONS']);
    }
}

