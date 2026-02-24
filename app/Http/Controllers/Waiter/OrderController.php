<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['table'])
            ->where('waiter_id', auth()->id())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->latest()
            ->get();

        return view('waiter.orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $tables = RestaurantTable::where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::where('is_active', true)
            ->with(['menuItems' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $selectedTable = $request->table_id
            ? RestaurantTable::find($request->table_id)
            : null;

        return view('waiter.orders.create', compact('tables', 'categories', 'selectedTable'));
    }

    public function store(Request $request)
{
    $request->validate([
        'restaurant_table_id'   => ['required', 'exists:restaurant_tables,id'],
        'items'                 => ['required', 'array', 'min:1'],
        'items.*.menu_item_id'  => ['required', 'exists:menu_items,id'],
        'items.*.quantity'      => ['required', 'integer', 'min:1'],
        'items.*.notes'         => ['nullable', 'string', 'max:255'],
        'notes'                 => ['nullable', 'string'],
    ]);

    $tableId = (int) $request->restaurant_table_id;

    // Prevent multiple active orders on same table
    $existingActive = Order::where('restaurant_table_id', $tableId)
        ->whereNotIn('status', ['paid', 'cancelled', 'completed'])
        ->exists();

    if ($existingActive) {
        return back()
            ->withErrors(['restaurant_table_id' => 'This table already has an active order. Please choose a different table.'])
            ->withInput();
    }

    DB::transaction(function () use ($request, $tableId) {

        // --- 1) Calculate subtotal from items ---
        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            $quantity = (int) $item['quantity'];

            $lineTotal = (float) $menuItem->price * $quantity;
            $subtotal += $lineTotal;

            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'item_name'    => $menuItem->name,
                'item_price'   => $menuItem->price,
                'quantity'     => $quantity,
                'notes'        => $item['notes'] ?? null,
                'subtotal'     => $lineTotal,

                // initial items are not new
                'is_new'       => 0,
                'status'       => 'confirmed',
                'added_at'     => null,
            ];
        }

        $subtotal = round($subtotal, 2);

        // --- 2) Read tax/service % from settings (defaults to 0) ---
        // Adjust keys if your settings table uses different names
        $taxPercent = (float) (Setting::where('key', 'tax_percent')->value('value') ?? 0);
        $servicePercent = (float) (Setting::where('key', 'service_charge_percent')->value('value') ?? 0);

        // --- 3) Calculate amounts ---
        $taxAmount = round(($subtotal * $taxPercent) / 100, 2);
        $serviceAmount = round(($subtotal * $servicePercent) / 100, 2);

        $total = round($subtotal + $taxAmount + $serviceAmount, 2);

        // --- 4) Create order (Option A: ONLY restaurant_table_id) ---
        $order = Order::create([
            'order_number'          => 'ORD-' . strtoupper(uniqid()),
            'restaurant_table_id'   => $tableId,
            'waiter_id'             => auth()->id(),

            'status'                => 'confirmed',
            'confirmed_at'          => now(),

            'notes'                 => $request->notes,
            'subtotal'              => $subtotal,

            'tax_rate'              => $taxPercent,
            'tax_amount'            => $taxAmount,

            'service_charge_rate'   => $servicePercent,
            'service_charge_amount' => $serviceAmount,

            'discount_percentage'   => 0,
            'discount_amount'       => 0,

            'total'                 => $total,
            'total_paid'            => 0,
            'remaining_amount'      => $total,
        ]);

        // --- 5) Create items ---
        foreach ($orderItems as $itemData) {
            $order->items()->create($itemData);
        }

        // --- 6) Occupy table ---
        $order->table()->update(['status' => 'occupied']);
    });

    return redirect()->route('waiter.orders.index')->with('success', 'Order created successfully.');
}

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load(['table', 'waiter', 'items']);
    
        $categories = \App\Models\Category::where('is_active', true)
            ->with(['menuItems' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
    
        // Debug - this will show you what's being passed to the view
        // dd([
        //     'order' => $order->toArray(),
        //     'categories' => $categories->toArray(),
        //     'categories_count' => $categories->count(),
        //     'has_items' => $categories->isNotEmpty()
        // ]);
    
        return view('waiter.orders.show', compact('order', 'categories'));
    }

    public function addItem(Request $request, Order $order)
    {
        $this->authorizeOrder($order);
    
        if (in_array($order->status, ['paid', 'cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot add items to a ' . $order->status . ' order.'
            ], 422);
        }
    
        $request->validate([
            'menu_item_id' => ['required', 'exists:menu_items,id'],
            'quantity'     => ['required', 'integer', 'min:1'],
            'notes'        => ['nullable', 'string', 'max:255'],
        ]);
    
        DB::transaction(function () use ($request, $order) {
            $menuItem = MenuItem::findOrFail($request->menu_item_id);
            $qty      = (int) $request->quantity;
            $line     = $menuItem->price * $qty;
    
            // ✅ Always mark as NEW because it came from Add Items modal
            $markNew = true;
    
            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'item_name'    => $menuItem->name,
                'item_price'   => $menuItem->price,
                'quantity'     => $qty,
                'notes'        => $request->notes,
                'subtotal'     => $line,
                'is_new'       => $markNew ? 1 : 0,
                'added_at'     => $markNew ? now() : null,
                'status'       => 'pending',
            ]);
    
            $order->update([
                'modified_at' => now(),
                'modified_by' => auth()->id(),
            ]);
    
            $this->recalculateOrder($order);
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Item added successfully.',
        ]);
    }

    private function recalculateOrder(Order $order)
    {
        // Calculate new subtotal from all items
        $subtotal = $order->items()->sum('subtotal');
        
        $serviceAmount = 0;
        $taxAmount = 0;
        $total = round($subtotal, 2);
        
        // Update order
        $order->update([
            'subtotal' => $subtotal,
            'service_charge_amount' => $serviceAmount,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'remaining_amount' => $total - $order->total_paid,
        ]);
    }

    public function removeItem(Order $order, OrderItem $item)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Cannot modify a confirmed order.');
        }

        $item->delete();
        $this->recalculate($order);

        return back()->with('success', 'Item removed.');
    }

    public function confirm(Order $order)
    {
        $this->authorizeOrder($order);
    
        if (in_array($order->status, ['paid', 'cancelled', 'completed'])) {
            return back()->with('error', 'Cannot send items for a ' . $order->status . ' order.');
        }
    
        // ✅ Only send NEW pending items
        $sent = $order->items()
            ->where('is_new', 1)
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed', // now visible to kitchen
            ]);
    
        if ($sent <= 0) {
            return back()->with('info', 'No new items to send.');
        }
    
        $order->update([
            'modified_at' => now(),
            'modified_by' => auth()->id(),
        ]);
    
        return back()->with('success', $sent . ' new item(s) sent to kitchen!');
    }

    public function cancel(Order $order)
    {
        $this->authorizeOrder($order);

        if (in_array($order->status, ['paid', 'cancelled'])) {
            return back()->with('error', 'Order cannot be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        // If no other active orders for the table, free it
        $hasOtherActive = Order::where('restaurant_table_id', $order->restaurant_table_id)
            ->where('id', '!=', $order->id)
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->exists();

        if (!$hasOtherActive) {
            $order->table()->update(['status' => 'available']);
        }

        return redirect()->route('waiter.orders.index')->with('success', 'Order cancelled.');
    }

    // ── Private helpers ─────────────────────────────────────────────────────

    private function authorizeOrder(Order $order): void
    {
        abort_unless(
            $order->waiter_id === auth()->id() || auth()->user()->role === 'admin',
            403,
            'Unauthorized'
        );
    }

    private function recalculate(Order $order): void
    {
        $subtotal = $order->items()->sum(DB::raw('item_price * quantity'));

        $serviceAmount = 0;
        $taxAmount     = 0;
        $total         = round($subtotal, 2);

        $order->update([
            'subtotal'              => $subtotal,
            'service_charge_amount' => $serviceAmount,
            'tax_amount'            => $taxAmount,
            'total'                 => $total,
            'remaining_amount'      => $total - $order->total_paid,
        ]);
    }
}
