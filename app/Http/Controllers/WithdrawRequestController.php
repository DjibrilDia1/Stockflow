<?php

namespace App\Http\Controllers;

use App\Models\WithdrawRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class WithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('WithdrawRequests/Index', [
            'withdrawRequests' => WithdrawRequest::with(['service', 'requester'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('WithdrawRequests/Create', [
            'services' => Service::all(['id', 'name']),
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
            'service_id' => 'required|exists:services,id',
            'requested_by' => 'required|exists:users,id',
            'status' => 'required|in:DRAFT,APPROVED,FULFILLED,REJECTED',
        ]);

        WithdrawRequest::create($validated);

        return Redirect::route('withdraw-requests.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(WithdrawRequest $withdrawRequest): Response
    {
        $withdrawRequest->load(['service', 'requester', 'lines.item', 'lines.warehouse']);
        return Inertia::render('WithdrawRequests/Show', [
            'withdrawRequest' => $withdrawRequest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WithdrawRequest $withdrawRequest): Response
    {
        return Inertia::render('WithdrawRequests/Edit', [
            'withdrawRequest' => $withdrawRequest,
            'services' => Service::all(['id', 'name']),
            'users' => User::all(['id', 'name']),
            'statuses' => ['DRAFT', 'APPROVED', 'FULFILLED', 'REJECTED'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WithdrawRequest $withdrawRequest)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'requested_by' => 'required|exists:users,id',
            'status' => 'required|in:DRAFT,APPROVED,FULFILLED,REJECTED',
        ]);

        $withdrawRequest->update($validated);

        return Redirect::route('withdraw-requests.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WithdrawRequest $withdrawRequest)
    {
        $withdrawRequest->delete();

        return Redirect::route('withdraw-requests.index');
    }
}
