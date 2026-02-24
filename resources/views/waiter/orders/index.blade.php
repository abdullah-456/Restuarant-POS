@extends('layouts.waiter')

@section('title', 'My Orders')
@section('page-title', 'My Active Orders')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
        <p class="text-sm text-gray-600">All your current active orders.</p>
        <a href="{{ route('waiter.orders.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm shadow-sm hover:bg-blue-700 transition font-medium">
            <i class="fas fa-plus mr-2"></i>New Order
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <i class="fas fa-receipt text-5xl text-gray-200 mb-4"></i>
            <p class="text-gray-500 text-lg font-medium">No active orders</p>
            <p class="text-gray-400 text-sm mt-1">Create a new order to get started.</p>
            <a href="{{ route('waiter.orders.create') }}"
               class="inline-flex items-center mt-4 px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Create Order
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($orders as $order)
                @php
                    $statusColors = [
                        'draft'     => ['bg-gray-100 text-gray-700',   'border-l-gray-400'],
                        'confirmed' => ['bg-yellow-100 text-yellow-800','border-l-yellow-500'],
                        'preparing' => ['bg-blue-100 text-blue-800',   'border-l-blue-500'],
                        'ready'     => ['bg-green-100 text-green-800', 'border-l-green-500'],
                    ];
                    $color = $statusColors[$order->status] ?? ['bg-gray-100 text-gray-700', 'border-l-gray-400'];
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 {{ $color[1] }} overflow-hidden">
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $order->order_number }}</h3>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-chair mr-1"></i>{{ optional($order->table)->name ?? 'No table' }}
                                </p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $color[0] }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                            <span class="font-bold text-gray-800">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 flex gap-2">
                        <a href="{{ route('waiter.orders.show', $order) }}"
                           class="flex-1 text-center py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        @if($order->status === 'draft')
                            <form action="{{ route('waiter.orders.confirm', $order) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                    <i class="fas fa-check mr-1"></i>Confirm
                                </button>
                            </form>
                        @endif
                        @if(in_array($order->status, ['draft', 'confirmed']))
                            <button type="button"
                                    onclick="confirmDelete('{{ route('waiter.orders.cancel', $order) }}', 'Cancel Order', 'Are you sure you want to cancel order {{ $order->order_number }}?')"
                                    class="flex-none px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Cancel form (hidden) --}}
    <form id="cancelOrderForm" method="POST" style="display:none;">
        @csrf
    </form>
</div>

@push('scripts')
<script>
function confirmDelete(url, title, message) {
    showConfirm(title, message, function() {
        const form = document.getElementById('cancelOrderForm');
        form.action = url;
        form.submit();
    });
}
</script>
@endpush
@endsection
