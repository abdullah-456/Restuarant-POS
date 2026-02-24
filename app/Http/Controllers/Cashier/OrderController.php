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
            ->where('status', 'ready')
            ->oldest('ready_at')
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

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
            'payment_method'  => ['required', 'in:cash,card,upi,other'],
        ]);

        $amountTendered = (float) $request->amount_tendered;
        $totalDue       = (float) $order->total;

        if ($amountTendered < $totalDue) {
            return back()->withErrors(['amount_tendered' => 'Amount tendered is less than the total due ($' . number_format($totalDue, 2) . ').'])->withInput();
        }

        // Build notes with extra info
        $change      = round($amountTendered - $totalDue, 2);
        $notesParts  = [];
        if ($request->reference) $notesParts[] = 'Ref: ' . $request->reference;
        $notesParts[] = 'Tendered: $' . number_format($amountTendered, 2);
        $notesParts[] = 'Change: $' . number_format($change, 2);
        $notes = implode(' | ', $notesParts);

        // Map payment_method: 'upi' → 'mobile_payment'
        $method = match($request->payment_method) {
            'upi'   => 'mobile_payment',
            default => $request->payment_method,
        };

        // Record payment using existing schema columns
        Payment::create([
            'order_id'       => $order->id,
             'cashier_id'      => auth()->id(),
            'payment_method' => $method,
            'amount'         => $totalDue,
            'notes'          => $notes,
            // 'paid_at'        => now(),
        ]);

        // Mark order as paid
        $order->update([
            'status'           => 'paid',
            'total_paid'       => $totalDue,
            'remaining_amount' => 0,
            // 'paid_at'          => now(),
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

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Payment of $' . number_format($totalDue, 2) . ' received! Change: $' . number_format($change, 2));
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id'                     => $order->id,
            'order_number'           => $order->order_number,
            'status'                 => $order->status,
            'table'                  => ['name' => optional($order->table)->name ?? 'N/A'],
            'waiter'                 => ['name' => optional($order->waiter)->name ?? 'N/A'],
            'subtotal'               => $order->subtotal,
            'discount_amount'        => $order->discount_amount,
            'service_charge_amount'  => $order->service_charge_amount,
            'tax_amount'             => $order->tax_amount,
            'total'                  => $order->total,
            'ready_at'               => optional($order->ready_at)->toIso8601String(),
            'items'                  => $order->items->map(fn($i) => [
                'item_name'  => $i->item_name,
                'item_price' => $i->item_price,
                'quantity'   => $i->quantity,
            ]),
        ];
    }
}
