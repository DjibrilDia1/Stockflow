<?php

namespace App\Http\Controllers;

use App\Models\LigneDemandeSortie;
use App\Models\DemandeSortie;
use App\Models\Article;
use App\Models\Entrepot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class LigneDemandeSortieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/LigneDemandeSorties/Index', [
            'withdrawRequestLines' => LigneDemandeSortie::with(['withdrawRequest', 'item', 'warehouse'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/LigneDemandeSorties/Create', [
            'withdrawRequests' => DemandeSortie::all(['dso_id']), // Just ID for selection
            'items' => Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lds_dso_id' => 'required|exists:demandes_sortie,dso_id',
            'lds_art_id' => 'required|exists:articles,art_id',
            'lds_ent_id' => 'required|exists:entrepots,ent_id',
            'lds_qte_demandee' => 'required|integer|min:1',
            'lds_qte_servie' => 'required|integer|min:0',
            'lds_note' => 'nullable|string',
        ]);

        LigneDemandeSortie::create($validated);

        return Redirect::route('withdraw-request-lines.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(LigneDemandeSortie $withdrawRequestLine): Response
    {
        $withdrawRequestLine->load(['withdrawRequest', 'item', 'warehouse']);
        return Inertia::render('Gestionnaire/LigneDemandeSorties/Show', [
            'withdrawRequestLine' => $withdrawRequestLine,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LigneDemandeSortie $withdrawRequestLine): Response
    {
        return Inertia::render('Gestionnaire/LigneDemandeSorties/Edit', [
            'withdrawRequestLine' => $withdrawRequestLine,
            'withdrawRequests' => DemandeSortie::all(['dso_id']),
            'items' => Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LigneDemandeSortie $withdrawRequestLine)
    {
        $validated = $request->validate([
            'lds_dso_id' => 'required|exists:demandes_sortie,dso_id',
            'lds_art_id' => 'required|exists:articles,art_id',
            'lds_ent_id' => 'required|exists:entrepots,ent_id',
            'lds_qte_demandee' => 'required|integer|min:1',
            'lds_qte_servie' => 'required|integer|min:0',
            'lds_note' => 'nullable|string',
        ]);

        $withdrawRequestLine->update($validated);

        return Redirect::route('withdraw-request-lines.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LigneDemandeSortie $withdrawRequestLine)
    {
        $withdrawRequestLine->delete();

        return Redirect::route('withdraw-request-lines.index');
    }
}

