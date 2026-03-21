<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\DemandeSortie;
use App\Models\Service;
use App\Models\StockArticle;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DemandeSortieController extends Controller
{
    // =========================================================================
    // RÈGLES DE VALIDATION
    // =========================================================================

    /** Règles communes à store() et update() (vue gestionnaire). */
    private function validationRules(): array
    {
        $statuses = implode(',', [
            DemandeSortie::STATUS_DRAFT,
            DemandeSortie::STATUS_APPROVED,
            DemandeSortie::STATUS_FULFILLED,
            DemandeSortie::STATUS_REJECTED,
        ]);

        return [
            'dso_ser_id'       => 'required|exists:services,ser_id',
            'dso_demandeur_id' => 'required|exists:users,id',
            'dso_statut'       => "required|in:{$statuses}",
        ];
    }

    // =========================================================================
    // VUE GESTIONNAIRE
    // =========================================================================

    /**
     * Liste paginée des demandes pour le gestionnaire.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Demandes', DemandeSortie::getManagerIndexData());
    }

    /**
     * Formulaire de création d'une demande (vue gestionnaire).
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/DemandeSorties/Create', $this->formData());
    }

    /**
     * Crée une nouvelle demande depuis la vue gestionnaire.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        DemandeSortie::create($validated);

        return Redirect::route('gestionnaire.demandes.index')
            ->with('success', 'Demande de sortie créée avec succès.');
    }

    /**
     * Détail d'une demande spécifique.
     */
    public function show(DemandeSortie $withdrawRequest): Response
    {
        $withdrawRequest->load(['service', 'requester', 'lines.item', 'lines.warehouse']);

        return Inertia::render('Gestionnaire/DemandeSorties/Show', [
            'withdrawRequest' => $withdrawRequest,
        ]);
    }

    /**
     * Formulaire de modification d'une demande.
     */
    public function edit(DemandeSortie $withdrawRequest): Response
    {
        return Inertia::render('Gestionnaire/DemandeSorties/Edit', array_merge(
            ['withdrawRequest' => $withdrawRequest],
            $this->formData()
        ));
    }

    /**
     * Met à jour une demande existante.
     */
    public function update(Request $request, DemandeSortie $withdrawRequest): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $withdrawRequest->update($validated);

        return Redirect::route('gestionnaire.demandes.index')
            ->with('success', 'Demande de sortie mise à jour avec succès.');
    }

    /**
     * Supprime une demande.
     */
    public function destroy(DemandeSortie $withdrawRequest): RedirectResponse
    {
        $withdrawRequest->delete();

        return Redirect::route('gestionnaire.demandes.index')
            ->with('success', 'Demande de sortie supprimée avec succès.');
    }

    /**
     * Approuve ou rejette une demande.
     * L'approbation vérifie le stock et crée les mouvements OUT via le modèle.
     */
    public function validateRequest(Request $request, DemandeSortie $demande): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:' . DemandeSortie::STATUS_APPROVED . ',' . DemandeSortie::STATUS_REJECTED,
        ]);

        if ($validated['status'] === DemandeSortie::STATUS_APPROVED) {
            if (!$demande->approve(auth()->id())) {
                return Redirect::back()
                    ->with('error', 'Stock insuffisant dans l\'entrepôt choisi.');
            }
            return Redirect::back()->with('success', 'La demande a été approuvée.');
        }

        $demande->reject();

        return Redirect::back()->with('success', 'La demande a été rejetée.');
    }

    // =========================================================================
    // VUE DEMANDEUR
    // =========================================================================

    /**
     * Liste des demandes du demandeur connecté.
     */
    public function demandeurIndex(): Response
    {
        return Inertia::render('Demandeur/Demandes', [
            'requests'            => $this->buildDemandeurRequests(),
            'articlesDisponibles' => Article::asAvailableList(),
        ]);
    }

    /**
     * Crée une nouvelle demande depuis la vue demandeur.
     * Vérifie le stock disponible avant de créer.
     */
    public function demandeurStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'article_id'  => 'required|exists:articles,art_id',
            'entrepot_id' => 'required|exists:entrepots,ent_id',
            'quantite'    => 'required|integer|min:1',
            'motif'       => 'nullable|string',
        ]);

        $stock = StockArticle::findStock($validated['article_id'], $validated['entrepot_id']);

        if (!$stock || $stock->sta_quantite < $validated['quantite']) {
            return Redirect::back()
                ->with('error', 'La quantité demandée dépasse le stock disponible dans cet entrepôt.');
        }

        $serviceId = auth()->user()->ser_id ?? Service::value('ser_id');

        DemandeSortie::createWithLine($serviceId, auth()->id(), $validated);

        return Redirect::route('demandeur.demandes.index')
            ->with('success', 'Demande de sortie créée avec succès.');
    }

    // =========================================================================
    // MÉTHODES PRIVÉES
    // =========================================================================

    /**
     * Données partagées entre create() et edit() (formulaires gestionnaire).
     */
    private function formData(): array
    {
        $statuses = [
            DemandeSortie::STATUS_DRAFT,
            DemandeSortie::STATUS_APPROVED,
            DemandeSortie::STATUS_FULFILLED,
            DemandeSortie::STATUS_REJECTED,
        ];

        return [
            'services' => Service::all(['ser_id', 'ser_nom']),
            'users'    => User::all(['id', 'name']),
            'statuses' => $statuses,
        ];
    }

    /**
     * Demandes du demandeur connecté formatées pour la vue.
     * Utilise les accesseurs du modèle pour éviter la duplication du match().
     */
    private function buildDemandeurRequests(): \Illuminate\Support\Collection
    {
        return DemandeSortie::forUser(auth()->id())
            ->with(['lines.item'])
            ->latest()
            ->get()
            ->map(fn ($d) => [
                'id'      => $d->dso_id,
                'ref'     => $d->reference,
                'type'    => 'Retrait',
                'article' => $d->lines->first()?->item?->art_nom ?? 'Multiples',
                'qty'     => $d->lines->sum('lds_qte_demandee'),
                'date'    => $d->dso_created_at?->format('d/m/Y') ?? 'N/A',
                'status'  => $d->status_label,
            ]);
    }
}