<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Utilisateurs', [
            'users' => User::with('service')->paginate(3),
            'services' => Service::all(['ser_id', 'ser_nom']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'ser_id' => 'required|exists:services,ser_id',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'role'     => $validated['role'],
            'ser_id'   => $validated['ser_id'],
        ]);

        return Redirect::route('gestionnaire.utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'ser_id' => 'required|exists:services,ser_id',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'role'   => $validated['role'],
            'ser_id' => $validated['ser_id'],
        ];

        if ($request->filled('password')) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return Redirect::route('gestionnaire.utilisateurs.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return Redirect::back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return Redirect::route('gestionnaire.utilisateurs.index')->with('success', 'Utilisateur supprimé.');
    }
}
