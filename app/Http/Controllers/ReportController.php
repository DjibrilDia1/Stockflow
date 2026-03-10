<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\MouvementStock;
use App\Models\StockArticle;
use App\Models\Entrepot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MouvementsExport;
use Illuminate\Support\Facades\DB;
use App\Enums\UserRole;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'low-stock');
        $role = $request->user()->role;
        
        $view = match ($role) {
            UserRole::GESTIONNAIRE => 'Gestionnaire/Rapports/Index',
            UserRole::RESPONSABLE => 'Responsable/Rapports/Index',
            default => 'Auth/Login'
        };

        return Inertia::render($view, [
            'activeTab' => $tab,
            'filters' => $request->only(['article', 'entrepot', 'type', 'date_start', 'date_end']),
            'lowStockData' => $this->getLowStockData(),
            'movementsData' => $this->getMovementJournalData($request),
            'kpiData' => $this->getKpiData(),
            'entrepots' => Entrepot::all(['ent_id', 'ent_nom']),
            'articles' => Article::all(['art_id', 'art_nom']),
        ]);
    }

    private function getLowStockData()
    {
        return StockArticle::join('articles', 'stocks_articles.sta_art_id', '=', 'articles.art_id')
            ->join('entrepots', 'stocks_articles.sta_ent_id', '=', 'entrepots.ent_id')
            ->where(function($query) {
                $query->whereColumn('sta_quantite', '<', 'art_seuil_alerte')
                      ->orWhereColumn('sta_quantite', '<', 'art_stock_securite');
            })
            ->select('stocks_articles.*', 'articles.art_reference', 'articles.art_nom', 'articles.art_seuil_alerte', 'articles.art_stock_securite', 'entrepots.ent_nom')
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->sta_id,
                    'reference' => $stock->art_reference,
                    'nom' => $stock->art_nom,
                    'entrepot' => $stock->ent_nom,
                    'stock_actuel' => $stock->sta_quantite,
                    'seuil_bas' => $stock->art_seuil_alerte,
                    'stock_securite' => $stock->art_stock_securite,
                    'statut' => $stock->sta_quantite < $stock->art_stock_securite ? 'Critique' : 'Alerte',
                ];
            });
    }

    private function getMovementJournalData(Request $request)
    {
        $query = MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user'])
            ->orderBy('mvs_date_mouvement', 'desc');

        if ($request->filled('article')) {
            $query->where('mvs_art_id', $request->article);
        }
        if ($request->filled('entrepot')) {
            $query->where('mvs_ent_id', $request->entrepot);
        }
        if ($request->filled('type')) {
            $query->where('mvs_type', $request->type);
        }
        if ($request->filled('date_start')) {
            $query->whereDate('mvs_date_mouvement', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('mvs_date_mouvement', '<=', $request->date_end);
        }

        return $query->paginate(10)->withQueryString();
    }

    private function getKpiData()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        // 1. Total Articles Count
        $totalArticles = Article::count();

        // 2. Low Stock Articles Count (Articles where at least one warehouse is below threshold)
        $lowStockCount = StockArticle::join('articles', 'stocks_articles.sta_art_id', '=', 'articles.art_id')
            ->whereColumn('sta_quantite', '<', 'art_seuil_alerte')
            ->where('sta_quantite', '>', 0)
            ->count();

        // 3. Out of Stock Articles Count (Articles where at least one warehouse is at 0)
        $outOfStockCount = StockArticle::where('sta_quantite', '<=', 0)->count();

        // 4. Movements This Month
        $movementsThisMonth = MouvementStock::whereBetween('mvs_date_mouvement', [$startOfMonth, $now])->count();

        // 5. Most Consumed Item
        $mostConsumedItem = MouvementStock::where('mvs_type', 'OUT')
            ->select('mvs_art_id', DB::raw('SUM(ABS(mvs_quantite)) as total_qty'))
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->first();
            
        $mostConsumedItemName = '-';
        if ($mostConsumedItem) {
            $article = Article::find($mostConsumedItem->mvs_art_id);
            $mostConsumedItemName = $article ? $article->art_nom : '-';
        }

        // 6. Available stock count (Above threshold)
        // For the sake of simplification, we consider an item "available" if its stock > threshold
        $availableCount = StockArticle::join('articles', 'stocks_articles.sta_art_id', '=', 'articles.art_id')
            ->whereColumn('sta_quantite', '>=', 'art_seuil_alerte')
            ->count();

        return [
            'underThreshold' => $lowStockCount + $outOfStockCount,
            'lowStock' => $lowStockCount,
            'outOfStock' => $outOfStockCount,
            'available' => $availableCount,
            'movementsThisMonth' => $movementsThisMonth,
            'mostConsumedItem' => [
                'name' => $mostConsumedItemName,
                'quantity' => $mostConsumedItem ? $mostConsumedItem->total_qty : 0
            ],
            'totalArticles' => $totalArticles,
        ];
    }

    public function exportMovements(Request $request)
    {
        return Excel::download(new MouvementsExport($request), 'journal_mouvements_' . now()->format('Y-m-d') . '.xlsx');
    }
}
