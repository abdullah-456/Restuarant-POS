<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['table', 'waiter'])->latest();
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        $orders = $query->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['table', 'waiter', 'items', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }
        $order->update(['status' => 'cancelled']);
        if ($order->table) {
            $order->table()->update(['status' => 'available']);
        }
        return back()->with('success', 'Order #' . $order->order_number . ' cancelled.');
    }

    public function printKitchen(Order $order, Request $request)
    {
        $order->load(['table', 'items']);
        if ($request->has('new_only')) {
            $order->setRelation('items', $order->items->where('is_new', 1));
        }
        if ($order->items->isEmpty()) {
            return "No new items to print.";
        }
        return view('admin.orders.print_kitchen', compact('order'));
    }

    public function printBill(Order $order)
    {
        $order->load(['table', 'waiter', 'items', 'payments']);
        return view('admin.orders.print_bill', compact('order'));
    }
}
