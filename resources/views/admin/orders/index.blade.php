@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="space-y-5">
    {{-- Header & Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">All Orders</h2>
            <p class="text-sm text-gray-500 mt-0.5">Overview of all orders in the system.</p>
        </div>
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <select name="status" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                <option value="">All Statuses</option>
                @foreach(['draft' => 'Draft', 'confirmed' => 'Confirmed', 'preparing' => 'Preparing', 'ready' => 'Ready', 'paid' => 'Paid', 'cancelled' => 'Cancelled'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            @if(request('status'))
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl bg-white shadow-sm transition">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type / Table</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Waiter</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        @php
                            $statusMap = [
                                'paid'      => 'bg-green-100 text-green-800',
                                'confirmed' => 'bg-yellow-100 text-yellow-800',
                                'preparing' => 'bg-orange-100 text-orange-800',
                                'ready'     => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'draft'     => 'bg-gray-100 text-gray-700',
                            ];
                            $badge = $statusMap[$order->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900">#{{ $order->order_number }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $order->order_type }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-gray-700">{{ $order->table?->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell text-gray-600">{{ $order->waiter?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold text-gray-900">Rs. {{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-gray-500 text-xs hidden md:table-cell">{{ $order->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-600 transition" title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.print-bill', $order) }}" target="_blank"
                                       class="w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-600 transition" title="Print Bill">
                                        <i class="fas fa-file-invoice text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.print-kitchen', $order) }}" target="_blank"
                                       class="w-8 h-8 flex items-center justify-center bg-orange-50 hover:bg-orange-100 rounded-lg text-orange-600 transition" title="Print Kitchen Slip">
                                        <i class="fas fa-utensils text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <i class="fas fa-receipt text-4xl text-gray-200 mb-3 block"></i>
                                <p class="text-gray-400 font-medium">No orders found</p>
                                @if(request('status'))
                                    <p class="text-gray-400 text-sm mt-1">Try changing or clearing the status filter.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>{{ $orders->appends(request()->query())->links() }}</div>
</div>
@endsection
