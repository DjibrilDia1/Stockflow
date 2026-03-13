<?php

namespace App\Http\Controllers;

use App\Models\DemandeSortie;
use App\Models\Service;
use App\Models\User;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DemandeSortieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Demandes', DemandeSortie::getManagerIndexData());
    }

    /**
     * Affiche les demandes du demandeur connect.
     */
    public function demandeurIndex(): Response
    {
        return Inertia::render('Demandeur/Demandes', DemandeSortie::getDemandeurIndexData(auth()->id()));
    }

    /**
     * Enregistre une nouvelle demande du demandeur.
     */
    public function demandeurStore(Request $request)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,art_id',
            'entrepot_id' => 'required|exists:entrepots,ent_id',
            'quantite' => 'required|integer|min:1',
            'motif' => 'nullable|string',
        ]);

        $article = Article::find($validated['article_id']);
        if (!$article || !$article->hasStock($validated['entrepot_id'], $validated['quantite'])) {
            return Redirect::back()->with('error', 'La quantité demandée dépasse le stock disponible dans cet entrepôt.');
        }

        DemandeSortie::createWithLine($validated, auth()->id());

        return Redirect::route('demandeur.demandes.index')->with('success', 'Demande de sortie créée avec succès.');
    }

    /**
     * Valide ou rejette une demande (pour le gestionnaire).
     */
    public function validateRequest(Request $request, DemandeSortie $demande)
    {
        $validated = $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
        ]);

        if ($validated['status'] === 'APPROVED') {
            if (!$demande->approve()) {
                return Redirect::back()->with('error', 'Impossible d\'approuver la demande. Vérifiez le stock disponible.');
            }
        } else {
            $demande->reject();
        }

        return Redirect::back()->with('success', 'La demande a été ' . ($validated['status'] === 'APPROVED' ? 'approuvée.' : 'rejetée.'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/DemandeSorties/Create', [
            'services' => Service::all(['ser_id', 'ser_nom']),
            'users' => User::all(['id', 'name']), 
            'statuses' => ['DRAFT', 'APPROVED', 'FULFILLED', 'REJECTED'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dso_ser_id' => 'required|exists:services,ser_id',
            'dso_demandeur_id' => 'required|exists:users,id',
            'dso_statut' => 'required|in:DRAFT,APPROVED,FULFILLED,REJECTED',
        ]);

        DemandeSortie::create($validated);

        return Redirect::route('gestionnaire.demandes.index')->with('success', 'Demande de sortie créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DemandeSortie $withdrawRequest): Response
    {
        $withdrawRequest->load(['service', 'requester', 'lines.item', 'lines.warehouse']);
        return Inertia::render('Gestionnaire/DemandeSorties/Show', [
            'withdrawRequest' => $withdrawRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DemandeSortie $withdrawRequest): Response
    {
        return Inertia::render('Gestionnaire/DemandeSorties/Edit', [
            'withdrawRequest' => $withdrawRequest,
            'services' => Service::all(['ser_id', 'ser_nom']),
            'users' => User::all(['id', 'name']),
            'statuses' => ['DRAFT', 'APPROVED', 'FULFILLED', 'REJECTED'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DemandeSortie $withdrawRequest)
    {
        $validated = $request->validate([
            'dso_ser_id' => 'required|exists:services,ser_id',
            'dso_demandeur_id' => 'required|exists:users,id',
            'dso_statut' => 'required|in:DRAFT,APPROVED,FULFILLED,REJECTED',
        ]);

        $withdrawRequest->update($validated);

        return Redirect::route('gestionnaire.demandes.index')->with('success', 'Demande de sortie mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DemandeSortie $withdrawRequest)
    {
        $withdrawRequest->delete();

        return Redirect::route('gestionnaire.demandes.index')->with('success', 'Demande de sortie supprimée avec succès.');
    }
}
