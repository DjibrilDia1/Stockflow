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
     * Les catÃ©gories associÃ©es sont prÃ©-chargÃ©es pour optimiser les performances.
     *
     * @return Response La vue Inertia avec la liste des articles.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Articles', [
            'items' => Article::with(['category', 'itemStocks'])->paginate(3, ['*'], 'items'),
            'categories' => Categorie::paginate(3, ['*'], 'categories'),
        ]);
    }

    /**
     * Affiche le formulaire de crÃ©ation d'un nouvel article.
     * Fournit la liste des catÃ©gories pour le formulaire de sÃ©lection.
     *
     * @return Response La vue Inertia pour crÃ©er un article.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Articles/Create', [
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
        ]);
    }

    /**
     * Enregistre un nouvel article dans la base de donnÃ©es.
     *
     * @param  Request  $request Les donnÃ©es du formulaire de crÃ©ation.
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

        return Redirect::route('gestionnaire.articles.index');
    }

    /**
     * Affiche les dÃ©tails d'un article spÃ©cifique.
     *
     * @param  Article  $item Le modÃ¨le de l'article Ã  afficher.
     * @return Response La vue Inertia avec les dÃ©tails de l'article.
     */
    public function show(Article $item): Response
    {
        // PrÃ©-charge la catÃ©gorie pour l'affichage
        $item->load('category');
        return Inertia::render('Gestionnaire/Articles/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un article existant.
     *
     * @param  Article  $item Le modÃ¨le de l'article Ã  modifier.
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
     * Met Ã  jour un article spÃ©cifique dans la base de donnÃ©es.
     *
     * @param  Request  $request Les nouvelles donnÃ©es du formulaire.
     * @param  Article  $item Le modÃ¨le de l'article Ã  mettre Ã  jour.
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

        return Redirect::route('gestionnaire.articles.index');
    }

    /**
     * Supprime un article spÃ©cifique de la base de donnÃ©es.
     *
     * @param  Article  $item Le modÃ¨le de l'article Ã  supprimer.
     * @return RedirectResponse Une redirection vers la liste des articles.
     */
    public function destroy(Article $item): RedirectResponse
    {
        $item->delete();

        return Redirect::route('gestionnaire.articles.index');
    }
}

