@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
    <div
        class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-end justify-between">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm shadow-md">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </form>

        <div class="flex gap-2">
            <a href="{{ route('admin.reports.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="lg:col-span-2 space-y-4 md:space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                    <p class="text-blue-100 text-xs md:text-sm font-medium">Total Sales</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">Rs.
                        {{ number_format($stats['total_sales'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                    <p class="text-green-100 text-xs md:text-sm font-medium">Total Orders</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-4 md:p-6 text-white">
                    <p class="text-yellow-100 text-xs md:text-sm font-medium">Pending Orders</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['pending_orders'] }}</p>
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 lg:sticky lg:top-24">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Summary</h2>
                <ul class="space-y-2 text-sm md:text-base text-gray-700">
                    <li>Total sales: <strong>Rs. {{ number_format($stats['total_sales'], 2) }}</strong></li>
                    <li>Total orders: <strong>{{ $stats['total_orders'] }}</strong></li>
                    <li>Pending orders: <strong>{{ $stats['pending_orders'] }}</strong></li>
                </ul>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 lg:sticky lg:top-24 mt-4">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Top Selling Items Today</h2>
                <div class="space-y-2 md:space-y-3">
                    @forelse($topSellingItems as $item)
                        <div class="flex justify-between items-center py-2 md:py-3 border-b border-gray-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm md:text-base truncate">
                                    {{ $item->item_name ?? 'N/A' }}</p>
                                <p class="text-xs md:text-sm text-gray-500">{{ $item->total_quantity ?? 0 }} items sold</p>
                            </div>
                            <span
                                class="px-2 md:px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs md:text-sm font-semibold flex-shrink-0 ml-2">
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
        document.addEventListener('DOMContentLoaded', function () {
            // Check if Chart is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded!');
                return;
            }

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

            // Check if data exists and is valid
            if (!revenueData || revenueData.length === 0) {
                console.log('No revenue data available');
                // Show a message on the canvas
                ctx.font = '14px Arial';
                ctx.fillStyle = '#666';
                ctx.textAlign = 'center';
                ctx.fillText('No data available', canvas.width / 2, canvas.height / 2);
                return;
            }

            try {
                // Prepare chart data
                const labels = revenueData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });

                const values = revenueData.map(item => parseFloat(item.revenue) || 0);

                console.log('Chart Labels:', labels);
                console.log('Chart Values:', values);

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
                                    label: function (context) {
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
                                    callback: function (value) {
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
            } catch (error) {
                console.error('Error creating chart:', error);
                // Show error on canvas
                ctx.font = '14px Arial';
                ctx.fillStyle = '#ff0000';
                ctx.textAlign = 'center';
                ctx.fillText('Error loading chart', canvas.width / 2, canvas.height / 2);
            }
        });
    </script>
@endpush