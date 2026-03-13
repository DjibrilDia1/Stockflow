<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ServiceFournisseurController extends Controller
{
    /**
     * Affiche la liste des services et fournisseurs.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Gestionnaire/Services-Fournisseurs', [
            'services'     => Service::getUsersCountAttribute(3),
            'fournisseurs' => Fournisseur::getUsersCountAttribute(3),
        ]);
    }

    // --- Services ---

    /**
     * Enregistre un nouveau service.
     */
    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'ser_nom'  => 'required|string|max:255',
            'ser_code' => 'required|string|max:255|unique:services,ser_code',
            'ser_type' => 'nullable|string|max:255',
        ]);

        Service::create($validated);
        return Redirect::back()->with('success', 'Le service a été ajouté avec succès.');
    }

    /**
     * Met à jour un service.
     */
    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'ser_nom'  => 'required|string|max:255',
            'ser_code' => 'required|string|max:255|unique:services,ser_code,' . $service->ser_id . ',ser_id',
            'ser_type' => 'nullable|string|max:255',
        ]);

        $service->update($validated);
        return Redirect::back()->with('success', 'Le service a été mis à jour avec succès.');
    }

    /**
     * Supprime un service.
     */
    public function destroyService(Service $service)
    {
        $service->delete();
        return Redirect::back()->with('success', 'Le service a été supprimé avec succès.');
    }

    // --- Fournisseurs ---
    /**
     * Enregistre un nouveau fournisseur.
     */
    public function storeFournisseur(Request $request)
    {
        $validated = $request->validate([
            'fou_nom' => 'required|string|max:255',
            'fou_email' => 'nullable|email|max:255|unique:fournisseurs,fou_email',
            'fou_telephone' => 'nullable|string|max:255',
            'fou_adresse' => 'nullable|string',
        ]);

        Fournisseur::create($validated);
        return Redirect::back()->with('success', 'Le fournisseur a été ajouté avec succès.');
    }

    /**
     * Met à jour un fournisseur.
     */
    public function updateFournisseur(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'fou_nom' => 'required|string|max:255',
            'fou_email' => 'nullable|email|max:255|unique:fournisseurs,fou_email,' . $fournisseur->fou_id . ',fou_id',
            'fou_telephone' => 'nullable|string|max:255',
            'fou_adresse' => 'nullable|string',
        ]);

        $fournisseur->update($validated);
        return Redirect::back()->with('success', 'Le fournisseur a été mis à jour avec succès.');
    }

    /**
     * Supprime un fournisseur.
     */
    public function destroyFournisseur(Fournisseur $fournisseur)
    {
        $fournisseur->delete();
        return Redirect::back()->with('success', 'Le fournisseur a été supprimé avec succès.');
    }
}
