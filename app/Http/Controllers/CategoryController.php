<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Affiche la liste de toutes les catégories.
     *
     * @return Response La vue Inertia avec la liste des catégories.
     */
    public function index(): Response
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle catégorie.
     *
     * @return Response La vue Inertia pour créer une catégorie.
     */
    public function create(): Response
    {
        return Inertia::render('Categories/Create');
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return Redirect::route('categories.index');
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     *
     * @param  Category  $category Le modèle de la catégorie à afficher.
     * @return Response La vue Inertia avec les détails de la catégorie.
     */
    public function show(Category $category): Response
    {
        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une catégorie existante.
     *
     * @param  Category  $category Le modèle de la catégorie à modifier.
     * @return Response La vue Inertia pour modifier la catégorie.
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Met à jour une catégorie spécifique dans la base de données.
     *
     * @param  Request  $request Les nouvelles données du formulaire.
     * @param  Category  $category Le modèle de la catégorie à mettre à jour.
     * @return RedirectResponse Une redirection vers la liste des catégories.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return Redirect::route('categories.index');
    }

    /**
     * Supprime une catégorie spécifique de la base de données.
     *
     * @param  Category  $category Le modèle de la catégorie à supprimer.
     * @return RedirectResponse Une redirection vers la liste des catégories.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return Redirect::route('categories.index');
    }
}
