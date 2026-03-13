<?php

namespace App\Http\Controllers;

use App\Models\DemandeSortie;
use App\Models\StockArticle;
use App\Models\Service;
use App\Models\User;
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
        return Inertia::render('Gestionnaire/Demandes', [
            'withdrawRequests' => DemandeSortie::with(['service', 'requester', 'lines.item'])
                ->latest()
                ->paginate(3),
        ]);
    }

    /**
     * Affiche les demandes du demandeur connecté.
     */
    public function demandeurIndex(): Response
    {
        return Inertia::render('Demandeur/Demandes', [
            'requests' => DemandeSortie::forUser(auth()->id())
                ->with(['lines.item'])
                ->latest()
                ->get()
                ->map(function ($d) {
                    return [
                        'id'      => $d->dso_id,
                        'ref'     => 'DSO-' . str_pad($d->dso_id, 5, '0', STR_PAD_LEFT),
                        'type'    => 'Retrait',
                        'article' => $d->lines->first()->item->art_nom ?? 'Multiples',
                        'qty'     => $d->lines->sum('lds_qte_demandee'),
                        'date'    => $d->dso_created_at->format('d/m/Y'),
                        'status'  => match ($d->dso_statut) {
                            'DRAFT'     => 'En validation',
                            'APPROVED'  => 'En preparation',
                            'FULFILLED' => 'Prete',
                            'REJECTED'  => 'Rejetee',
                            default     => $d->dso_statut,
                        },
                    ];
                }),
            'articlesDisponibles' => \App\Models\Article::with(['itemStocks.warehouse'])
                ->get()
                ->map(function ($article) {
                    return [
                        'id'         => $article->art_id,
                        'nom'        => $article->art_nom,
                        'warehouses' => $article->itemStocks->map(function ($stock) {
                            return [
                                'id'   => $stock->sta_ent_id,
                                'name' => $stock->warehouse->ent_nom ?? 'N/A',
                                'qty'  => $stock->sta_quantite,
                            ];
                        })->values(),
                    ];
                }),
        ]);
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

        $stock = StockArticle::findStock($validated['article_id'], $validated['entrepot_id']);

        if (!$stock || $stock->sta_quantite < $validated['quantite']) {
            return Redirect::back()->with('error', 'La quantité demandée dépasse le stock disponible dans cet entrepôt.');
        }

        $serviceId = auth()->user()->ser_id;

        if (!$serviceId) {
            $service = Service::first();
            $serviceId = $service ? $service->ser_id : null;
        }

        DemandeSortie::createWithLine($serviceId, auth()->id(), $validated);

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

        if ($validated['status'] === 'APPROVED' && $demande->dso_statut === 'DRAFT') {
            if (!$demande->approve(auth()->id())) {
                return Redirect::back()->with('error', 'Stock insuffisant dans l\'entrepôt choisi.');
            }
        }

        $demande->update([
            'dso_statut' => $validated['status'],
        ]);

        return Redirect::back()->with('success', 'La demande a été ' . ($validated['status'] === 'APPROVED' ? 'approuvée et le stock a été mis à jour.' : 'rejetée.'));
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
