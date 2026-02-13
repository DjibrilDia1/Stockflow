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
        return Inertia::render('Gestionnaire/MouvementStocks/Index', [
            'stockMovements' => MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user'])->get(),
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
            'mvs_fou_id' => 'nullable|exists:fournisseurs,fou_id',
            'mvs_ser_id' => 'nullable|exists:services,ser_id',
            'mvs_usr_id' => ['nullable', Rule::exists('users', 'id')],
            'mvs_transfer_id' => ['nullable', Rule::exists('mouvements_stock', 'mvs_id')],
            'mvs_piece_jointe_url' => 'nullable|url|max:255',
            'mvs_date_mouvement' => 'required|date',
        ]);

        // Default user_id to current authenticated user if not provided
        if (!isset($validated['mvs_usr_id'])) {
            $validated['mvs_usr_id'] = $request->user()->id;
        }

        MouvementStock::create($validated);

        return Redirect::route('stock-movements.index');
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
        // Direct deletion of MouvementStock records is not typically done.
        return Redirect::route('stock-movements.index')->with('error', 'Direct deletion of stock movement is not allowed.');
    }
}

