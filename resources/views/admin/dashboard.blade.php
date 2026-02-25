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
                        <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">Rs. {{ number_format($stats['total_sales_today']) }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-2 md:p-3">
                        <i class="fas fa-money-bill-wave text-xs md:text-2xl"></i>
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
            <div class="relative" style="height: 300px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
            @if(empty($revenueChart))
                <div class="text-center py-8 text-gray-500">
                    No revenue data available for the last 30 days
                </div>
            @endif
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


@endsection

@push('scripts')
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for Chart.js...');
    
    // Check if Chart is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded! Loading dynamically...');
        
        // Dynamically load Chart.js if not present
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = initializeChart;
        script.onerror = function() {
            console.error('Failed to load Chart.js');
            showMessage('Failed to load Chart.js');
        };
        document.head.appendChild(script);
    } else {
        console.log('Chart.js is loaded');
        initializeChart();
    }
});

function showMessage(message) {
    const canvas = document.getElementById('revenueChart');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.font = '14px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText(message, canvas.width/2, canvas.height/2);
    }
}

function initializeChart() {
    console.log('Initializing chart...');
    
    // Get the canvas element
    const canvas = document.getElementById('revenueChart');
    if (!canvas) {
        console.error('Canvas element not found!');
        return;
    }

    // Get the context
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Could not get canvas context!');
        return;
    }

    // Get the data
    const revenueData = @json($revenueChart);
    console.log('Revenue Data:', revenueData);

    // Check if data exists
    if (!revenueData || revenueData.length === 0) {
        console.log('No revenue data available');
        showMessage('No data available');
        return;
    }

    try {
        // Prepare chart data with proper formatting
        const labels = revenueData.map(item => {
            if (!item.date) return 'Unknown';
            const date = new Date(item.date);
            // Check if date is valid
            if (isNaN(date.getTime())) {
                return item.date; // Return as is if invalid
            }
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        
        const values = revenueData.map(item => {
            const val = parseFloat(item.revenue);
            return isNaN(val) ? 0 : val;
        });
        
        console.log('Chart Labels:', labels);
        console.log('Chart Values:', values);

        // Check if all values are zero
        const allZero = values.every(v => v === 0);
        if (allZero) {
            console.log('All revenue values are zero - showing zero line');
            // Don't return - show the chart with zero values
        }

        // Create the chart
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (Rs.)',
                    data: values,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: Rs. ' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toFixed(0);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        console.log('Chart created successfully!');
        
        // Add a note if all values are zero
        if (allZero) {
            const note = document.createElement('p');
            note.className = 'text-sm text-gray-500 mt-2 text-center';
            note.textContent = 'Note: No revenue data available for the selected period';
            canvas.parentElement.appendChild(note);
        }
    } catch (error) {
        console.error('Error creating chart:', error);
        showMessage('Error loading chart: ' + error.message);
    }
}
</script>
@endpush