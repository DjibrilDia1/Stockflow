<?php

namespace App\Http\Controllers;

use App\Models\WithdrawRequestLine;
use App\Models\WithdrawRequest;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class WithdrawRequestLineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('WithdrawRequestLines/Index', [
            'withdrawRequestLines' => WithdrawRequestLine::with(['withdrawRequest', 'item', 'warehouse'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('WithdrawRequestLines/Create', [
            'withdrawRequests' => WithdrawRequest::all(['id']), // Just ID for selection
            'items' => Item::all(['id', 'name', 'ref']),
            'warehouses' => Warehouse::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'withdraw_request_id' => 'required|exists:withdraw_requests,id',
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'qty_requested' => 'required|integer|min:1',
            'qty_fulfilled' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        WithdrawRequestLine::create($validated);

        return Redirect::route('withdraw-request-lines.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(WithdrawRequestLine $withdrawRequestLine): Response
    {
        $withdrawRequestLine->load(['withdrawRequest', 'item', 'warehouse']);
        return Inertia::render('WithdrawRequestLines/Show', [
            'withdrawRequestLine' => $withdrawRequestLine,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WithdrawRequestLine $withdrawRequestLine): Response
    {
        return Inertia::render('WithdrawRequestLines/Edit', [
            'withdrawRequestLine' => $withdrawRequestLine,
            'withdrawRequests' => WithdrawRequest::all(['id']),
            'items' => Item::all(['id', 'name', 'ref']),
            'warehouses' => Warehouse::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WithdrawRequestLine $withdrawRequestLine)
    {
        $validated = $request->validate([
            'withdraw_request_id' => 'required|exists:withdraw_requests,id',
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'qty_requested' => 'required|integer|min:1',
            'qty_fulfilled' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        $withdrawRequestLine->update($validated);

        return Redirect::route('withdraw-request-lines.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WithdrawRequestLine $withdrawRequestLine)
    {
        $withdrawRequestLine->delete();

        return Redirect::route('withdraw-request-lines.index');
    }
}
