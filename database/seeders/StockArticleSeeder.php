<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\StockArticle;
use App\Models\Entrepot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itemStylo = Article::where('art_reference', 'STYLO-BLEU-001')->firstOrFail();
        $itemPc = Article::where('art_reference', 'PC-PORT-HP-005')->firstOrFail();
        $itemToner = Article::where('art_reference', 'TONER-LASER-30A')->firstOrFail();
        $itemCahier = Article::where('art_reference', 'CAHIER-A4-CARRE')->firstOrFail();

        $warehouseMain = Entrepot::where('ent_code', 'ENT-MAIN')->firstOrFail();
        $warehouseAnnexe = Entrepot::where('ent_code', 'ANN-MAG')->firstOrFail();

        // Stock for Stylo Bille Bleu
        StockArticle::create([
            'sta_art_id' => $itemStylo->art_id,
            'sta_ent_id' => $warehouseMain->ent_id,
            'sta_quantite' => 100,
        ]);
        StockArticle::create([
            'sta_art_id' => $itemStylo->art_id,
            'sta_ent_id' => $warehouseAnnexe->ent_id,
            'sta_quantite' => 50,
        ]);

        // Stock for Ordinateur Portable HP ProBook
        StockArticle::create([
            'sta_art_id' => $itemPc->art_id,
            'sta_ent_id' => $warehouseMain->ent_id,
            'sta_quantite' => 10,
        ]);

        // Stock for Cartouche Toner Laser 30A
        StockArticle::create([
            'sta_art_id' => $itemToner->art_id,
            'sta_ent_id' => $warehouseMain->ent_id,
            'sta_quantite' => 20,
        ]);
        StockArticle::create([
            'sta_art_id' => $itemToner->art_id,
            'sta_ent_id' => $warehouseAnnexe->ent_id,
            'sta_quantite' => 5,
        ]);

        // Stock for Cahier A4 Grands Carreaux
        StockArticle::create([
            'sta_art_id' => $itemCahier->art_id,
            'sta_ent_id' => $warehouseMain->ent_id,
            'sta_quantite' => 150,
        ]);
    }
}

