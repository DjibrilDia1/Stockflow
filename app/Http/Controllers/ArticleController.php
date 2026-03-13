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
     * Enregistre un nouvel article dans la base de donnees.
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

        Article::create($validated);

        return Redirect::route('gestionnaire.articles.index')->with('success', 'Article créé avec succès.');
    }

    /**
     * Met e jour un article specifique dans la base de donnees.
     *
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

        $item->update($validated);

        return Redirect::route('gestionnaire.articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime un article specifique de la base de donnees.
     *
     */
    public function destroy(Article $item): RedirectResponse
    {
        try {
            $item->delete();
            return Redirect::route('gestionnaire.articles.index')->with('success', 'Article supprime avec succes.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')->with('error', 'Impossible de supprimer cet article car il est lie e des mouvements de stock ou des demandes.');
        }
    }
}