<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    /**
     * Affiche la liste de tous les articles.
     * Les catégories associées sont pré-chargées pour optimiser les performances.
     *
     * @return Response La vue Inertia avec la liste des articles.
     */
    public function index(): Response
    {
        return Inertia::render('Items/Index', [
            'items' => Item::with('category')->get(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouvel article.
     * Fournit la liste des catégories pour le formulaire de sélection.
     *
     * @return Response La vue Inertia pour créer un article.
     */
    public function create(): Response
    {
        return Inertia::render('Items/Create', [
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    /**
     * Enregistre un nouvel article dans la base de données.
     *
     * @param  Request  $request Les données du formulaire de création.
     * @return RedirectResponse Une redirection vers la liste des articles.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ref' => 'required|string|max:255|unique:items',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'low_threshold' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'default_price' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        Item::create($validated);

        return Redirect::route('items.index');
    }

    /**
     * Affiche les détails d'un article spécifique.
     *
     * @param  Item  $item Le modèle de l'article à afficher.
     * @return Response La vue Inertia avec les détails de l'article.
     */
    public function show(Item $item): Response
    {
        // Pré-charge la catégorie pour l'affichage
        $item->load('category');
        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un article existant.
     *
     * @param  Item  $item Le modèle de l'article à modifier.
     * @return Response La vue Inertia pour modifier l'article.
     */
    public function edit(Item $item): Response
    {
        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    /**
     * Met à jour un article spécifique dans la base de données.
     *
     * @param  Request  $request Les nouvelles données du formulaire.
     * @param  Item  $item Le modèle de l'article à mettre à jour.
     * @return RedirectResponse Une redirection vers la liste des articles.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'ref' => 'required|string|max:255|unique:items,ref,' . $item->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'low_threshold' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'default_price' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $item->update($validated);

        return Redirect::route('items.index');
    }

    /**
     * Supprime un article spécifique de la base de données.
     *
     * @param  Item  $item Le modèle de l'article à supprimer.
     * @return RedirectResponse Une redirection vers la liste des articles.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return Redirect::route('items.index');
    }
}
