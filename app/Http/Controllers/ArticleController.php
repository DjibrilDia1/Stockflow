<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    // =========================================================================
    // RÈGLES DE VALIDATION
    // =========================================================================

    /** Règles communes à store() et update(). */
    private function validationRules(?int $articleId = null): array
    {
        $uniqueReference = 'unique:articles,art_reference' . ($articleId ? ",{$articleId},art_id" : '');

        return [
            'art_reference'      => "required|string|max:255|{$uniqueReference}|regex:/^(?![0-9]+\$).+\$/",
            'art_nom'            => 'required|string|max:255|regex:/^(?![0-9]+$).+$/',
            'art_unite'          => 'required|string|max:255|regex:/^[^0-9]+$/',
            'art_cat_id'         => 'required|integer|exists:categories,cat_id',
            'art_seuil_alerte'   => 'nullable|integer|min:0',
            'art_stock_securite' => 'nullable|integer|min:0',
            'art_prix_defaut'    => 'nullable|numeric|min:0|max:99999999.99',
        ];
    }

    // =========================================================================
    // VUE GESTIONNAIRE
    // =========================================================================

    /**
     * Liste paginée des articles avec catégories pour le gestionnaire.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Articles', [
            'items'          => Article::getPaginatedForManager(3),
            'categories_all' => Categorie::getAllCategories(),
            'categories'     => Categorie::getWithRelationsPaginated(3),
        ]);
    }

    /**
     * Formulaire de création d'un article.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Articles/Create', [
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Enregistre un nouvel article.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        Article::storeFromRequest($validated);

        return Redirect::route('gestionnaire.articles.index')
            ->with('success', 'Article créé avec succès.');
    }

    /**
     * Détail d'un article spécifique.
     */
    public function show(Article $item): Response
    {
        $item->load('category');

        return Inertia::render('Gestionnaire/Articles/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Formulaire de modification d'un article.
     */
    public function edit(Article $item): Response
    {
        return Inertia::render('Gestionnaire/Articles/Edit', [
            'item'       => $item,
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Met à jour un article existant.
     */
    public function update(Request $request, Article $item): RedirectResponse
    {
        $validated = $request->validate($this->validationRules($item->getKey()));

        $item->updateFromRequest($validated);

        return Redirect::route('gestionnaire.articles.index')
            ->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprime un article.
     * Intercepte les violations de contrainte FK (mouvements ou demandes liés).
     */
    public function destroy(Article $item): RedirectResponse
    {
        try {
            $item->delete();

            return Redirect::route('gestionnaire.articles.index')
                ->with('success', 'Article supprimé avec succès.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')
                ->with('error', 'Impossible de supprimer cet article car il est lié à des mouvements de stock ou des demandes.');
        }
    }

    // =========================================================================
    // VUE DEMANDEUR
    // =========================================================================

    /**
     * Liste des articles disponibles pour le demandeur (consultation + formulaire de demande).
     */
    public function demandeurIndex(): Response
    {
        return Inertia::render('Demandeur/Articles', [
            'articles'            => Article::forDemandeurDisplay(),
            'categories'          => Categorie::all(['cat_id', 'cat_nom']),
            'articlesDisponibles' => Article::asAvailableList(),
        ]);
    }
}