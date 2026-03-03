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
        $articles = Article::all();
        $totalArticles = $articles->count();
        $totalCategories = Categorie::count();
        $totalEntrepots = Entrepot::count();
        $movementsCount = MouvementStock::count();

        // Stock value calculation
        $stockValue = Article::all()->reduce(function ($carry, $article) {
            return $carry + ($article->total_stock * ($article->art_prix_defaut ?? 0));
        }, 0);

        // Articles under threshold
        $stockAlerts = Article::all()->filter(function($article) {
            return $article->total_stock <= $article->art_seuil_alerte;
        })->map(function($article) {
            return [
                'id' => $article->art_id,
                'product' => $article->art_nom,
                'location' => 'Tous entrepôts',
                'current' => $article->total_stock,
                'threshold' => $article->art_seuil_alerte,
            ];
        })->take(5)->values();

        $underThresholdCount = Article::all()->filter(function($article) {
            return $article->total_stock <= $article->art_seuil_alerte;
        })->count();

        $recentMovements = MouvementStock::with(['item', 'warehouse', 'service'])
                                        ->latest()
                                        ->take(5)
                                        ->get()
                                        ->map(function($m) {
                                            return [
                                                'id' => $m->mvs_id,
                                                'date' => $m->mvs_date_mouvement->format('d/m/Y'),
                                                'article' => $m->item->art_nom ?? 'N/A',
                                                'type' => $m->mvs_type,
                                                'quantity' => $m->mvs_type === 'OUT' ? -$m->mvs_quantite : $m->mvs_quantite,
                                                'entrepot' => $m->warehouse->ent_nom ?? 'N/A',
                                                'service' => $m->service->ser_nom ?? 'N/A'
                                            ];
                                        });

        // Top articles consumed (based on OUT movements)
        $topArticles = MouvementStock::where('mvs_type', 'OUT')
            ->selectRaw('mvs_art_id, sum(mvs_quantite) as total_qty')
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('item')
            ->get()
            ->map(function($m) {
                return [
                    'name' => $m->item->art_nom ?? 'N/A',
                    'value' => $m->total_qty
                ];
            });

        return Inertia::render('Gestionnaire/Dashboard', [
            'stats' => [
                'underThreshold' => $underThresholdCount,
                'totalArticles' => $totalArticles,
                'movements' => $movementsCount,
                'stockValue' => $stockValue,
            ],
            'stockAlerts' => $stockAlerts,
            'recentMovements' => $recentMovements,
            'topArticles' => $topArticles,
        ]);
    }

    /**
     * Dashboard pour les demandeurs.
     */
    public function demandeurDashboard(): Response
    {
        $userId = Auth::id();
        
        $inProgressCount = DemandeSortie::where('dso_demandeur_id', $userId)
            ->whereIn('dso_statut', ['DRAFT', 'APPROVED'])
            ->count();
            
        $processedCount = DemandeSortie::where('dso_demandeur_id', $userId)
            ->where('dso_statut', 'FULFILLED')
            ->count();
            
        $rejectedCount = DemandeSortie::where('dso_demandeur_id', $userId)
            ->where('dso_statut', 'REJECTED')
            ->count();

        $recentRequests = DemandeSortie::where('dso_demandeur_id', $userId)
            ->with(['service', 'lines.item'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($d) {
                return [
                    'id' => $d->dso_id,
                    'ref' => 'DSO-' . str_pad($d->dso_id, 5, '0', STR_PAD_LEFT),
                    'service' => $d->service->ser_nom ?? 'N/A',
                    'date' => $d->dso_created_at->format('d/m/Y'),
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
