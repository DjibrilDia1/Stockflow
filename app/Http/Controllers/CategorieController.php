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
     * Affiche la liste de toutes les catégories.
     *
     * @return Response La vue Inertia avec la liste des catégories.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Categories/Index', [
            'categories' => Categorie::all(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle catégorie.
     *
     * @return Response La vue Inertia pour créer une catégorie.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Categories/Create');
    }

    /**
     * Enregistre une nouvelle catégorie dans la base de données.
     *
     * @param  Request  $request Les données du formulaire de création.
     * @return RedirectResponse Une redirection vers la liste des catégories.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cat_nom' => 'required|string|max:255',
            'cat_code' => 'required|string|max:255|unique:categories,cat_code',
            'cat_description' => 'nullable|string',
        ]);

        Categorie::create($validated);

        return Redirect::route('gestionnaire.articles.index');
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     *
     * @param  Categorie  $category Le modèle de la catégorie à afficher.
     * @return Response La vue Inertia avec les détails de la catégorie.
     */
    public function show(Categorie $category): Response
    {
        return Inertia::render('Gestionnaire/Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une catégorie existante.
     *
     * @param  Categorie  $category Le modèle de la catégorie à modifier.
     * @return Response La vue Inertia pour modifier la catégorie.
     */
    public function edit(Categorie $category): Response
    {
        return Inertia::render('Gestionnaire/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Met à jour une catégorie spécifique dans la base de données.
     *
     * @param  Request  $request Les nouvelles données du formulaire.
     * @param  Categorie  $category Le modèle de la catégorie à mettre à jour.
     * @return RedirectResponse Une redirection vers la liste des catégories.
     */
    public function update(Request $request, Categorie $category): RedirectResponse
    {
        $validated = $request->validate([
            'cat_nom' => 'required|string|max:255',
            'cat_code' => 'required|string|max:255|unique:categories,cat_code,' . $category->getKey() . ',cat_id',
            'cat_description' => 'nullable|string',
        ]);

        $category->update($validated);

        return Redirect::route('gestionnaire.articles.index');
    }

    /**
     * Supprime une catégorie spécifique de la base de données.
     *
     * @param  Categorie  $category Le modèle de la catégorie à supprimer.
     * @return RedirectResponse Une redirection vers la liste des catégories.
     */
    public function destroy(Categorie $category): RedirectResponse
    {
        try {
            $category->delete();
            return Redirect::route('gestionnaire.articles.index')->with('success', 'Catégorie supprimée avec succès.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')->with('error', 'Impossible de supprimer cette catégorie car elle contient des articles.');
        }
    }
}
