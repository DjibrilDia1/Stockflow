<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\DemandeSortie;
use App\Models\Entrepot;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    // =========================================================================
    // DASHBOARD GESTIONNAIRE
    // =========================================================================

    /**
     * Dashboard principal du gestionnaire.
     * Charge les KPIs, alertes stock, mouvements récents et top articles.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Dashboard', [
            'stats'           => $this->buildManagerStats(),
            'stockAlerts'     => $this->buildStockAlerts(),
            'recentMovements' => $this->buildRecentMovements(),
            'topArticles'     => MouvementStock::topConsumedArticles(5),
        ]);
    }

    /** KPIs de la carte de synthèse (compteurs globaux + valeur du stock). */
    private function buildManagerStats(): array
    {
        return [
            'underThreshold' => Article::belowAlertThreshold()->count(),
            'totalArticles'  => Article::count(),
            'movements'      => MouvementStock::count(),
            'stockValue'     => Article::totalStockValue(),
        ];
    }

    /** 5 articles les plus proches de la rupture de stock. */
    private function buildStockAlerts(): \Illuminate\Support\Collection
    {
        return Article::belowAlertThreshold()
            ->take(5)
            ->get()
            ->map(fn ($article) => [
                'id'        => $article->art_id,
                'product'   => $article->art_nom,
                'location'  => 'Tous entrepôts',
                'current'   => (int) ($article->total_stock ?? 0),
                'threshold' => $article->art_seuil_alerte,
            ]);
    }

    /** 5 derniers mouvements de stock avec article, entrepôt et service. */
    private function buildRecentMovements(): \Illuminate\Support\Collection
    {
        return MouvementStock::with(['item', 'warehouse', 'service'])
            ->latest('mvs_id')
            ->take(5)
            ->get()
            ->map(fn ($m) => [
                'id'       => $m->mvs_id,
                'date'     => $m->mvs_date_mouvement?->format('d/m/Y') ?? 'N/A',
                'article'  => $m->item->art_nom ?? 'Article supprimé',
                'type'     => $m->mvs_type,
                'quantity' => $m->mvs_type === 'OUT' ? -$m->mvs_quantite : $m->mvs_quantite,
                'entrepot' => $m->warehouse->ent_nom ?? 'N/A',
                'service'  => $m->service->ser_nom ?? 'Service interne',
            ]);
    }

    // =========================================================================
    // DASHBOARD DEMANDEUR
    // =========================================================================

    /**
     * Dashboard du demandeur connecté.
     * Charge ses statistiques de demandes et ses 5 dernières demandes.
     */
    public function demandeurDashboard(): Response
    {
        $userId = Auth::id();

        return Inertia::render('Demandeur/Dashboard', [
            'stats'          => DemandeSortie::getStatsForUser($userId),
            'recentRequests' => $this->buildRecentRequests($userId),
            'notifications'  => $this->defaultNotifications(),
        ]);
    }

    /** 5 dernières demandes du demandeur connecté. */
    private function buildRecentRequests(int $userId): \Illuminate\Support\Collection
    {
        return DemandeSortie::forUser($userId)
            ->with(['service', 'lines.item'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($d) => [
                'id'      => $d->dso_id,
                'ref'     => $d->reference,
                'service' => $d->service->ser_nom ?? 'N/A',
                'date'    => $d->dso_created_at?->format('d/m/Y') ?? 'N/A',
                'status'  => $d->status_label,
            ]);
    }

    /** Notifications statiques affichées à la connexion. */
    private function defaultNotifications(): array
    {
        return [
            ['id' => 1, 'message' => 'Bienvenue sur votre espace de gestion de stock.', 'type' => 'info'],
        ];
    }
}