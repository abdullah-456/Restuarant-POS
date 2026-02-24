<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Return JSON list of orders that kitchen needs to handle.
     */
    public function list()
    {
        $orders = Order::with(['table', 'items'])
            ->whereIn('status', ['confirmed', 'preparing', 'ready'])
            ->oldest('confirmed_at')
            ->get()
            ->map(function ($order) {
    
                // ✅ Only show:
                // - all old items
                // - NEW items only when they are confirmed (sent to kitchen)
                $items = $order->items
                    ->filter(function ($i) {
                        if ((int)$i->is_new !== 1) return true;
                        return $i->status === 'confirmed';
                    })
                    ->values()
                    ->map(fn($i) => [
                        'item_name' => $i->item_name,
                        'quantity'  => $i->quantity,
                        'notes'     => $i->notes,
                        'is_new'    => (int) $i->is_new,
                        'added_at'  => $i->added_at ? $i->added_at->toIso8601String() : null,
                    ]);
    
                return [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'status'       => $order->status,
                    'confirmed_at' => optional($order->confirmed_at)->toIso8601String(),
                    'table'        => ['name' => optional($order->table)->name ?? 'N/A'],
                    'notes'        => $order->notes,
                    'items'        => $items,
                ];
            });
    
        return response()->json($orders);
    }

    /**
     * Update an order's status (confirmed → preparing → ready).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:preparing,ready'],
        ]);

        $allowed = [
            'confirmed' => 'preparing',
            'preparing' => 'ready',
        ];

        if (!isset($allowed[$order->status]) || $allowed[$order->status] !== $request->status) {
            return response()->json(['error' => 'Invalid status transition.'], 422);
        }

        $updates = ['status' => $request->status];

        if ($request->status === 'ready') {
            $updates['ready_at'] = now();
        }

        $order->update($updates);

        return response()->json(['success' => true, 'status' => $order->status]);
    }
}
