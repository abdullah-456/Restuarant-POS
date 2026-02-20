@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')
@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div id="orders-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Orders loaded via AJAX -->
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

    function createOrderCard(order) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 border-l-4 border-l-green-500';
        card.innerHTML = `
            <div class="mb-3 md:mb-4">
                <h3 class="text-lg md:text-xl font-bold text-gray-800 truncate">${order.order_number}</h3>
                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-table mr-1"></i>${order.table.name}</p>
                <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-user mr-1"></i>${order.waiter.name}</p>
            </div>
            <div class="space-y-1 mb-3 md:mb-4 text-xs md:text-sm">
                ${order.items.map(item => `
                    <div class="flex justify-between text-gray-700">
                        <span class="flex-1 min-w-0 truncate mr-2">${item.quantity}x ${item.item_name}</span>
                        <span class="flex-shrink-0">$${(item.item_price * item.quantity).toFixed(2)}</span>
                    </div>
                `).join('')}
            </div>
            <div class="border-t border-gray-200 pt-2 md:pt-3 mb-3 md:mb-4 space-y-1 text-xs md:text-sm">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal:</span><span>$${parseFloat(order.subtotal).toFixed(2)}</span></div>
                ${parseFloat(order.discount_amount) > 0 ? `<div class="flex justify-between text-red-600"><span>Discount:</span><span>-$${parseFloat(order.discount_amount).toFixed(2)}</span></div>` : ''}
                ${parseFloat(order.service_charge_amount) > 0 ? `<div class="flex justify-between"><span class="text-gray-600">Service:</span><span>$${parseFloat(order.service_charge_amount).toFixed(2)}</span></div>` : ''}
                ${parseFloat(order.tax_amount) > 0 ? `<div class="flex justify-between"><span class="text-gray-600">Tax:</span><span>$${parseFloat(order.tax_amount).toFixed(2)}</span></div>` : ''}
                <div class="flex justify-between font-bold text-base md:text-lg border-t border-gray-200 pt-2 mt-2">
                    <span>Total:</span><span>$${parseFloat(order.total).toFixed(2)}</span>
                </div>
            </div>
            <a href="/cashier/orders/${order.id}" class="block w-full bg-green-600 text-white text-center px-3 md:px-4 py-2 md:py-2.5 rounded-lg hover:bg-green-700 transition font-medium text-sm md:text-base">
                <i class="fas fa-credit-card mr-2"></i>Process Payment
            </a>
        `;
        return card;
    }

    loadOrders();
    setInterval(loadOrders, 3000);
</script>
@endpush
@endsection
