@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Orders</h2>
            <p class="text-sm text-gray-500">Overview of all orders in the system.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Statuses</option>
                @foreach(['draft','confirmed','preparing','ready','paid','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm text-gray-700">
                Filter
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm md:text-base">
            <thead>
                <tr class="bg-gray-50 text-left text-xs md:text-sm font-semibold text-gray-600">
                    <th class="px-3 md:px-4 py-2 md:py-3">Order #</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Table</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Waiter</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Status</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Total</th>
                    <th class="px-3 md:px-4 py-2 md:py-3">Created</th>
                    <th class="px-3 md:px-4 py-2 md:py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-3 md:px-4 py-2 md:py-3 font-medium text-gray-800">{{ $order->order_number }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">{{ $order->table?->name ?? '-' }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">{{ $order->waiter?->name ?? '-' }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'paid') bg-green-100 text-green-800
                                @elseif(in_array($order->status, ['confirmed','preparing','ready'])) bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-3 md:px-4 py-2 md:py-3">Rs. {{ number_format($order->total, 2) }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3 text-gray-600">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 md:px-4 py-2 md:py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs font-medium text-gray-700">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 md:px-4 py-6 text-center text-gray-500 text-sm">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection

