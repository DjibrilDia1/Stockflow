<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\MouvementStock;
use App\Models\StockArticle;
use App\Models\Entrepot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MouvementsExport;
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

        $lowStockData = [];
        $movementsData = [
            'data' => [],
            'links' => [],
            'from' => null,
            'to' => null,
            'total' => 0,
        ];
        $kpiData = [
            'underThreshold' => 0,
            'lowStock' => 0,
            'outOfStock' => 0,
            'available' => 0,
            'movementsThisMonth' => 0,
            'mostConsumedItem' => [
                'name' => '-',
                'quantity' => 0,
            ],
            'totalArticles' => 0,
        ];
        $entrepots = [];
        $articles = [];

        $lowStockData = $this->getLowStockData();
        $movementsData = $this->getMovementJournalData($request);
        $kpiData = $this->getKpiData();
        $entrepots = Entrepot::all(['ent_id', 'ent_nom']);
        $articles = Article::all(['art_id', 'art_nom']);

        return Inertia::render($view, [
            'activeTab' => $tab,
            'filters' => $request->only(['article', 'entrepot', 'type', 'date_start', 'date_end']),
            'lowStockData' => $lowStockData,
            'movementsData' => $movementsData,
            'kpiData' => $kpiData,
            'entrepots' => $entrepots,
            'articles' => $articles,
        ]);
    }

    private function getLowStockData()
    {
        return StockArticle::lowStockReport();
    }

    private function getMovementJournalData(Request $request)
    {
        return MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user'])
            ->filtered($request->only(['article', 'entrepot', 'type', 'date_start', 'date_end']))
            ->orderBy('mvs_date_mouvement', 'desc')
            ->paginate(3)
            ->withQueryString();
    }

    private function getKpiData()
    {
        $totalArticles      = Article::count();
        
        $lowStockItems = StockArticle::withDetails()->lowStock()->get()->map(fn($s) => [
            'nom' => $s->item->art_nom ?? 'Inconnu',
            'qte' => $s->sta_quantite,
            'entrepot' => $s->warehouse->ent_nom ?? 'N/A'
        ]);
        
        $outOfStockItems = StockArticle::withDetails()->outOfStock()->get()->map(fn($s) => [
            'nom' => $s->item->art_nom ?? 'Inconnu',
            'qte' => $s->sta_quantite,
            'entrepot' => $s->warehouse->ent_nom ?? 'N/A'
        ]);
        
        $availableItems = StockArticle::withDetails()->available()->get()->map(fn($s) => [
            'nom' => $s->item->art_nom ?? 'Inconnu',
            'qte' => $s->sta_quantite,
            'entrepot' => $s->warehouse->ent_nom ?? 'N/A'
        ]);

        $lowStockCount      = $lowStockItems->count();
        $outOfStockCount    = $outOfStockItems->count();
        $movementsThisMonth = MouvementStock::thisMonth()->count();
        $availableCount     = $availableItems->count();

        $mostConsumedItem     = MouvementStock::mostConsumedArticle();
        $mostConsumedItemName = '-';
        if ($mostConsumedItem) {
            $article = Article::find($mostConsumedItem->mvs_art_id);
            $mostConsumedItemName = $article ? $article->art_nom : '-';
        }

        return [
            'underThreshold'   => $lowStockCount + $outOfStockCount,
            'lowStock'         => $lowStockCount,
            'outOfStock'       => $outOfStockCount,
            'available'        => $availableCount,
            'movementsThisMonth' => $movementsThisMonth,
            'mostConsumedItem' => [
                'name'     => $mostConsumedItemName,
                'quantity' => $mostConsumedItem ? $mostConsumedItem->total_qty : 0,
            ],
            'totalArticles' => $totalArticles,
            'itemsByStatus' => [
                'low' => $lowStockItems,
                'out' => $outOfStockItems,
                'available' => $availableItems,
            ]
        ];
    }

    public function exportMovements(Request $request)
    {
        return Excel::download(new MouvementsExport($request), 'journal_mouvements_' . now()->format('Y-m-d') . '.xlsx');
    }
}
