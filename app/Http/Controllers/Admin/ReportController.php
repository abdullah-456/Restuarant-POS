<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalSalesToday = Order::whereDate('created_at', $today)->sum('total');
        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::whereIn('status', ['confirmed', 'preparing', 'ready'])->count();

        $revenueChart = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topSellingItems = OrderItem::selectRaw('item_name, SUM(quantity) as total_quantity')
            ->whereDate('created_at', $today)
            ->groupBy('item_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return view('admin.reports.index', [
            'stats' => [
                'total_sales_today' => $totalSalesToday,
                'total_orders_today' => $totalOrdersToday,
                'pending_orders' => $pendingOrders,
            ],
            'revenueChart' => $revenueChart,
            'topSellingItems' => $topSellingItems,
        ]);
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrdersExport, 'daily_sales_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPDF()
    {
        $date = date('Y-m-d');
        $orders = Order::with(['table', 'waiter'])
            ->whereDate('created_at', $date)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('orders', 'date'));
        return $pdf->download('daily_sales_' . $date . '.pdf');
    }
}

