@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex flex-wrap gap-3 items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Ready for Payment</h2>
        <div class="flex gap-2">
            <button onclick="toggleRecentPayments()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-black transition flex items-center gap-2 text-sm font-semibold shadow-sm">
                <i class="fas fa-history"></i> Recent Payments
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-8 bg-green-50 border-2 border-green-200 rounded-2xl p-6 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-green-900">Payment Successful!</h3>
                <p class="text-green-700 text-sm">{{ session('success') }}</p>
            </div>
        </div>
        <button onclick="this.parentElement.remove()" class="p-3 text-green-400 hover:text-green-600 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div id="orders-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Orders loaded via AJAX -->
    </div>

    <!-- Recent Payments Modal/Slide-over (hidden by default) -->
    <div id="recent-payments-container" class="hidden mt-12">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Recently Paid</h2>
            <button onclick="toggleRecentPayments()" class="text-sm text-gray-500 hover:text-gray-700">Close</button>
        </div>
        <div id="recent-payments-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <!-- Paid orders loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    function loadOrders() {
        fetch('{{ route("cashier.orders.list") }}')
            .then(response => response.json())
            .then(orders => {
                const container = document.getElementById('orders-container');
                container.innerHTML = '';
                if (orders.length === 0) {
                    container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8 md:py-12 bg-white rounded-lg border border-gray-200 p-4 md:p-6"><i class="fas fa-inbox text-3xl md:text-4xl mb-2 block text-gray-300"></i><span class="text-sm md:text-base">No orders ready for payment</span></p>';
                    return;
                }
                orders.forEach(order => {
                    container.appendChild(createOrderCard(order));
                });
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
                    container.innerHTML = '<p class="col-span-full py-6 text-gray-400 italic">No recent payments today</p>';
                    return;
                }
                orders.forEach(order => {
                    container.appendChild(createOrderCard(order, true));
                });
            });
    }

    function toggleRecentPayments() {
        const container = document.getElementById('recent-payments-container');
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            loadRecentPayments();
            container.scrollIntoView({ behavior: 'smooth' });
        } else {
            container.classList.add('hidden');
        }
    }

    function createOrderCard(order, isPaid = false) {
        const card = document.createElement('div');
        card.className = `bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 border-l-4 ${isPaid ? 'border-l-blue-500' : 'border-l-green-500'}`;
        
        let actionHtml = '';
        if (isPaid) {
            actionHtml = `
                <a href="/admin/orders/${order.id}/print-bill" target="_blank" class="block w-full bg-blue-600 text-white text-center px-4 py-2.5 rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    <i class="fas fa-print mr-2"></i>Re-Print Invoice
                </a>
            `;
        } else {
            actionHtml = `
                <a href="/cashier/orders/${order.id}" class="block w-full bg-green-600 text-white text-center px-4 py-2.5 rounded-lg hover:bg-green-700 transition font-medium text-sm">
                    <i class="fas fa-credit-card mr-2"></i>Process Payment
                </a>
            `;
        }

        card.innerHTML = `
            <div class="mb-3 md:mb-4">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 truncate">${order.order_number}</h3>
                    ${isPaid ? '<span class="text-[10px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded">PAID</span>' : ''}
                </div>
                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-table mr-1"></i>${order.table.name}</p>
                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-user mr-1"></i>${order.waiter.name}</p>
            </div>
            <div class="space-y-1 mb-3 md:mb-4 text-xs md:text-sm">
                ${order.items.map(item => `
                    <div class="flex justify-between text-gray-700">
                        <span class="flex-1 min-w-0 truncate mr-2">${item.quantity}x ${item.item_name}</span>
                        <span class="flex-shrink-0">Rs. ${(item.item_price * item.quantity).toFixed(2)}</span>
                    </div>
                `).join('')}
            </div>
            <div class="border-t border-gray-200 pt-2 md:pt-3 mb-3 md:mb-4 space-y-1 text-xs md:text-sm">
                <div class="flex justify-between font-bold text-base md:text-lg">
                    <span>Total:</span><span>Rs. ${parseFloat(order.total).toFixed(2)}</span>
                </div>
            </div>
            ${actionHtml}
        `;
        return card;
    }

    @if(session('print_order_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const printUrl = "{{ route('admin.orders.print-bill', session('print_order_id')) }}";
            const printWindow = window.open(printUrl, '_blank', 'width=800,height=600');
            if (printWindow) {
                printWindow.focus();
            }
        });
    </script>
    @endif

    loadOrders();
    setInterval(loadOrders, 5000);
</script>
@endpush
@endsection
