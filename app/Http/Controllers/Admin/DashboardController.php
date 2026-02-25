<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stats
        $totalSalesToday = Order::whereDate('created_at', $today)->sum('total');
        $totalOrdersToday  = Order::whereDate('created_at', $today)->count();
        $pendingOrders     = Order::whereIn('status', ['confirmed', 'preparing', 'ready'])->count();
        $totalUsers        = User::count();

        // Revenue Chart Data - Match the Reports page format (keep as collection, not array)
        $revenueChart = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // If no data, create sample data in the same object format
        if ($revenueChart->isEmpty()) {
            $revenueChart = collect();
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                // Create object with same structure as Eloquent would return
                $revenueChart->push((object)[
                    'date' => $date,
                    'revenue' => 0
                ]);
            }
        }

        // Top Selling Items Today
        $topSellingItems = OrderItem::selectRaw('item_name, SUM(quantity) as total_quantity')
            ->whereDate('created_at', $today)
            ->groupBy('item_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $stats = [
            'total_sales_today' => $totalSalesToday ?: 0,
            'total_orders_today' => $totalOrdersToday ?: 0,
            'pending_orders' => $pendingOrders ?: 0,
            'total_users' => $totalUsers ?: 0,
            'formatted_total_sales' => number_format($totalSalesToday ?: 0, 2),
        ];

        if (request()->ajax()) {
            return response()->json([
                'stats' => $stats,
                'topSellingItems' => $topSellingItems
            ]);
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'revenueChart' => $revenueChart,
            'topSellingItems' => $topSellingItems,
        ]);
    }
}