<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::orderBy('name')->paginate(20);

        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved,cleaning'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        RestaurantTable::create($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Table created successfully.');
    }

    public function edit(RestaurantTable $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'status' => ['required', 'in:available,occupied,reserved,cleaning'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $table->update($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();

        return redirect()->route('admin.tables.index')->with('success', 'Table deleted successfully.');
    }
}

