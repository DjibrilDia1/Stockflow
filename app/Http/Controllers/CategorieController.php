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
     * Affiche la liste de toutes les catÃ©gories.
     *
     * @return Response La vue Inertia avec la liste des catÃ©gories.
     */
    public function index(): Response
    {
        return Inertia::render('Categories/Index', [
            'categories' => Categorie::all(),
        ]);
    }

    /**
     * Affiche le formulaire de crÃ©ation d'une nouvelle catÃ©gorie.
     *
     * @return Response La vue Inertia pour crÃ©er une catÃ©gorie.
     */
    public function create(): Response
    {
        return Inertia::render('Categories/Create');
    }

    /**
     * Enregistre une nouvelle catÃ©gorie dans la base de donnÃ©es.
     *
     * @param  Request  $request Les donnÃ©es du formulaire de crÃ©ation.
     * @return RedirectResponse Une redirection vers la liste des catÃ©gories.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cat_nom' => 'required|string|max:255',
            'cat_code' => 'required|string|max:255|unique:categories,cat_code',
            'cat_description' => 'nullable|string',
        ]);

        Categorie::create($validated);

        return Redirect::route('categories.index');
    }

    /**
     * Affiche les dÃ©tails d'une catÃ©gorie spÃ©cifique.
     *
     * @param  Categorie  $category Le modÃ¨le de la catÃ©gorie Ã  afficher.
     * @return Response La vue Inertia avec les dÃ©tails de la catÃ©gorie.
     */
    public function show(Categorie $category): Response
    {
        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    /**
     * Affiche le formulaire de modification d'une catÃ©gorie existante.
     *
     * @param  Categorie  $category Le modÃ¨le de la catÃ©gorie Ã  modifier.
     * @return Response La vue Inertia pour modifier la catÃ©gorie.
     */
    public function edit(Categorie $category): Response
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Met Ã  jour une catÃ©gorie spÃ©cifique dans la base de donnÃ©es.
     *
     * @param  Request  $request Les nouvelles donnÃ©es du formulaire.
     * @param  Categorie  $category Le modÃ¨le de la catÃ©gorie Ã  mettre Ã  jour.
     * @return RedirectResponse Une redirection vers la liste des catÃ©gories.
     */
    public function update(Request $request, Categorie $category): RedirectResponse
    {
        $validated = $request->validate([
            'cat_nom' => 'required|string|max:255',
            'cat_code' => 'required|string|max:255|unique:categories,cat_code,' . $category->getKey() . ',cat_id',
            'cat_description' => 'nullable|string',
        ]);

        $category->update($validated);

        return Redirect::route('categories.index');
    }

    /**
     * Supprime une catÃ©gorie spÃ©cifique de la base de donnÃ©es.
     *
     * @param  Categorie  $category Le modÃ¨le de la catÃ©gorie Ã  supprimer.
     * @return RedirectResponse Une redirection vers la liste des catÃ©gories.
     */
    public function destroy(Categorie $category): RedirectResponse
    {
        $category->delete();

        return Redirect::route('categories.index');
    }
}

