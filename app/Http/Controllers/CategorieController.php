<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CategorieController extends Controller
{
    // =========================================================================
    // RÈGLES DE VALIDATION
    // =========================================================================

    /** Règles communes à store() et update(). */
    private function validationRules(?int $categoryId = null): array
    {
        $uniqueCode = 'unique:categories,cat_code' . ($categoryId ? ",{$categoryId},cat_id" : '');

        return [
            'cat_nom'         => 'required|string|max:255',
            'cat_code'        => "required|string|max:255|{$uniqueCode}",
            'cat_description' => 'nullable|string',
        ];
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /**
     * Enregistre une nouvelle catégorie.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        Categorie::create($validated);

        return Redirect::route('gestionnaire.articles.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, Categorie $category): RedirectResponse
    {
        $validated = $request->validate($this->validationRules($category->getKey()));

        $category->update($validated);

        return Redirect::route('gestionnaire.articles.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie.
     * Intercepte les violations FK si des articles y sont rattachés.
     */
    public function destroy(Categorie $category): RedirectResponse
    {
        try {
            $category->delete();

            return Redirect::route('gestionnaire.articles.index')
                ->with('success', 'Catégorie supprimée avec succès.');
        } catch (\Exception $e) {
            return Redirect::route('gestionnaire.articles.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des articles.');
        }
    }
}