<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Items/Index', [
            'items' => Item::with('category')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Items/Create', [
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|max:255|unique:items',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'low_threshold' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'default_price' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        Item::create($validated);

        return Redirect::route('items.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): Response
    {
        // Eager load category for display
        $item->load('category');
        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item): Response
    {
        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categories' => Category::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'ref' => 'required|string|max:255|unique:items,ref,' . $item->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'low_threshold' => 'nullable|integer|min:0',
            'safety_stock' => 'nullable|integer|min:0',
            'default_price' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $item->update($validated);

        return Redirect::route('items.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return Redirect::route('items.index');
    }
}
