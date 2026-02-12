<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Gestionnaire/Services/Index', [
            'services' => Service::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Gestionnaire/Services/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ser_nom' => 'required|string|max:255',
            'ser_code' => 'required|string|max:255|unique:services,ser_code',
            'ser_type' => 'nullable|string|max:255',
        ]);

        Service::create($validated);

        return Redirect::route('services.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): Response
    {
        return Inertia::render('Gestionnaire/Services/Show', [
            'service' => $service,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): Response
    {
        return Inertia::render('Gestionnaire/Services/Edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'ser_nom' => 'required|string|max:255',
            'ser_code' => 'required|string|max:255|unique:services,ser_code,' . $service->getKey() . ',ser_id',
            'ser_type' => 'nullable|string|max:255',
        ]);

        $service->update($validated);

        return Redirect::route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return Redirect::route('services.index');
    }
}
