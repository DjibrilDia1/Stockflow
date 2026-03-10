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
    /**
     * Affiche la liste de tous les articles.
     * Les categories associees sont pre-chargees pour optimiser les performances.
     *
     * @return Response La vue Inertia avec la liste des articles.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Articles', [
            'items' => Article::with(['category', 'itemStocks.warehouse'])->paginate(3, ['*'], 'items'),
            'categories_all' => Categorie::all(['cat_id', 'cat_nom']),
            'categories' => Categorie::paginate(3, ['*'], 'categories'),
            'warehouses' => \App\Models\Entrepot::all(['ent_id', 'ent_nom']),
        ]);
    }

    /**
     * Affiche la liste des articles pour les demandeurs.
     */
    public function demandeurIndex(): Response
    {
        return Inertia::render('Demandeur/Articles', [
            'articles' => Article::with(['category', 'itemStocks.warehouse'])->get()->map(function($article) {
                return [
                    'id' => $article->art_id,
                    'code' => $article->art_reference,
                    'name' => $article->art_nom,
                    'category' => $article->category->cat_nom ?? 'N/A',
                    'stock' => $article->total_stock,
                    'status' => $article->total_stock > $article->art_seuil_alerte ? 'Disponible' : ($article->total_stock > 0 ? 'Stock bas' : 'Rupture'),
                    'warehouses' => $article->itemStocks->map(function($stock) {
                        return [
                            'id' => $stock->sta_ent_id,
                            'name' => $stock->warehouse->ent_nom ?? 'N/A',
                            'qty' => $stock->sta_quantite,
                        ];
                    }),
                ];
            }),
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
            'articlesDisponibles' => Article::with(['itemStocks.warehouse'])
                ->get()
                ->map(function($article) {
                    return [
                        'id' => $article->art_id,
                        'nom' => $article->art_nom,
                        'warehouses' => $article->itemStocks->map(function($stock) {
                            return [
                                'id' => $stock->sta_ent_id,
                                'name' => $stock->warehouse->ent_nom ?? 'N/A',
                                'qty' => $stock->sta_quantite,
                            ];
                        })->values()
                    ];
                }),
        ]);
    }

    /**
     * Affiche le formulaire de creation d'un nouvel article.
     * Fournit la liste des categories pour le formulaire de selection.
     *
     * @return Response La vue Inertia pour creer un article.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Articles/Create', [
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Enregistre un nouvel article dans la base de donnees.
     *
     * @param  Request  $request Les donnees du formulaire de creation.
     * @return RedirectResponse Une redirection vers la liste des articles.
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
     * Affiche les details d'un article specifique.
     *
     * @param  Article  $item Le modele de l'article e afficher.
     * @return Response La vue Inertia avec les details de l'article.
     */
    public function show(Article $item): Response
    {
        // Pre-charge la categorie pour l'affichage
        $item->load('category');
        return Inertia::render('Gestionnaire/Articles/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un article existant.
     *
     * @param  Article  $item Le modele de l'article e modifier.
     * @return Response La vue Inertia pour modifier l'article.
     */
    public function edit(Article $item): Response
    {
        return Inertia::render('Gestionnaire/Articles/Edit', [
            'item' => $item,
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Met e jour un article specifique dans la base de donnees.
     *
     * @param  Request  $request Les nouvelles donnees du formulaire.
     * @param  Article  $item Le modele de l'article e mettre e jour.
     * @return RedirectResponse Une redirection vers la liste des articles.
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
     * @param  Article  $item Le modele de l'article e supprimer.
     * @return RedirectResponse Une redirection vers la liste des articles.
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