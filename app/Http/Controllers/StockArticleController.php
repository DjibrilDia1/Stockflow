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
     * Affiche l'état actuel des stocks pour tous les articles dans tous les entrepôts.
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
     * Affiche un message indiquant que la création directe de stock n'est pas recommandée.
     * Le stock est une conséquence des mouvements (entrées/sorties).
     *
     * @return Response La vue Inertia avec un message d'information.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Create', [
            'message' => 'La création directe de stocks n\'est pas recommandée. Le stock est mis à jour via les mouvements.',
        ]);
    }

    /**
     * Empàªche l'enregistrement direct d'un enregistrement de stock.
     * La logique métier veut que le stock soit géré via les mouvements de stock.
     *
     * @param  Request  $request
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function store(Request $request): RedirectResponse
    {
        // La création directe d'un enregistrement StockArticle n'est généralement pas effectuée.
        // Le stock est géré via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La création directe de stock n\'est pas autorisée.');
    }

    /**
     * Affiche le stock pour un article et un entrepôt spécifiques.
     *
     * @param  StockArticle  $itemStock L'enregistrement de stock spécifique.
     * @return Response La vue Inertia avec les détails du stock.
     */
    public function show(StockArticle $itemStock): Response
    {
        $itemStock->load(['item', 'warehouse']); // Pré-charge les relations pour l'élément unique
        return Inertia::render('Gestionnaire/StockArticles/Show', [
            'itemStock' => $itemStock,
        ]);
    }

    /**
     * Affiche un message indiquant que la modification directe de stock n'est pas recommandée.
     *
     * @param  StockArticle  $itemStock L'enregistrement de stock à modifier.
     * @return Response La vue Inertia avec un message d'information.
     */
    public function edit(StockArticle $itemStock): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Edit', [
            'itemStock' => $itemStock,
            'message' => 'La modification directe du stock n\'est pas recommandée. Le stock est mis à jour via les mouvements.',
        ]);
    }

    /**
     * Empàªche la mise à jour directe d'un enregistrement de stock.
     *
     * @param  Request  $request
     * @param  StockArticle  $itemStock
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function update(Request $request, StockArticle $itemStock): RedirectResponse
    {
        // La mise à jour directe d'un enregistrement StockArticle n'est généralement pas effectuée.
        // Le stock est géré via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La mise à jour directe du stock n\'est pas autorisée.');
    }

    /**
     * Empàªche la suppression directe d'un enregistrement de stock.
     *
     * @param  StockArticle  $itemStock
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function destroy(StockArticle $itemStock): RedirectResponse
    {
        // La suppression directe d'un enregistrement StockArticle n'est généralement pas effectuée.
        // Le stock est géré via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La suppression directe du stock n\'est pas autorisée.');
    }
}
