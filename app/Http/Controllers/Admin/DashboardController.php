<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'total_sales_today' => 0,
                'total_orders_today' => 0,
                'pending_orders' => 0,
                'total_users' => 0,
            ],
            'revenueChart' => [],
            'topSellingItems' => [],
        ]);
    }
}
