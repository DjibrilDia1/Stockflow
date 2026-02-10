<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class EntrepotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Entrepots/Index', [
            'warehouses' => Entrepot::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Entrepots/Create');
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ent_nom' => 'required|string|max:255',
            'ent_code' => 'required|string|max:255|unique:entrepots,ent_code',
            'ent_localisation' => 'nullable|string|max:255',
        ]);

        Entrepot::create($validated);

        return Redirect::route('warehouses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Entrepot $warehouse): Response
    {
        return Inertia::render('Entrepots/Show', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entrepot $warehouse): Response
    {
        return Inertia::render('Entrepots/Edit', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entrepot $warehouse)
    {
        $validated = $request->validate([
            'ent_nom' => 'required|string|max:255',
            'ent_code' => 'required|string|max:255|unique:entrepots,ent_code,' . $warehouse->getKey() . ',ent_id',
            'ent_localisation' => 'nullable|string|max:255',
        ]);

        $warehouse->update($validated);

        return Redirect::route('warehouses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entrepot $warehouse)
    {
        $warehouse->delete();

        return Redirect::route('warehouses.index');
    }
}

