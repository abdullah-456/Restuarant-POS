<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function list()
    {
        $orders = Order::whereDate('created_at', today())
            ->whereIn('status', ['confirmed', 'preparing', 'ready', 'paid'])
            ->with(['table', 'items'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $hasNew = $order->items->where('is_new', 1)->isNotEmpty();
                $items = $order->items->map(fn($i) => [
                        'item_name' => $i->item_name,
                        'quantity'  => $i->quantity,
                        'notes'     => $i->notes,
                        'is_new'    => (int) $i->is_new,
                    ]);
    
                return [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'order_type'   => $order->order_type,
                    'status'       => $order->status,
                    'has_new_items'=> $hasNew,
                    'confirmed_at' => optional($order->confirmed_at)->toIso8601String(),
                    'table'        => ['name' => optional($order->table)->name ?? 'N/A'],
                    'items'        => $items,
                    'delivery_address' => $order->delivery_address,
                ];
            });
    
        return response()->json($orders);
    }

    public function markPrinted(Order $order)
    {
        $order->items()->update(['is_new' => 0]);
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => ['required', 'in:preparing,ready']]);
        $updates = ['status' => $request->status];
        if ($request->status === 'ready') {
            $updates['ready_at'] = now();
        }
        $order->update($updates);
        return response()->json(['success' => true]);
    }
}
