<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class CategorieController extends Controller
{
    /**
     * Affiche la liste de toutes les cat�gories.
     *
     * @return Response La vue Inertia avec la liste des cat�gories.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Categories/Index', [
            'categories' => Categorie::all(),
        ]);
    }

    /**
     * Affiche le formulaire de cr�ation d'une nouvelle cat�gorie.
     *
     * @return Response La vue Inertia pour cr�er une cat�gorie.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Categories/Create');
    }

    /**
     * Enregistre une nouvelle cat�gorie dans la base de donn�es.
     *
     * @param  Request  $request Les donn�es du formulaire de cr�ation.
     * @return RedirectResponse Une redirection vers la liste des cat�gories.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cat_nom' => 'required|string|max:255',
            'cat_code' => 'required|string|max:255|unique:categories,cat_code',
            'cat_description' => 'nullable|string',
        ]);

        Categorie::create($validated);

        return Redirect::route('gestionnaire.articles.index')->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Affiche les d�tails d'une cat�gorie sp�cifique.
     *
     * @param  Categorie  $category Le mod�le de la cat�gorie � afficher.
     * @return Response La vue Inertia avec les d�tails de la cat�gorie.
     */
    public function show(Categorie $category): Response
    {
        return Inertia::render('Gestionnaire/Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une cat�gorie existante.
     *
     * @param  Categorie  $category Le mod�le de la cat�gorie � modifier.
     * @return Response La vue Inertia pour modifier la cat�gorie.
     */
    public function edit(Categorie $category): Response
    {
        return Inertia::render('Gestionnaire/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Met � jour une cat�gorie sp�cifique dans la base de donn�es.
     *
     * @param  Request  $request Les nouvelles donn�es du formulaire.
     * @param  Categorie  $category Le mod�le de la cat�gorie � mettre � jour.
     * @return RedirectResponse Une redirection vers la liste des cat�gories.
     */
    public function update(Request $request, Categorie $category): RedirectResponse
    {
        $validated = $request->validate([
            'cat_nom' => 'required|string|max:255',
            'cat_code' => 'required|string|max:255|unique:categories,cat_code,' . $category->getKey() . ',cat_id',
            'cat_description' => 'nullable|string',
        ]);

        $category->update($validated);

        return Redirect::route('gestionnaire.articles.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une cat�gorie sp�cifique de la base de donn�es.
     *
     * @param  Categorie  $category Le mod�le de la cat�gorie � supprimer.
     * @return RedirectResponse Une redirection vers la liste des cat�gories.
     */
    public function destroy(Categorie $category): RedirectResponse
    {
        try {
            $category->delete();
            return Redirect::route('gestionnaire.articles.index')->with('success', 'Cat�gorie supprim�e avec succ�s.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')->with('error', 'Impossible de supprimer cette cat�gorie car elle contient des articles.');
        }
    }
}
