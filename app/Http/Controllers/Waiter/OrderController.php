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
    public function index(Request $request)
    {
        $orders = Order::with(['table'])
            ->where('waiter_id', auth()->id())
            ->whereNotIn('status', ['paid', 'cancelled', 'completed'])
            ->latest()
            ->get();

        if ($request->ajax()) {
            return response()->json($orders->map(fn($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'order_type' => $o->order_type,
                'status' => $o->status,
                'total' => $o->total,
                'created_at_time' => $o->created_at->format('H:i'),
                'table_name' => optional($o->table)->name ?? 'N/A'
            ]));
        }

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
            'restaurant_table_id'   => ['nullable', 'exists:restaurant_tables,id'],
            'order_type'            => ['required', 'in:dining,takeaway,delivery'],
            'delivery_address'      => ['required_if:order_type,delivery', 'nullable', 'string'],
            'customer_phone'        => ['nullable', 'string', 'max:20'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.menu_item_id'  => ['required', 'exists:menu_items,id'],
            'items.*.quantity'      => ['required', 'integer', 'min:1'],
            'items.*.notes'         => ['nullable', 'string', 'max:255'],
            'notes'                 => ['nullable', 'string'],
        ]);

        if ($request->order_type === 'dining' && !$request->restaurant_table_id) {
            return back()->withErrors(['restaurant_table_id' => 'Please select a table for Dining orders.'])->withInput();
        }

        $tableId = $request->restaurant_table_id ? (int) $request->restaurant_table_id : null;

        if ($tableId) {
            $existingActive = Order::where('restaurant_table_id', $tableId)
                ->whereNotIn('status', ['paid', 'cancelled', 'completed'])
                ->exists();

            if ($existingActive) {
                return back()
                    ->withErrors(['restaurant_table_id' => 'This table already has an active order.'])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request, $tableId) {
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
                    'is_new'       => 1,
                    'status'       => 'confirmed',
                    'added_at'     => now(),
                ];
            }

            $taxPercent = (float) (Setting::where('key', 'tax_percent')->value('value') ?? 0);
            $servicePercent = (float) (Setting::where('key', 'service_charge_percent')->value('value') ?? 0);

            $taxAmount = round(($subtotal * $taxPercent) / 100, 2);
            $serviceAmount = round(($subtotal * $servicePercent) / 100, 2);
            $total = round($subtotal + $taxAmount + $serviceAmount, 2);

            $todayOrderCount = Order::whereDate('created_at', today())->count();
            $orderNumber     = $todayOrderCount + 1;

            $order = Order::create([
                'order_number'          => (string) $orderNumber,
                'restaurant_table_id'   => $tableId,
                'waiter_id'             => auth()->id(),
                'order_type'            => $request->order_type,
                'delivery_address'      => $request->delivery_address,
                'customer_phone'        => $request->customer_phone,
                'status'                => 'confirmed',
                'confirmed_at'          => now(),
                'notes'                 => $request->notes,
                'subtotal'              => $subtotal,
                'tax_rate'              => $taxPercent,
                'tax_amount'            => $taxAmount,
                'service_charge_rate'   => $servicePercent,
                'service_charge_amount' => $serviceAmount,
                'total'                 => $total,
                'remaining_amount'      => $total,
            ]);

            foreach ($orderItems as $itemData) {
                $order->items()->create($itemData);
            }

            if ($tableId && $request->order_type === 'dining') {
                $order->table()->update(['status' => 'occupied']);
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'redirect_url' => route('waiter.orders.index')
            ]);
        }

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
        return view('waiter.orders.show', compact('order', 'categories'));
    }

    public function addItem(Request $request, Order $order)
    {
        $this->authorizeOrder($order);
    
        if (in_array($order->status, ['paid', 'cancelled', 'completed'])) {
            return response()->json(['success' => false, 'error' => 'Order is finalized.'], 422);
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
    
            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'item_name'    => $menuItem->name,
                'item_price'   => $menuItem->price,
                'quantity'     => $qty,
                'notes'        => $request->notes,
                'subtotal'     => $line,
                'is_new'       => 1,
                'added_at'     => now(),
                'status'       => 'confirmed',
            ]);
    
            $order->update([
                'modified_at' => now(),
                'modified_by' => auth()->id(),
            ]);
    
            $this->recalculate($order);
        });
    
        return response()->json([
            'success' => true,
            'message' => 'Item added.',
            'print_url' => route('admin.orders.print-kitchen', $order->id)
        ]);
    }

    public function cancel(Order $order)
    {
        $this->authorizeOrder($order);
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return back()->with('error', 'Order cannot be cancelled.');
        }
        $order->update(['status' => 'cancelled']);
        if ($order->table) {
            $order->table()->update(['status' => 'available']);
        }
        return redirect()->route('waiter.orders.index')->with('success', 'Order cancelled.');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->waiter_id === auth()->id() || auth()->user()->role === 'admin', 403);
    }

    private function recalculate(Order $order): void
    {
        $subtotal = $order->items()->sum('subtotal');
        $taxPercent = $order->tax_rate;
        $servicePercent = $order->service_charge_rate;

        $taxAmount = round(($subtotal * $taxPercent) / 100, 2);
        $serviceAmount = round(($subtotal * $servicePercent) / 100, 2);
        $total = round($subtotal + $taxAmount + $serviceAmount, 2);

        $order->update([
            'subtotal'              => $subtotal,
            'tax_amount'            => $taxAmount,
            'service_charge_amount' => $serviceAmount,
            'total'                 => $total,
            'remaining_amount'      => $total - $order->total_paid,
        ]);
    }
}
