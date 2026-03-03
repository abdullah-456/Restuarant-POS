<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Return JSON list of orders ready for payment.
     */
    public function list()
    {
        $orders = Order::with(['table', 'waiter', 'items'])
            ->whereIn('status', ['confirmed', 'preparing', 'ready'])
            ->oldest('confirmed_at')
            ->get()
            ->map(fn(Order $order) => $this->formatOrder($order));

        return response()->json($orders);
    }

    public function recentPayments()
    {
        $orders = Order::with(['table', 'waiter', 'items'])
            ->where('status', 'paid')
            ->whereDate('created_at', today())
            ->latest('updated_at')
            ->limit(15)
            ->get()
            ->map(fn(Order $order) => $this->formatOrder($order));

        return response()->json($orders);
    }

    /**
     * Show payment processing page for a specific order.
     */
    public function show(Order $order)
    {
        if (!in_array($order->status, ['ready', 'confirmed', 'preparing'])) {
            return redirect()->route('cashier.dashboard')->with('error', 'This order is not ready for payment.');
        }

        $order->load(['table', 'waiter', 'items']);

        return view('cashier.orders.show', compact('order'));
    }

    /**
     * Process payment for an order.
     */
    public function processPayment(Request $request, Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('error', 'This order is already paid.');
        }

        $request->validate([
            'amount_tendered' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,online'],
        ]);

        $amountTendered = (float) $request->amount_tendered;
        $totalDue = (float) $order->total;

        if ($amountTendered < $totalDue) {
            return back()->withErrors(['amount_tendered' => 'Amount tendered is less than the total due (Rs. ' . number_format($totalDue, 2) . ').'])->withInput();
        }

        // Build notes with extra info
        $change = round($amountTendered - $totalDue, 2);
        $notesParts = [];
        if ($request->reference)
            $notesParts[] = 'Ref: ' . $request->reference;
        $notesParts[] = 'Tendered: Rs. ' . number_format($amountTendered, 2);
        $notesParts[] = 'Change: Rs. ' . number_format($change, 2);
        $notes = implode(' | ', $notesParts);

        // Record payment using existing schema columns
        Payment::create([
            'order_id' => $order->id,
            'cashier_id' => auth()->id(),
            'payment_method' => $request->payment_method,
            'amount' => $totalDue,
            'notes' => $notes,
        ]);

        // Mark order as paid
        $order->update([
            'status' => 'paid',
            'total_paid' => $totalDue,
            'remaining_amount' => 0,
            'paid_at' => now(),
        ]);

        // Free the table if no other active orders
        if ($order->table) {
            $hasOtherActive = Order::where('restaurant_table_id', $order->restaurant_table_id)
                ->where('id', '!=', $order->id)
                ->whereNotIn('status', ['paid', 'cancelled'])
                ->exists();

            if (!$hasOtherActive) {
                $order->table()->update(['status' => 'available']);
            }
        }
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment received successfully!',
                'print_order_id' => $order->id
            ]);
        }

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Payment of Rs. ' . number_format($totalDue, 2) . ' received! Change: Rs. ' . number_format($change, 2))
            ->with('print_order_id', $order->id);
    }

    public function updateItem(Request $request, Order $order, \App\Models\OrderItem $item)
    {
        if ($order->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Cannot edit paid order.'], 422);
        }

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item->update([
            'quantity' => $request->quantity,
            'subtotal' => $item->item_price * $request->quantity,
        ]);

        $this->recalculate($order);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully!',
            'order' => $this->formatOrder($order->fresh(['items', 'table']))
        ]);
    }

    public function removeItem(Order $order, \App\Models\OrderItem $item)
    {
        if ($order->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Cannot edit paid order.'], 422);
        }

        $item->delete();
        $this->recalculate($order);

        if ($order->items()->count() === 0) {
            $order->update(['status' => 'cancelled']);
            if ($order->table) {
                $order->table()->update(['status' => 'available']);
            }
            return response()->json([
                'success' => true,
                'message' => 'Last item removed. Order cancelled.',
                'redirect' => route('cashier.dashboard')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed successfully!',
            'order' => $this->formatOrder($order->fresh(['items', 'table']))
        ]);
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
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'service_charge_amount' => $serviceAmount,
            'total' => $total,
            'remaining_amount' => $total - $order->total_paid,
        ]);
    }

    private function formatOrder(Order $order): array
    {
        $order->loadMissing('items.menuItem.dealItems.menuItem');
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'order_type' => $order->order_type,
            'status' => $order->status,
            'table' => ['name' => optional($order->table)->name ?? 'N/A'],
            'waiter' => ['name' => optional($order->waiter)->name ?? 'N/A'],
            'subtotal' => $order->subtotal,
            'discount_amount' => $order->discount_amount,
            'service_charge_amount' => $order->service_charge_amount,
            'tax_amount' => $order->tax_amount,
            'total' => $order->total,
            'ready_at' => optional($order->ready_at)?->toIso8601String(),
            'items' => $order->items->map(fn($i) => [
                'id' => $i->id,
                'item_name' => $i->item_name,
                'item_price' => $i->item_price,
                'quantity' => $i->quantity,
                'subtotal' => $i->subtotal,
                'is_deal' => (bool) ($i->menuItem?->is_deal),
                'deal_constituents' => ($i->menuItem && $i->menuItem->is_deal) ? $i->menuItem->dealItems->map(fn($di) => [
                    'name' => $di->menuItem->name,
                    'quantity' => $di->quantity
                ])->toArray() : []
            ]),
        ];
    }
}
