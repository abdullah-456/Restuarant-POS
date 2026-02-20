<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->orderBy('name')->paginate(20);

        return view('admin.menu-items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        MenuItem::create($validated);

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu_item)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.edit', [
            'item' => $menu_item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MenuItem $menu_item)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($menu_item->image) {
                Storage::disk('public')->delete($menu_item->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $menu_item->update($validated);

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu_item)
    {
        if ($menu_item->image) {
            Storage::disk('public')->delete($menu_item->image);
        }

        $menu_item->delete();

        return redirect()->route('admin.menu-items.index')->with('success', 'Menu item deleted successfully.');
    }
}

