@extends('layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Order Details</h2>

            <div class="space-y-1 text-sm md:text-base text-gray-700 mb-4">
                <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                <p><strong>Table:</strong> {{ $order->table?->name ?? '-' }}</p>
                <p><strong>Waiter:</strong> {{ $order->waiter?->name ?? '-' }}</p>
                <p><strong>Status:</strong>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                        @if($order->status === 'paid') bg-green-100 text-green-800
                        @elseif(in_array($order->status, ['confirmed','preparing','ready'])) bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
                <p><strong>Created:</strong> {{ $order->created_at?->format('Y-m-d H:i') }}</p>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="font-semibold text-gray-800 mb-2">Items</h3>
                <div class="space-y-2 text-sm">
                    @foreach($order->items as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->quantity }}x {{ $item->item_name }}</span>
                            <span>${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Discount:</span>
                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->service_charge_amount > 0)
                    <div class="flex justify-between">
                        <span>Service Charge:</span>
                        <span>${{ number_format($order->service_charge_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->tax_amount > 0)
                    <div class="flex justify-between">
                        <span>Tax:</span>
                        <span>${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-lg border-t border-gray-200 pt-2 mt-2">
                    <span>Total:</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Payments</h2>

            <div class="space-y-2 text-sm">
                @forelse($order->payments as $payment)
                    <div class="flex justify-between border-b border-gray-100 py-2">
                        <div>
                            <p class="font-medium">{{ ucfirst($payment->payment_method) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->paid_at?->format('Y-m-d H:i') }}</p>
                        </div>
                        <span class="font-semibold">${{ number_format($payment->amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">No payments recorded.</p>
                @endforelse
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Total Paid:</span>
                    <span>${{ number_format($order->total_paid, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t border-gray-200 pt-2 mt-2">
                    <span>Remaining:</span>
                    <span>${{ number_format($order->remaining_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

