<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catFdb = Categorie::where('cat_code', 'FDB')->firstOrFail();
        $catMatInf = Categorie::where('cat_code', 'MAT-INF')->firstOrFail();
        $catCons = Categorie::where('cat_code', 'CONS')->firstOrFail();

        Article::create([
            'art_reference' => 'STYLO-BLEU-001',
            'art_nom' => 'Stylo Bille Bleu',
            'art_unite' => 'unité',
            'art_cat_id' => $catFdb->cat_id,
            'art_seuil_alerte' => 10,
            'art_stock_securite' => 5,
            'art_prix_defaut' => 150.00,
        ]);

        Article::create([
            'art_reference' => 'PC-PORT-HP-005',
            'art_nom' => 'Ordinateur Portable HP ProBook',
            'art_unite' => 'unité',
            'art_cat_id' => $catMatInf->cat_id,
            'art_seuil_alerte' => 2,
            'art_stock_securite' => 1,
            'art_prix_defaut' => 450000.00,
        ]);

        Article::create([
            'art_reference' => 'TONER-LASER-30A',
            'art_nom' => 'Cartouche Toner Laser 30A',
            'art_unite' => 'unité',
            'art_cat_id' => $catCons->cat_id,
            'art_seuil_alerte' => 3,
            'art_stock_securite' => 1,
            'art_prix_defaut' => 35000.00,
        ]);

        Article::create([
            'art_reference' => 'CAHIER-A4-CARRE',
            'art_nom' => 'Cahier A4 Grands Carreaux',
            'art_unite' => 'unité',
            'art_cat_id' => $catFdb->cat_id,
            'art_seuil_alerte' => 20,
            'art_stock_securite' => 10,
            'art_prix_defaut' => 500.00,
        ]);
    }
}

