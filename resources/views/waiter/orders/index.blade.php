@extends('layouts.waiter')

@section('title', 'My Orders')
@section('page-title', 'My Active Orders')

@push('styles')
<style>
    /* Prevent global CSS from breaking small elements */
    .order-action-btn {
        min-height: unset !important;
        min-width: unset !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-4">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-500">All your current active orders — auto-refreshes every 5 s.</p>
        <a href="{{ route('waiter.orders.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            <i class="fas fa-plus text-xs"></i> New Order
        </a>
    </div>

    {{-- Orders grid --}}
    <div id="orders-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="col-span-full flex items-center justify-center py-16 text-gray-300">
            <i class="fas fa-spinner fa-spin text-3xl"></i>
        </div>
    </div>
</div>

<form id="cancelOrderForm" method="POST" class="hidden">@csrf</form>
@endsection

@push('scripts')
<script>
const statusConfig = {
    'draft':     { badge: 'bg-gray-100 text-gray-700',    border: 'border-l-gray-400',    label: 'Draft' },
    'confirmed': { badge: 'bg-blue-100 text-blue-800',    border: 'border-l-blue-500',    label: 'Confirmed' },
    'preparing': { badge: 'bg-purple-100 text-purple-800',border: 'border-l-purple-500',  label: 'Preparing' },
    'ready':     { badge: 'bg-emerald-100 text-emerald-800', border: 'border-l-emerald-500', label: 'Ready ✓' },
};

function loadOrders() {
    fetch('{{ route("waiter.orders.index") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(orders => {
        const container = document.getElementById('orders-container');
        container.innerHTML = '';

        if (orders.length === 0) {
            container.innerHTML = `
                <div class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-gray-200 text-center">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-3">
                        <i class="fas fa-receipt text-2xl text-emerald-300"></i>
                    </div>
                    <p class="text-gray-600 font-semibold">No active orders</p>
                    <p class="text-gray-400 text-sm mt-1">Create a new order to get started.</p>
                    <a href="{{ route('waiter.orders.create') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition">
                        <i class="fas fa-plus text-xs"></i> Create Order
                    </a>
                </div>`;
            return;
        }

        orders.forEach(order => {
            const cfg  = statusConfig[order.status] || statusConfig['draft'];
            const card = document.createElement('div');
            card.className = `bg-white rounded-2xl shadow-sm border border-gray-200 border-l-4 ${cfg.border} hover:shadow-md transition-shadow overflow-hidden`;

            card.innerHTML = `
                <div class="p-3.5">
                    <div class="flex items-start justify-between gap-2 mb-2.5">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded bg-gray-800 text-white">${order.order_type.toUpperCase()}</span>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold ${cfg.badge}">${cfg.label}</span>
                            </div>
                            <h3 class="font-black text-gray-900 text-base">#${order.order_number}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                <i class="fas fa-chair mr-0.5"></i>${order.order_type === 'dining' ? order.table_name : 'N/A'}
                                &nbsp;·&nbsp;${order.created_at_time}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-[10px] text-gray-400">Total</p>
                            <p class="font-black text-gray-900 text-sm">Rs. ${parseFloat(order.total).toFixed(2)}</p>
                        </div>
                    </div>

                    <div class="flex gap-1.5">
                        <a href="/waiter/orders/${order.id}"
                           class="order-action-btn flex-1 text-center py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        ${order.status === 'draft' ? `
                            <form action="/waiter/orders/${order.id}/confirm" method="POST" style="flex:1;display:flex;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit"
                                        class="order-action-btn w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-check mr-1"></i>Confirm
                                </button>
                            </form>` : ''}
                    </div>
                </div>`;

            container.appendChild(card);
        });
    })
    .catch(err => {
        console.error('Error loading orders:', err);
        document.getElementById('orders-container').innerHTML = `
            <div class="col-span-full text-center py-12 text-red-400">
                <i class="fas fa-exclamation-circle text-3xl mb-2 block"></i>
                <p class="text-sm">Failed to load orders. Retrying…</p>
            </div>`;
    });
}

loadOrders();
setInterval(loadOrders, 5000);
</script>
@endpush
