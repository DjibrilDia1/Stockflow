<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Article;
use App\Models\Entrepot;
use App\Models\Fournisseur;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

class MouvementStockController extends Controller
{
    /**
     * Affiche la liste des mouvements de stock avec pagination.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Mouvements', [
            'stockMovements' => MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user'])
                ->latest('mvs_date_mouvement')
                ->paginate(3),
            'articles' => Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
            'suppliers' => Fournisseur::all(['fou_id', 'fou_nom']),
        ]);
    }

    /**
     * Enregistre un nouveau mouvement de stock.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mvs_art_id' => 'required|exists:articles,art_id',
            'mvs_ent_id' => 'required|exists:entrepots,ent_id',
            'mvs_type' => 'required|in:IN,OUT,ADJUST,TRANSFER',
            'mvs_quantite' => 'required|integer|min:1',
            'mvs_motif' => 'nullable|string|max:255',
            'mvs_date_mouvement' => 'required|date',
            'mvs_ent_dest_id' => 'required_if:mvs_type,TRANSFER|nullable|exists:entrepots,ent_id',
            'mvs_fou_id' => 'nullable|exists:fournisseurs,fou_id',
        ]);

        if (!isset($validated['mvs_usr_id'])) {
            $validated['mvs_usr_id'] = $request->user()->id;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            // 1. Enregistrer le mouvement
            $movement = MouvementStock::create($validated);

            // 2. Mettre à jour le stock dans l'entrepôt source
            $stockSource = \App\Models\StockArticle::firstOrCreate(
                ['sta_art_id' => $validated['mvs_art_id'], 'sta_ent_id' => $validated['mvs_ent_id']],
                ['sta_quantite' => 0]
            );

            if ($validated['mvs_type'] === 'IN' || $validated['mvs_type'] === 'ADJUST') {
                $stockSource->sta_quantite += $validated['mvs_quantite'];
            } elseif ($validated['mvs_type'] === 'OUT' || $validated['mvs_type'] === 'TRANSFER') {
                $stockSource->sta_quantite -= $validated['mvs_quantite'];
            }
            $stockSource->save();

            // 3. Si c'est un transfert, ajouter dans l'entrepôt de destination
            if ($validated['mvs_type'] === 'TRANSFER' && isset($validated['mvs_ent_dest_id'])) {
                $stockDest = \App\Models\StockArticle::firstOrCreate(
                    ['sta_art_id' => $validated['mvs_art_id'], 'sta_ent_id' => $validated['mvs_ent_dest_id']],
                    ['sta_quantite' => 0]
                );
                $stockDest->sta_quantite += $validated['mvs_quantite'];
                $stockDest->save();

                // On crée un deuxième mouvement de type "IN" pour la destination
                MouvementStock::create([
                    'mvs_art_id' => $validated['mvs_art_id'],
                    'mvs_ent_id' => $validated['mvs_ent_dest_id'],
                    'mvs_type' => 'IN',
                    'mvs_quantite' => $validated['mvs_quantite'],
                    'mvs_motif' => "Transfert depuis l'entrepôt ID: " . $validated['mvs_ent_id'] . ". " . ($validated['mvs_motif'] ?? ''),
                    'mvs_date_mouvement' => $validated['mvs_date_mouvement'],
                    'mvs_usr_id' => $validated['mvs_usr_id'],
                    'mvs_transfer_id' => $movement->mvs_id,
                ]);
            }
        });

        return Redirect::route('gestionnaire.mouvements.index')->with('success', 'Mouvement enregistré.');
    }

    /**
     * Supprime un mouvement de stock.
     */
    public function destroy(MouvementStock $stockMovement)
    {
        $stockMovement->delete();
        return Redirect::route('gestionnaire.mouvements.index')->with('success', 'Mouvement supprimé.');
    }
}
