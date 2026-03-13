<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class MouvementStockController extends Controller
{
    /**
     * Affiche la liste des mouvements de stock avec pagination.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Mouvements', MouvementStock::getIndexData());
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

        MouvementStock::recordMovement($validated);

        $messages = [
            'IN' => 'Entrée
             enregistrée avec succès.',
            'OUT' => 'La sortie de stock a été enregistrée avec succès.',
            'TRANSFER' => 'Le transfert entre entrepôts a été effectué avec succès.',
            'ADJUST' => 'L\'ajustement de stock a été enregistré avec succès.',
        ];

        $type = $validated['mvs_type'];
        $message = $messages[$type] ?? 'Le mouvement de stock a été enregistré.';

        return Redirect::route('gestionnaire.mouvements.index')->with('success', $message);
    }

    /**
     * Supprime un mouvement de stock.
     */
    public function destroy(MouvementStock $stockMovement)
    {
        $stockMovement->delete();
        return Redirect::route('gestionnaire.mouvements.index')->with('success', 'Mouvement de stock supprimé avec succès.');
    }
}
