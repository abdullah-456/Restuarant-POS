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
}

