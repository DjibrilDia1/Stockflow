<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\DemandeSortie;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dashboard pour les gestionnaires.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Dashboard', Article::getDashboardData());
    }

    /**
     * Dashboard pour les demandeurs.
     */
    public function demandeurDashboard(): Response
    {
        $userId = Auth::id();
        
        return Inertia::render('Demandeur/Dashboard', [
            'stats' => DemandeSortie::getStatsForUser($userId),
            'recentRequests' => DemandeSortie::forUser($userId)
                ->withDetails()
                ->latest()
                ->take(5)
                ->get()
                ->map->formatForUser(),
            'notifications' => [
                ['id' => 1, 'message' => 'Bienvenue sur votre espace de gestion de stock.', 'type' => 'info'],
            ]
        ]);
    }
}
