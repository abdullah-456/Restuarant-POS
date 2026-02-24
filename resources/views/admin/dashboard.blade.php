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
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Revenue (Last 30 Days)</h2>
            <div class="relative" style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

    
    </div>

    <div class="lg:col-span-1">
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
