@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <div class="lg:col-span-2 space-y-4 md:space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs md:text-sm font-medium">Total Sales Today</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">Rs. {{ number_format($stats['total_sales_today'], 2) }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-2 md:p-3">
                        <i class="fas fa-money-bill-wave text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs md:text-sm font-medium">Total Orders</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['total_orders_today'] }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-2 md:p-3">
                        <i class="fas fa-shopping-cart text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs md:text-sm font-medium">Pending Orders</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['pending_orders'] }}</p>
                    </div>
                    <div class="bg-yellow-400 bg-opacity-30 rounded-full p-2 md:p-3">
                        <i class="fas fa-clock text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs md:text-sm font-medium">Active Users</p>
                        <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-2 md:p-3">
                        <i class="fas fa-users text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Revenue Chart (Last 30 Days)</h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Top Selling Items Today</h2>
            <div class="space-y-2 md:space-y-3">
                @forelse($topSellingItems as $item)
                    <div class="flex justify-between items-center py-2 md:py-3 border-b border-gray-100 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm md:text-base truncate">{{ $item->item_name ?? 'N/A' }}</p>
                            <p class="text-xs md:text-sm text-gray-500">{{ $item->total_quantity ?? 0 }} items sold</p>
                        </div>
                        <span class="px-2 md:px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs md:text-sm font-semibold flex-shrink-0 ml-2">
                            #{{ $loop->iteration }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-6 md:py-8 text-sm md:text-base">No sales today</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 lg:sticky lg:top-24">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-users w-5 md:w-6 text-blue-600 group-hover:text-blue-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">Manage Users</span>
                </a>
                <a href="{{ route('admin.tables.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-green-50 hover:bg-green-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-table w-5 md:w-6 text-green-600 group-hover:text-green-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">Manage Tables</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-folder w-5 md:w-6 text-purple-600 group-hover:text-purple-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">Manage Categories</span>
                </a>
                <a href="{{ route('admin.menu-items.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-utensils w-5 md:w-6 text-orange-600 group-hover:text-orange-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">Manage Menu Items</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-shopping-cart w-5 md:w-6 text-yellow-600 group-hover:text-yellow-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">View Orders</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-chart-bar w-5 md:w-6 text-indigo-600 group-hover:text-indigo-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">View Reports</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 md:px-4 py-2 md:py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition group text-sm md:text-base">
                    <i class="fas fa-cog w-5 md:w-6 text-gray-600 group-hover:text-gray-700"></i>
                    <span class="ml-2 md:ml-3 font-medium text-gray-700 group-hover:text-gray-900">Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const revenueData = @json($revenueChart);
if (revenueData && revenueData.length > 0) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(item => item.revenue),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toFixed(0);
                        }
                    }
                }
            }
        }
    });
}
</script>
@endpush
@endsection
