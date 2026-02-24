<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalSalesToday   = Order::where('status', 'paid')->whereDate('paid_at', $today)->sum('total');
        $totalOrdersToday  = Order::whereDate('created_at', $today)->count();
        $pendingOrders     = Order::whereIn('status', ['confirmed', 'preparing', 'ready'])->count();
        $totalUsers        = User::count();

        $revenueChart = Order::where('status', 'paid')
            ->selectRaw('DATE(paid_at) as date, SUM(total) as revenue')
            ->where('paid_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topSellingItems = OrderItem::selectRaw('item_name, SUM(quantity) as total_quantity')
            ->whereDate('created_at', $today)
            ->groupBy('item_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'total_sales_today' => $totalSalesToday,
                'total_orders_today' => $totalOrdersToday,
                'pending_orders' => $pendingOrders,
                'total_users' => $totalUsers,
            ],
            'revenueChart' => $revenueChart,
            'topSellingItems' => $topSellingItems,
        ]);
    }
}
