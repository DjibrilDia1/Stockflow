<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Fournisseurs/Index', [
            'suppliers' => Fournisseur::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Fournisseurs/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fou_nom' => 'required|string|max:255',
            'fou_contact_nom' => 'nullable|string|max:255',
            'fou_telephone' => 'nullable|string|max:255',
            'fou_email' => 'nullable|email|max:255',
            'fou_adresse' => 'nullable|string',
        ]);

        Fournisseur::create($validated);

        return Redirect::route('suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fournisseur $supplier): Response
    {
        return Inertia::render('Fournisseurs/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fournisseur $supplier): Response
    {
        return Inertia::render('Fournisseurs/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fournisseur $supplier)
    {
        $validated = $request->validate([
            'fou_nom' => 'required|string|max:255',
            'fou_contact_nom' => 'nullable|string|max:255',
            'fou_telephone' => 'nullable|string|max:255',
            'fou_email' => 'nullable|email|max:255',
            'fou_adresse' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return Redirect::route('suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fournisseur $supplier)
    {
        $supplier->delete();

        return Redirect::route('suppliers.index');
    }
}

