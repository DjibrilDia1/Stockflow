<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Entrepot;
use App\Models\MouvementStock;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $totalArticles = Article::count();
        $totalCategories = Categorie::count();
        $totalEntrepots = Entrepot::count();
        $recentMovements = MouvementStock::with(['item', 'warehouse', 'user'])
                                        ->latest()
                                        ->take(5)
                                        ->get();

        return Inertia::render('Gestionnaire/Dashboard', [
            'totalArticles' => $totalArticles,
            'totalCategories' => $totalCategories,
            'totalEntrepots' => $totalEntrepots,
            'recentMovements' => $recentMovements,
        ]);
    }
}
