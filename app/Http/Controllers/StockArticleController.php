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
     * Affiche l'�tat actuel des stocks pour tous les articles dans tous les entrep�ts.
     *
     * @return Response La vue Inertia avec la liste des stocks d'articles.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Index', [
            'itemStocks' => StockArticle::withDetails()->get(),
        ]);
    }

    /**
     * Affiche un message indiquant que la cr�ation directe de stock n'est pas recommand�e.
     * Le stock est une cons�quence des mouvements (entr�es/sorties).
     *
     * @return Response La vue Inertia avec un message d'information.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Create', [
            'message' => 'La cr�ation directe de stocks n\'est pas recommand�e. Le stock est mis � jour via les mouvements.',
        ]);
    }

    /**
     * Emp�che l'enregistrement direct d'un enregistrement de stock.
     * La logique m�tier veut que le stock soit g�r� via les mouvements de stock.
     *
     * @param  Request  $request
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function store(Request $request): RedirectResponse
    {
        // La cr�ation directe d'un enregistrement StockArticle n'est g�n�ralement pas effectu�e.
        // Le stock est g�r� via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La cr�ation directe de stock n\'est pas autoris�e.');
    }

    /**
     * Affiche le stock pour un article et un entrep�t sp�cifiques.
     *
     * @param  StockArticle  $itemStock L'enregistrement de stock sp�cifique.
     * @return Response La vue Inertia avec les d�tails du stock.
     */
    public function show(StockArticle $itemStock): Response
    {
        $itemStock->load(['item', 'warehouse']); // Pr�-charge les relations pour l'�l�ment unique
        return Inertia::render('Gestionnaire/StockArticles/Show', [
            'itemStock' => $itemStock,
        ]);
    }

    /**
     * Affiche un message indiquant que la modification directe de stock n'est pas recommand�e.
     *
     * @param  StockArticle  $itemStock L'enregistrement de stock � modifier.
     * @return Response La vue Inertia avec un message d'information.
     */
    public function edit(StockArticle $itemStock): Response
    {
        return Inertia::render('Gestionnaire/StockArticles/Edit', [
            'itemStock' => $itemStock,
            'message' => 'La modification directe du stock n\'est pas recommand�e. Le stock est mis � jour via les mouvements.',
        ]);
    }

    /**
     * Emp�che la mise � jour directe d'un enregistrement de stock.
     *
     * @param  Request  $request
     * @param  StockArticle  $itemStock
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function update(Request $request, StockArticle $itemStock): RedirectResponse
    {
        // La mise � jour directe d'un enregistrement StockArticle n'est g�n�ralement pas effectu�e.
        // Le stock est g�r� via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La mise � jour directe du stock n\'est pas autoris�e.');
    }

    /**
     * Emp�che la suppression directe d'un enregistrement de stock.
     *
     * @param  StockArticle  $itemStock
     * @return RedirectResponse Une redirection avec un message d'erreur.
     */
    public function destroy(StockArticle $itemStock): RedirectResponse
    {
        // La suppression directe d'un enregistrement StockArticle n'est g�n�ralement pas effectu�e.
        // Le stock est g�r� via les enregistrements MouvementStock.
        return Redirect::route('item-stocks.index')->with('error', 'La suppression directe du stock n\'est pas autoris�e.');
    }
}
