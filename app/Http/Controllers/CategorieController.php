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
     * Enregistre une nouvelle categorie dans la base de donnees.
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
     * Mettre jour une categorie specifique dans la base de donnees.
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
     * Supprime une categorie specifique de la base de donnees.
     */
    public function destroy(Categorie $category): RedirectResponse
    {
        try {
            $category->delete();
            return Redirect::route('gestionnaire.articles.index')->with('success', 'Categorie supprimee avec succes.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')->with('error', 'Impossible de supprimer cette categorie car elle contient des articles.');
        }
    }
}
