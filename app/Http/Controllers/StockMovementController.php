<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule; // Added this line

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('StockMovements/Index', [
            'stockMovements' => StockMovement::with(['item', 'warehouse', 'supplier', 'service', 'user'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('StockMovements/Create', [
            'items' => Item::all(['id', 'name', 'ref']),
            'warehouses' => Warehouse::all(['id', 'name']),
            'suppliers' => Supplier::all(['id', 'name']),
            'services' => Service::all(['id', 'name']),
            'users' => User::all(['id', 'name']),
            'movementTypes' => ['IN', 'OUT', 'ADJUST', 'TRANSFER'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:IN,OUT,ADJUST,TRANSFER',
            'qty' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'service_id' => 'nullable|exists:services,id',
            'user_id' => ['nullable', Rule::exists('users', 'id')], // Modified line 61
            'linked_transfer_id' => ['nullable', Rule::exists('stock_movements', 'id')], // Modified line 63
            'attachment_url' => 'nullable|url|max:255',
            'moved_at' => 'required|date',
        ]);

        // Default user_id to current authenticated user if not provided
        if (!isset($validated['user_id'])) {
            // $validated['user_id'] = auth()->id();
            $validated['user_id'] = $request->user()->id;
        }

        StockMovement::create($validated);

        return Redirect::route('stock-movements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement): Response
    {
        $stockMovement->load(['item', 'warehouse', 'supplier', 'service', 'user']);
        return Inertia::render('StockMovements/Show', [
            'stockMovement' => $stockMovement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * Direct editing of StockMovement records is not typically done.
     */
    public function edit(StockMovement $stockMovement): Response
    {
        return Inertia::render('StockMovements/Edit', [
            'stockMovement' => $stockMovement,
            'message' => 'Direct editing of stock movements is not recommended. Movements are historical records.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        // Direct updating of StockMovement records is not typically done.
        return Redirect::route('stock-movements.index')->with('error', 'Direct update of stock movement is not allowed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        // Direct deletion of StockMovement records is not typically done.
        return Redirect::route('stock-movements.index')->with('error', 'Direct deletion of stock movement is not allowed.');
    }
}
