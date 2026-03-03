<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MouvementStock;
use App\Models\Article;
use App\Models\Entrepot;

class MouvementStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = Article::all();
        $entrepots = Entrepot::all();

        if ($articles->isEmpty() || $entrepots->isEmpty()) {
            echo "Attention: Aucun article ou entrepôt trouvé pour les mouvements de stock.
";
            return;
        }

        // Mouvements d'entrée
        MouvementStock::create([
            'mvs_art_id' => $articles->random()->art_id,
            'mvs_ent_id' => $entrepots->random()->ent_id,
            'mvs_type' => 'IN',
            'mvs_quantite' => 50,
            'mvs_motif' => 'Réapprovisionnement fournisseur',
            'mvs_date_mouvement' => now()->subDays(5),
        ]);

        // Mouvements de sortie
        MouvementStock::create([
            'mvs_art_id' => $articles->random()->art_id,
            'mvs_ent_id' => $entrepots->random()->ent_id,
            'mvs_type' => 'OUT',
            'mvs_quantite' => 10,
            'mvs_motif' => 'Vente client',
            'mvs_date_mouvement' => now()->subDays(3),
        ]);

        // Mouvements d'ajustement
        MouvementStock::create([
            'mvs_art_id' => $articles->random()->art_id,
            'mvs_ent_id' => $entrepots->random()->ent_id,
            'mvs_type' => 'ADJUST',
            'mvs_quantite' => -5, // Ajustement négatif
            'mvs_motif' => 'Inventaire : article manquant',
            'mvs_date_mouvement' => now()->subDays(2),
        ]);

        // Mouvements de transfert
        MouvementStock::create([
            'mvs_art_id' => $articles->random()->art_id,
            'mvs_ent_id' => $entrepots->random()->ent_id,
            'mvs_type' => 'TRANSFER',
            'mvs_quantite' => 20,
            'mvs_motif' => 'Transfert vers un autre entrepôt',
            'mvs_date_mouvement' => now()->subDays(1),
        ]);

        // Exemple supplémentaire
        MouvementStock::create([
            'mvs_art_id' => $articles->random()->art_id,
            'mvs_ent_id' => $entrepots->random()->ent_id,
            'mvs_type' => 'IN',
            'mvs_quantite' => 100,
            'mvs_motif' => 'Nouvelle réception',
            'mvs_date_mouvement' => now(),
        ]);
    }
}
