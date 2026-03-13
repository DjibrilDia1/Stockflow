<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Entrepot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    /**
     * Affiche la liste de tous les articles.
     * Les categories associees sont pre-chargees pour optimiser les performances.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Articles', [
            'items'          => Article::getWithRelationsPaginated(3),
            'categories_all' => Categorie::getAllCategories(),
            'categories'     => Categorie::getWithRelationsPaginated(3),
        ]);
    }

    /**
     * Affiche la liste des articles pour les demandeurs.
     */
    public function demandeurIndex(): Response
    {
        return Inertia::render('Demandeur/Articles', [
            'articles'            => Article::forDemandeurDisplay(),
            'categories'          => Categorie::all(['cat_id', 'cat_nom']),
            'articlesDisponibles' => Article::asAvailableList(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouvel article.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Articles/Create', [
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Enregistre un nouvel article dans la base de données.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'art_reference' => 'required|string|max:255|unique:articles,art_reference|regex:/^(?![0-9]+$).+$/',
            'art_nom' => 'required|string|max:255|regex:/^(?![0-9]+$).+$/',
            'art_unite' => 'required|string|max:255|regex:/^[^0-9]+$/',
            'art_cat_id' => 'required|integer|exists:categories,cat_id',
            'art_seuil_alerte' => 'nullable|integer|min:0',
            'art_stock_securite' => 'nullable|integer|min:0',
            'art_prix_defaut' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        Article::storeFromRequest($validated);

        return Redirect::route('gestionnaire.articles.index')->with('success', 'Article créé avec succès.');
    }

    /**
     * Affiche les détails d'un article spécifique.
     */
    public function show(Article $item): Response
    {
        $item->load('category');
        return Inertia::render('Gestionnaire/Articles/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un article existant.
     */
    public function edit(Article $item): Response
    {
        return Inertia::render('Gestionnaire/Articles/Edit', [
            'item' => $item,
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Met à jour un article spécifique dans la base de données.
     */
    public function update(Request $request, Article $item): RedirectResponse
    {
        $validated = $request->validate([
            'art_reference' => 'required|string|max:255|regex:/^(?![0-9]+$).+$/|unique:articles,art_reference,' . $item->getKey() . ',art_id',
            'art_nom' => 'required|string|max:255|regex:/^(?![0-9]+$).+$/',
            'art_unite' => 'required|string|max:255|regex:/^[^0-9]+$/',
            'art_cat_id' => 'required|integer|exists:categories,cat_id',
            'art_seuil_alerte' => 'nullable|integer|min:0',
            'art_stock_securite' => 'nullable|integer|min:0',
            'art_prix_defaut' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $item->updateFromRequest($validated);

        return Redirect::route('gestionnaire.articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime un article spécifique de la base de données.
     */
    public function destroy(Article $item): RedirectResponse
    {
        try {
            $item->delete();
            return Redirect::route('gestionnaire.articles.index')->with('success', 'Article supprimé avec succès.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')->with('error', 'Impossible de supprimer cet article car il est lié à des mouvements de stock ou des demandes.');
      }
    }
}