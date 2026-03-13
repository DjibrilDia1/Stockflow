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
    public function index(Request $request): Response
    {
        $query = MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('mvs_motif', 'like', "%{$request->search}%")
                  ->orWhereHas('item', function($q) use ($request) {
                      $q->where('art_nom', 'like', "%{$request->search}%")
                        ->orWhere('art_reference', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->type) {
            $query->where('mvs_type', $request->type);
        }

        if ($request->article_id) {
            $query->where('mvs_art_id', $request->article_id);
        }

        if ($request->warehouse_id) {
            $query->where(function($q) use ($request) {
                $q->where('mvs_ent_id', $request->warehouse_id)
                  ->orWhere('mvs_ent_dest_id', $request->warehouse_id);
            });
        }

        if ($request->date_start) {
            $query->where('mvs_date_mouvement', '>=', $request->date_start);
        }

        if ($request->date_end) {
            $query->where('mvs_date_mouvement', '<=', $request->date_end);
        }

        return Inertia::render('Gestionnaire/Mouvements', [
            'stockMovements' => $query->latest('mvs_date_mouvement')->paginate(4)->withQueryString(),
            'warehouses_paginated' => \App\Models\Entrepot::paginate(5, ['*'], 'entrepots')->withQueryString(),
            'articles' => \App\Models\Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => \App\Models\Entrepot::all(['ent_id', 'ent_nom']),
            'suppliers' => \App\Models\Fournisseur::all(['fou_id', 'fou_nom']),
            'filters' => $request->only(['search', 'type', 'article_id', 'warehouse_id', 'date_start', 'date_end'])
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

        MouvementStock::recordWithStockUpdate($validated);

        $messages = [
            'IN' => 'L\'entrée de stock a été enregistrée avec succès.',
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

    // --- Entrepôts ---

    public function storeEntrepot(Request $request)
    {
        $validated = $request->validate([
            'ent_nom' => 'required|string|max:255',
            'ent_code' => 'required|string|max:255|unique:entrepots,ent_code',
            'ent_localisation' => 'nullable|string|max:255',
        ]);

        Entrepot::create($validated);
        return Redirect::back()->with('success', 'Entrepôt ajouté.');
    }

    public function updateEntrepot(Request $request, Entrepot $entrepot)
    {
        $validated = $request->validate([
            'ent_nom' => 'required|string|max:255',
            'ent_code' => 'required|string|max:255|unique:entrepots,ent_code,' . $entrepot->ent_id . ',ent_id',
            'ent_localisation' => 'nullable|string|max:255',
        ]);

        $entrepot->update($validated);
        return Redirect::back()->with('success', 'Entrepôt mis à jour.');
    }

    public function destroyEntrepot(Entrepot $entrepot)
    {
        $entrepot->delete();
        return Redirect::back()->with('success', 'Entrepôt supprimé.');
    }
}
