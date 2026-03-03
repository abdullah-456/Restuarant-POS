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
        $items = MenuItem::with(['category', 'dealItems'])->orderBy('name')->paginate(20);
        $categories = Category::orderBy('name')->get();
        $all_items = MenuItem::where('is_deal', false)->orderBy('name')->get();

        return view('admin.menu-items.index', compact('items', 'categories', 'all_items'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.create', compact('categories'));
    }

    public function createDeal()
    {
        $categories = Category::orderBy('name')->get();
        $all_items = MenuItem::where('is_deal', false)->orderBy('name')->get();

        return view('admin.menu-items.create-deal', compact('categories', 'all_items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [$request->boolean('is_deal') ? 'nullable' : 'required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'is_deal' => ['sometimes', 'boolean'],
            'deal_items' => ['required_if:is_deal,1', 'array'],
            'deal_items.*.menu_item_id' => ['required_with:deal_items', 'exists:menu_items,id'],
            'deal_items.*.quantity' => ['required_with:deal_items', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_deal'] = $request->boolean('is_deal', false);

        if ($validated['is_deal']) {
            $dealsCategory = Category::firstOrCreate(['name' => 'Deals']);
            $validated['category_id'] = $dealsCategory->id;
        }

        $menu_item = MenuItem::create($validated);

        if ($validated['is_deal'] && $request->has('deal_items')) {
            foreach ($request->deal_items as $item) {
                $menu_item->dealItems()->create($item);
            }
        }

        return redirect()->route('admin.menu-items.index')->with('success', $validated['is_deal'] ? 'Deal created successfully.' : 'Menu item created successfully.');
    }

    public function edit(MenuItem $menu_item)
    {
        if ($menu_item->is_deal) {
            return $this->editDeal($menu_item);
        }

        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.edit', [
            'item' => $menu_item,
            'categories' => $categories,
        ]);
    }

    public function editDeal(MenuItem $menu_item)
    {
        $categories = Category::orderBy('name')->get();
        $all_items = MenuItem::where('is_deal', false)->orderBy('name')->get();
        $menu_item->load('dealItems');

        return view('admin.menu-items.edit-deal', [
            'item' => $menu_item,
            'categories' => $categories,
            'all_items' => $all_items
        ]);
    }

    public function update(Request $request, MenuItem $menu_item)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [$request->boolean('is_deal') ? 'nullable' : 'required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'is_deal' => ['sometimes', 'boolean'],
            'deal_items' => ['required_if:is_deal,1', 'array'],
            'deal_items.*.menu_item_id' => ['required_with:deal_items', 'exists:menu_items,id'],
            'deal_items.*.quantity' => ['required_with:deal_items', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('image')) {
            if ($menu_item->image) {
                Storage::disk('public')->delete($menu_item->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_deal'] = $request->boolean('is_deal', false);

        if ($validated['is_deal']) {
            $dealsCategory = Category::firstOrCreate(['name' => 'Deals']);
            $validated['category_id'] = $dealsCategory->id;
        }

        $menu_item->update($validated);

        if ($validated['is_deal'] && $request->has('deal_items')) {
            $menu_item->dealItems()->delete();
            foreach ($request->deal_items as $item) {
                $menu_item->dealItems()->create($item);
            }
        } elseif (!$validated['is_deal']) {
            $menu_item->dealItems()->delete();
        }

        return redirect()->route('admin.menu-items.index')->with('success', $validated['is_deal'] ? 'Deal updated successfully.' : 'Menu item updated successfully.');
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

