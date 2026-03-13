<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Utilisateurs', User::getIndexData());
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:gestionnaire,demandeur,responsable',
            'ser_id' => 'required|exists:services,ser_id',
        ]);

        User::create($validated);

        return Redirect::route('gestionnaire.utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Met  jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:gestionnaire,demandeur,responsable',
            'ser_id' => 'required|exists:services,ser_id',
        ]);

        $user->update($validated);

        return Redirect::route('gestionnaire.utilisateurs.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return Redirect::back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return Redirect::route('gestionnaire.utilisateurs.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
