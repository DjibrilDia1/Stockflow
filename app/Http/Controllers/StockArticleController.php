<?php

namespace App\Http\Controllers;

use App\Models\StockArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class StockArticleController extends Controller
{
    /**
     * Affiche l'Ã©tat actuel des stocks pour tous les articles dans tous les entrepÃ´ts.
     *
     * @return Response La vue Inertia avec la liste des stocks d'articles.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Index', [
            'itemStocks' => StockArticle::with(['item', 'warehouse'])->get(),
        ]);
    }

    /**
     * Affiche un message indiquant que la crÃ©ation directe de stock n'est pas recommandÃ©e.
     * Le stock est une consÃ©quence des mouvements (entrÃ©es/sorties).
     *
     * @return Response La vue Inertia avec un message d'information.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Create', [
            'message' => 'La crÃ©ation directe de stocks n\'est pas recommandÃ©e. Le stock est mis Ã  jour via les mouvements.',
        ]);
    }

    /**
     * EmpÃªche l'enregistrement direct d'un enregistrement de stock.
     * La logique mÃ©tier veut que le stock soit gÃ©rÃ© via les mouvements de stock.
     *
     * @param  Request  $request
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function store(Request $request): RedirectResponse
    {
        // La crÃ©ation directe d'un enregistrement StockArticle n'est gÃ©nÃ©ralement pas effectuÃ©e.
        // Le stock est gÃ©rÃ© via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La crÃ©ation directe de stock n\'est pas autorisÃ©e.');
    }

    /**
     * Affiche le stock pour un article et un entrepÃ´t spÃ©cifiques.
     *
     * @param  StockArticle  $itemStock L'enregistrement de stock spÃ©cifique.
     * @return Response La vue Inertia avec les dÃ©tails du stock.
     */
    public function show(StockArticle $itemStock): Response
    {
        $itemStock->load(['item', 'warehouse']); // PrÃ©-charge les relations pour l'Ã©lÃ©ment unique
        return Inertia::render('Gestionnaire/StockArticles/Show', [
            'itemStock' => $itemStock,
        ]);
    }

    /**
     * Affiche un message indiquant que la modification directe de stock n'est pas recommandÃ©e.
     *
     * @param  StockArticle  $itemStock L'enregistrement de stock Ã  modifier.
     * @return Response La vue Inertia avec un message d'information.
     */
    public function edit(StockArticle $itemStock): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Edit', [
            'itemStock' => $itemStock,
            'message' => 'La modification directe du stock n\'est pas recommandÃ©e. Le stock est mis Ã  jour via les mouvements.',
        ]);
    }

    /**
     * EmpÃªche la mise Ã  jour directe d'un enregistrement de stock.
     *
     * @param  Request  $request
     * @param  StockArticle  $itemStock
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function update(Request $request, StockArticle $itemStock): RedirectResponse
    {
        // La mise Ã  jour directe d'un enregistrement StockArticle n'est gÃ©nÃ©ralement pas effectuÃ©e.
        // Le stock est gÃ©rÃ© via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La mise Ã  jour directe du stock n\'est pas autorisÃ©e.');
    }

    /**
     * EmpÃªche la suppression directe d'un enregistrement de stock.
     *
     * @param  StockArticle  $itemStock
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function destroy(StockArticle $itemStock): RedirectResponse
    {
        // La suppression directe d'un enregistrement StockArticle n'est gÃ©nÃ©ralement pas effectuÃ©e.
        // Le stock est gÃ©rÃ© via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La suppression directe du stock n\'est pas autorisÃ©e.');
    }
}

