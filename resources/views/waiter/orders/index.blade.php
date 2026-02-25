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

    <div id="orders-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Orders loaded via AJAX --}}
    </div>

    {{-- Cancel form (hidden) --}}
    <form id="cancelOrderForm" method="POST" style="display:none;">
        @csrf
    </form>
</div>

@push('scripts')
<script>
const statusColors = {
    'draft':     ['bg-gray-100 text-gray-700',   'border-l-gray-400'],
    'confirmed': ['bg-yellow-100 text-yellow-800','border-l-yellow-500'],
    'preparing': ['bg-blue-100 text-blue-800',   'border-l-blue-500'],
    'ready':     ['bg-green-100 text-green-800', 'border-l-green-500'],
};

function loadOrders() {
    fetch('{{ route("waiter.orders.index") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(orders => {
            const container = document.getElementById('orders-container');
            container.innerHTML = '';
            
            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16 bg-white rounded-xl border border-gray-200">
                        <i class="fas fa-receipt text-5xl text-gray-200 mb-4"></i>
                        <p class="text-gray-500 text-lg font-medium">No active orders</p>
                        <p class="text-gray-400 text-sm mt-1">Create a new order to get started.</p>
                    </div>`;
                return;
            }

            orders.forEach(order => {
                const colors = statusColors[order.status] || ['bg-gray-100 text-gray-700', 'border-l-gray-400'];
                const card = document.createElement('div');
                card.className = `bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 ${colors[1]} overflow-hidden`;
                
                card.innerHTML = `
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-1 mb-1">
                                    <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded bg-gray-800 text-white">
                                        ${order.order_type.toUpperCase()}
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-800">${order.order_number}</h3>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-chair mr-1"></i>${order.order_type === 'dining' ? order.table_name : 'N/A'}
                                </p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full ${colors[0]}">
                                ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">${order.created_at_time}</span>
                            <span class="font-bold text-gray-800">Rs. ${parseFloat(order.total).toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4 flex gap-2">
                        <a href="/waiter/orders/${order.id}"
                           class="flex-1 text-center py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        ${order.status === 'draft' ? `
                             <form action="/waiter/orders/${order.id}/confirm" method="POST" class="flex-1">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                    <i class="fas fa-check mr-1"></i>Confirm
                                </button>
                            </form>
                        ` : ''}
                    </div>
                `;
                container.appendChild(card);
            });
        });
}

function confirmDelete(url, title, message) {
    showConfirm(title, message, function() {
        const form = document.getElementById('cancelOrderForm');
        form.action = url;
        form.submit();
    });
}

loadOrders();
setInterval(loadOrders, 5000);
</script>
@endpush
@endsection
