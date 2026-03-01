<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::today();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::today()->endOfDay();

        $totalSales = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $pendingOrders = Order::whereIn('status', ['confirmed', 'preparing', 'ready'])->count();

        $revenueChart = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate->copy()->subDays(30), $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topSellingItems = OrderItem::selectRaw('item_name, SUM(quantity) as total_quantity')
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('item_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return view('admin.reports.index', [
            'stats' => [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
            ],
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'revenueChart' => $revenueChart,
            'topSellingItems' => $topSellingItems,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\OrdersExport($startDate, $endDate),
            'sales_report_' . $startDate . '_to_' . $endDate . '.xlsx'
        );
    }

    public function exportPDF(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', now()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date', now()->toDateString()))->endOfDay();

        $orders = Order::with(['table', 'waiter'])
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
            'orders' => $orders,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'totalSales' => $orders->sum('total'),
        ]);

        return $pdf->download('sales_report_' . $startDate->toDateString() . '_to_' . $endDate->toDateString() . '.pdf');
    }
}

