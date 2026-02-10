<?php

namespace App\Http\Controllers;

use App\Models\DemandeSortie;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DemandeSortieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('DemandeSorties/Index', [
            'withdrawRequests' => DemandeSortie::with(['service', 'requester'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('DemandeSorties/Create', [
            'services' => Service::all(['ser_id', 'ser_nom']),
            'users' => User::all(['id', 'name']), // Assuming users can be chosen as requesters
            'statuses' => ['DRAFT', 'APPROVED', 'FULFILLED', 'REJECTED'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dso_ser_id' => 'required|exists:services,ser_id',
            'dso_demandeur_id' => 'required|exists:users,id',
            'dso_statut' => 'required|in:DRAFT,APPROVED,FULFILLED,REJECTED',
        ]);

        DemandeSortie::create($validated);

        return Redirect::route('withdraw-requests.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DemandeSortie $withdrawRequest): Response
    {
        $withdrawRequest->load(['service', 'requester', 'lines.item', 'lines.warehouse']);
        return Inertia::render('DemandeSorties/Show', [
            'withdrawRequest' => $withdrawRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DemandeSortie $withdrawRequest): Response
    {
        return Inertia::render('DemandeSorties/Edit', [
            'withdrawRequest' => $withdrawRequest,
            'services' => Service::all(['ser_id', 'ser_nom']),
            'users' => User::all(['id', 'name']),
            'statuses' => ['DRAFT', 'APPROVED', 'FULFILLED', 'REJECTED'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DemandeSortie $withdrawRequest)
    {
        $validated = $request->validate([
            'dso_ser_id' => 'required|exists:services,ser_id',
            'dso_demandeur_id' => 'required|exists:users,id',
            'dso_statut' => 'required|in:DRAFT,APPROVED,FULFILLED,REJECTED',
        ]);

        $withdrawRequest->update($validated);

        return Redirect::route('withdraw-requests.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DemandeSortie $withdrawRequest)
    {
        $withdrawRequest->delete();

        return Redirect::route('withdraw-requests.index');
    }
}

