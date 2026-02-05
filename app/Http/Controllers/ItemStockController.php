<?php

namespace App\Http\Controllers;

use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ItemStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('ItemStocks/Index', [
            'itemStocks' => ItemStock::with(['item', 'warehouse'])->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * Direct creation of ItemStock records is not typically done via a dedicated form,
     * as stock levels are consequences of movements.
     */
    public function create(): Response
    {
        return Inertia::render('ItemStocks/Create', [
            'message' => 'Direct creation of item stocks is not recommended. Stock is updated via movements.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Direct storage of ItemStock records is not typically done.
        // Stock is managed via StockMovement records.
        return Redirect::route('item-stocks.index')->with('error', 'Direct creation of item stock is not allowed.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemStock $itemStock): Response
    {
        $itemStock->load(['item', 'warehouse']); // Eager load relationships for the single item
        return Inertia::render('ItemStocks/Show', [
            'itemStock' => $itemStock,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * Direct editing of ItemStock records is not typically done.
     */
    public function edit(ItemStock $itemStock): Response
    {
        return Inertia::render('ItemStocks/Edit', [
            'itemStock' => $itemStock,
            'message' => 'Direct editing of item stock is not recommended. Stock is updated via movements.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemStock $itemStock)
    {
        // Direct updating of ItemStock records is not typically done.
        // Stock is managed via StockMovement records.
        return Redirect::route('item-stocks.index')->with('error', 'Direct update of item stock is not allowed.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemStock $itemStock)
    {
        // Direct deletion of ItemStock records is not typically done.
        // Stock is managed via StockMovement records.
        return Redirect::route('item-stocks.index')->with('error', 'Direct deletion of item stock is not allowed.');
    }
}
