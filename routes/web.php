<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Waiter\DashboardController as WaiterDashboardController;
use App\Http\Controllers\Waiter\OrderController as WaiterOrderController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboardController;
use App\Http\Controllers\Kitchen\OrderController as KitchenOrderController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;

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

    Route::resource('orders', AdminOrderController::class)->names('admin.orders')->only(['index', 'show']);
    Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
    Route::get('/orders/{order}/print-kitchen', [AdminOrderController::class, 'printKitchen'])->name('admin.orders.print-kitchen');
    Route::get('/orders/{order}/print-bill', [AdminOrderController::class, 'printBill'])->name('admin.orders.print-bill');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('admin.reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('admin.reports.export.pdf');

    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});

// Waiter routes
Route::prefix('waiter')->middleware('auth')->group(function () {
    Route::get('/dashboard', [WaiterDashboardController::class, 'index'])->name('waiter.dashboard');

    Route::get('/orders', [WaiterOrderController::class, 'index'])->name('waiter.orders.index');
    Route::get('/orders/create', [WaiterOrderController::class, 'create'])->name('waiter.orders.create');
    Route::post('/orders', [WaiterOrderController::class, 'store'])->name('waiter.orders.store');
    Route::get('/orders/{order}', [WaiterOrderController::class, 'show'])->name('waiter.orders.show');
    Route::post('/orders/{order}/add-item', [WaiterOrderController::class, 'addItem'])->name('waiter.orders.add-item');
    Route::delete('/orders/{order}/remove-item/{item}', [WaiterOrderController::class, 'removeItem'])->name('waiter.orders.remove-item');
    Route::post('/orders/{order}/confirm', [WaiterOrderController::class, 'confirm'])->name('waiter.orders.confirm');
    Route::post('/orders/{order}/cancel', [WaiterOrderController::class, 'cancel'])->name('waiter.orders.cancel');
});

// Kitchen routes
Route::prefix('kitchen')->middleware('auth')->group(function () {
    Route::get('/dashboard', [KitchenDashboardController::class, 'index'])->name('kitchen.dashboard');
    Route::get('/orders/list', [KitchenOrderController::class, 'list'])->name('kitchen.orders.list');
    Route::post('/orders/{order}/status', [KitchenOrderController::class, 'updateStatus'])->name('kitchen.orders.status');
    Route::post('/orders/{order}/mark-printed', [KitchenOrderController::class, 'markPrinted'])->name('kitchen.orders.mark-printed');
});

// Cashier routes
Route::prefix('cashier')->middleware('auth')->group(function () {
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('cashier.dashboard');
    Route::get('/orders/list', [CashierOrderController::class, 'list'])->name('cashier.orders.list');
    Route::get('/orders/recent-payments', [CashierOrderController::class, 'recentPayments'])->name('cashier.orders.recent-payments');
    Route::get('/orders/{order}', [CashierOrderController::class, 'show'])->name('cashier.orders.show');
    Route::post('/orders/{order}/payment', [CashierOrderController::class, 'processPayment'])->name('cashier.orders.payment');
});
