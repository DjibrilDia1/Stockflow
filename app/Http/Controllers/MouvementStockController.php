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
use Illuminate\Validation\Rule; // Added this line

class MouvementStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Mouvements', [
            'stockMovements' => MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user'])->paginate(3),
            'articles' => Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/MouvementStocks/Create', [
            'items' => Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
            'suppliers' => Fournisseur::all(['fou_id', 'fou_nom']),
            'services' => Service::all(['ser_id', 'ser_nom']),
            'users' => User::all(['id', 'name']),
            'movementTypes' => ['IN', 'OUT', 'ADJUST', 'TRANSFER'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
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
            'mvs_ent_dest_id' => 'nullable|exists:entrepots,ent_id', // Pour les transferts
        ]);

        if (!isset($validated['mvs_usr_id'])) {
            $validated['mvs_usr_id'] = $request->user()->id;
        }

        // 1. Enregistrer le mouvement
        MouvementStock::create($validated);

        // 2. Mettre à jour le stock dans l'entrepôt source
        $stockSource = \App\Models\StockArticle::firstOrNew([
            'sta_art_id' => $validated['mvs_art_id'],
            'sta_ent_id' => $validated['mvs_ent_id'],
        ]);

        if ($validated['mvs_type'] === 'IN' || $validated['mvs_type'] === 'ADJUST') {
            $stockSource->sta_quantite += $validated['mvs_quantite'];
        } elseif ($validated['mvs_type'] === 'OUT' || $validated['mvs_type'] === 'TRANSFER') {
            $stockSource->sta_quantite -= $validated['mvs_quantite'];
        }
        $stockSource->save();

        // 3. Si c'est un transfert, ajouter dans l'entrepôt de destination
        if ($validated['mvs_type'] === 'TRANSFER' && isset($validated['mvs_ent_dest_id'])) {
            $stockDest = \App\Models\StockArticle::firstOrNew([
                'sta_art_id' => $validated['mvs_art_id'],
                'sta_ent_id' => $validated['mvs_ent_dest_id'],
            ]);
            $stockDest->sta_quantite += $validated['mvs_quantite'];
            $stockDest->save();
        }

        return Redirect::route('gestionnaire.mouvements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(MouvementStock $stockMovement): Response
    {
        $stockMovement->load(['item', 'warehouse', 'supplier', 'service', 'user']);
        return Inertia::render('Gestionnaire/MouvementStocks/Show', [
            'stockMovement' => $stockMovement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * Direct editing of MouvementStock records is not typically done.
     */
    public function edit(MouvementStock $stockMovement): Response
    {
        return Inertia::render('Gestionnaire/MouvementStocks/Edit', [
            'stockMovement' => $stockMovement,
            'message' => 'Direct editing of stock movements is not recommended. Movements are historical records.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MouvementStock $stockMovement)
    {
        // Direct updating of MouvementStock records is not typically done.
        return Redirect::route('stock-movements.index')->with('error', 'Direct update of stock movement is not allowed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MouvementStock $stockMovement)
    {
        // On récupère le stock associé pour éventuellement ajuster (optionnel selon vos règles métier)
        $stockMovement->delete();

        return Redirect::route('gestionnaire.mouvements.index')->with('success', 'Mouvement supprimé avec succès.');
    }
}

