@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header row --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Ready for Payment</h2>
            <p class="text-sm text-gray-500 mt-0.5">Orders confirmed by kitchen, awaiting payment.</p>
        </div>
        <button onclick="toggleRecentPayments()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm shadow-sm transition">
            <i class="fas fa-history"></i> Recent Payments
        </button>
    </div>

    {{-- Pending orders grid --}}
    <div id="orders-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
        <!-- Orders loaded via AJAX -->
    </div>

    {{-- Recent Payments (hidden by default) --}}
    <div id="recent-payments-container" class="hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-clock-rotate-left text-violet-500 mr-2"></i>Recently Paid
            </h3>
            <button onclick="toggleRecentPayments()" class="text-sm text-gray-500 hover:text-gray-800 transition flex items-center gap-1">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        <div id="recent-payments-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            <!-- Paid orders loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadOrders() {
        fetch('{{ route("cashier.orders.list") }}')
            .then(response => response.json())
            .then(orders => {
                const container = document.getElementById('orders-container');
                container.innerHTML = '';
                if (orders.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-gray-200 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-inbox text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No orders ready for payment</p>
                            <p class="text-gray-400 text-sm mt-1">Orders will appear here once kitchen marks them ready.</p>
                        </div>`;
                    return;
                }
                orders.forEach(order => container.appendChild(createOrderCard(order, false)));
            })
            .catch(error => console.error('Error loading orders:', error));
    }

    function loadRecentPayments() {
        fetch('{{ route("cashier.orders.recent-payments") }}')
            .then(response => response.json())
            .then(orders => {
                const container = document.getElementById('recent-payments-list');
                container.innerHTML = '';
                if (orders.length === 0) {
                    container.innerHTML = '<p class="col-span-full py-8 text-center text-gray-400 italic">No recent payments today</p>';
                    return;
                }
                orders.forEach(order => container.appendChild(createOrderCard(order, true)));
            })
            .catch(error => console.error('Error loading recent payments:', error));
    }

    function toggleRecentPayments() {
        const container = document.getElementById('recent-payments-container');
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            loadRecentPayments();
            setTimeout(() => container.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
        } else {
            container.classList.add('hidden');
        }
    }

    function createOrderCard(order, isPaid = false) {
        const card = document.createElement('div');
        const borderColor = isPaid ? 'border-l-blue-400' : 'border-l-violet-500';
        card.className = `bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden border-l-4 ${borderColor} hover:shadow-md transition-shadow`;

        const statusBadge = isPaid
            ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full"><i class="fas fa-check"></i> PAID</span>`
            : `<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full"><i class="fas fa-circle-dot text-[8px]"></i> READY</span>`;

        const actionHtml = isPaid
            ? `<a href="/admin/orders/${order.id}/print-bill" target="_blank"
                  class="flex items-center justify-center gap-2 w-full bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 py-2.5 rounded-xl text-sm font-semibold transition">
                    <i class="fas fa-print"></i> Re-Print Invoice
               </a>`
            : `<a href="/cashier/orders/${order.id}"
                  class="flex items-center justify-center gap-2 w-full bg-violet-600 hover:bg-violet-700 text-white py-2.5 rounded-xl text-sm font-semibold transition shadow-sm shadow-violet-200">
                    <i class="fas fa-credit-card"></i> Process Payment
               </a>`;

        const itemsHtml = order.items.slice(0, 4).map(item => `
            <div class="flex justify-between items-center text-xs py-1 border-b border-gray-50 last:border-0">
                <span class="text-gray-700 truncate mr-2">${item.quantity}× ${item.item_name}</span>
                <span class="text-gray-500 flex-shrink-0">Rs. ${(item.item_price * item.quantity).toFixed(2)}</span>
            </div>
        `).join('');
        const moreItems = order.items.length > 4 ? `<p class="text-xs text-gray-400 mt-1">+${order.items.length - 4} more item(s)</p>` : '';

        card.innerHTML = `
            <div class="p-5">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-bold text-gray-900">${order.order_number}</h3>
                            ${statusBadge}
                        </div>
                        <p class="text-xs text-gray-500"><i class="fas fa-table mr-1"></i>${order.table.name} &nbsp;·&nbsp; <i class="fas fa-user mr-1"></i>${order.waiter.name}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-gray-900">Rs. ${parseFloat(order.total).toFixed(2)}</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 mb-4">
                    ${itemsHtml}
                    ${moreItems}
                </div>
                ${actionHtml}
            </div>
        `;
        return card;
    }

    @if(session('print_order_id'))
    document.addEventListener('DOMContentLoaded', function() {
        const printUrl = "{{ route('admin.orders.print-bill', session('print_order_id')) }}";
        const printWindow = window.open(printUrl, '_blank', 'width=800,height=600');
        if (printWindow) printWindow.focus();
    });
    @endif

    loadOrders();
    setInterval(loadOrders, 5000);
</script>
@endpush
