<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Entrepot;
use App\Models\MouvementStock;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\DemandeSortie;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $totalArticles    = Article::count();
        $totalCategories  = Categorie::count();
        $totalEntrepots   = Entrepot::count();
        $movementsCount   = MouvementStock::count();
        $stockValue       = Article::totalStockValue();

        $alertsQuery         = Article::belowAlertThreshold();
        $underThresholdCount = $alertsQuery->count();

        $stockAlerts = $alertsQuery->take(5)->get()->map(function ($article) {
            return [
                'id'        => $article->art_id,
                'product'   => $article->art_nom,
                'location'  => 'Tous entrepôts',
                'current'   => (int) ($article->total_stock ?? 0),
                'threshold' => $article->art_seuil_alerte,
            ];
        });

        $recentMovements = MouvementStock::with(['item', 'warehouse', 'service'])
            ->latest('mvs_id')
            ->take(5)
            ->get()
            ->map(function ($m) {
                return [
                    'id'       => $m->mvs_id,
                    'date'     => $m->mvs_date_mouvement ? $m->mvs_date_mouvement->format('d/m/Y') : 'N/A',
                    'article'  => $m->item->art_nom ?? 'Article supprimé',
                    'type'     => $m->mvs_type,
                    'quantity' => $m->mvs_type === 'OUT' ? -$m->mvs_quantite : $m->mvs_quantite,
                    'entrepot' => $m->warehouse->ent_nom ?? 'N/A',
                    'service'  => $m->service->ser_nom ?? 'Service interne',
                ];
            });

        $topArticles = MouvementStock::topConsumedArticles(5);

        return Inertia::render('Gestionnaire/Dashboard', [
            'stats' => [
                'underThreshold' => $underThresholdCount,
                'totalArticles'  => $totalArticles,
                'movements'      => $movementsCount,
                'stockValue'     => $stockValue,
            ],
            'stockAlerts'     => $stockAlerts,
            'recentMovements' => $recentMovements,
            'topArticles'     => $topArticles,
        ]);
    }

    /**
     * Dashboard pour les demandeurs.
     */
    public function demandeurDashboard(): Response
    {
        $userId = Auth::id();
        
        $inProgressCount = DemandeSortie::forUser($userId)->inProgress()->count();
        $processedCount  = DemandeSortie::forUser($userId)->processed()->count();
        $rejectedCount   = DemandeSortie::forUser($userId)->rejected()->count();

        $recentRequests = DemandeSortie::forUser($userId)
            ->with(['service', 'lines.item'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($d) {
                return [
                    'id' => $d->dso_id,
                    'ref' => 'DSO-' . str_pad($d->dso_id, 5, '0', STR_PAD_LEFT),
                    'service' => $d->service->ser_nom ?? 'N/A',
                    'date' => $d->dso_created_at ? $d->dso_created_at->format('d/m/Y') : 'N/A',
                    'status' => match($d->dso_statut) {
                        'DRAFT' => 'En validation',
                        'APPROVED' => 'En preparation',
                        'FULFILLED' => 'Prete',
                        'REJECTED' => 'Rejetee',
                        default => $d->dso_statut
                    },
                ];
            });

        return Inertia::render('Demandeur/Dashboard', [
            'stats' => [
                'inProgress' => $inProgressCount,
                'processed' => $processedCount,
                'rejected' => $rejectedCount,
            ],
            'recentRequests' => $recentRequests,
            'notifications' => [
                ['id' => 1, 'message' => 'Bienvenue sur votre espace de gestion de stock.', 'type' => 'info'],
            ]
        ]);
    }
}
