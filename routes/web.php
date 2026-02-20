<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Waiter\DashboardController as WaiterDashboardController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboardController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('users', UserController::class)->names('admin.users')->except('show');
    Route::resource('tables', TableController::class)->names('admin.tables')->except('show');
    Route::resource('categories', CategoryController::class)->names('admin.categories')->except('show');
    Route::resource('menu-items', MenuItemController::class)->names('admin.menu-items')->except('show');

    Route::resource('orders', OrderController::class)->names('admin.orders')->only(['index', 'show']);

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});

// Waiter routes
Route::prefix('waiter')->middleware('auth')->group(function () {
    Route::get('/dashboard', [WaiterDashboardController::class, 'index'])->name('waiter.dashboard');
    Route::get('/orders/create', function () { 
        return view('waiter.orders.create', [
            'table' => null,
            'categories' => [],
        ]); 
    })->name('waiter.orders.create');
});

// Kitchen routes
Route::prefix('kitchen')->middleware('auth')->group(function () {
    Route::get('/dashboard', [KitchenDashboardController::class, 'index'])->name('kitchen.dashboard');
    Route::get('/orders/list', function () { return response()->json([]); })->name('kitchen.orders.list');
});

// Cashier routes
Route::prefix('cashier')->middleware('auth')->group(function () {
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('cashier.dashboard');
    Route::get('/orders/list', function () { return response()->json([]); })->name('cashier.orders.list');
});
