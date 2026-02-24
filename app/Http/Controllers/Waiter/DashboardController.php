<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;

class DashboardController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::where('is_active', true)
            ->with('activeOrders')
            ->orderBy('name')
            ->get();

        return view('waiter.dashboard', compact('tables'));
    }
}
