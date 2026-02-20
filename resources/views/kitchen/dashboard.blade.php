@extends('layouts.kitchen')

@section('title', 'Kitchen Display')
@section('page-title', 'Kitchen Display')

@section('content')
<div class="max-w-7xl mx-auto">
    <div id="orders-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Orders loaded via AJAX -->
    </div>
</div>

@push('scripts')
<script>
    function loadOrders() {
        fetch('{{ route("kitchen.orders.list") }}')
            .then(response => response.json())
            .then(orders => {
                const container = document.getElementById('orders-container');
                container.innerHTML = '';
                orders.forEach(order => {
                    container.appendChild(createOrderCard(order));
                });
            })
            .catch(error => console.error('Error loading orders:', error));
    }

    function createOrderCard(order) {
        const card = document.createElement('div');
        const borderClass = order.status === 'confirmed' ? 'border-l-yellow-500' : order.status === 'preparing' ? 'border-l-blue-500' : 'border-l-green-500';
        const badgeClass = order.status === 'confirmed' ? 'bg-yellow-100 text-yellow-800' : order.status === 'preparing' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 border-l-4 ' + borderClass;
        const timeAgo = getTimeAgo(new Date(order.confirmed_at));
        card.innerHTML = `
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800 truncate">${order.order_number}</h3>
                    <p class="text-xs md:text-sm text-gray-600"><i class="fas fa-table mr-1"></i>${order.table.name}</p>
                    <p class="text-xs text-gray-500">${timeAgo}</p>
                </div>
                <span class="px-2 md:px-3 py-1 rounded-full text-xs font-semibold ${badgeClass} flex-shrink-0 ml-2">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
            </div>
            <div class="space-y-1 md:space-y-2 mb-3 md:mb-4">
                ${order.items.map(item => `
                    <div class="flex justify-between text-xs md:text-sm">
                        <span class="text-gray-800 flex-1 min-w-0 truncate mr-2">${item.quantity}x ${item.item_name}</span>
                        ${item.notes ? `<span class="text-xs text-gray-500 flex-shrink-0">(${item.notes})</span>` : ''}
                    </div>
                `).join('')}
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                ${order.status === 'confirmed' ? `
                    <button onclick="updateStatus(${order.id}, 'preparing')" class="flex-1 bg-blue-600 text-white px-3 md:px-4 py-2 md:py-2.5 rounded-lg hover:bg-blue-700 transition font-medium text-sm md:text-base">
                        <i class="fas fa-play mr-1"></i><span class="hidden sm:inline">Start </span>Preparing
                    </button>
                ` : ''}
                ${order.status === 'preparing' ? `
                    <button onclick="updateStatus(${order.id}, 'ready')" class="flex-1 bg-green-600 text-white px-3 md:px-4 py-2 md:py-2.5 rounded-lg hover:bg-green-700 transition font-medium text-sm md:text-base">
                        <i class="fas fa-check mr-1"></i>Mark Ready
                    </button>
                ` : ''}
            </div>
        `;
        return card;
    }

    function updateStatus(orderId, status) {
        fetch(`/kitchen/orders/${orderId}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ status })
        })
        .then(response => response.json())
        .then(data => { if (data.success) { loadOrders(); playSound(); } })
        .catch(error => console.error('Error updating status:', error));
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return seconds + ' seconds ago';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes + ' minutes ago';
        return Math.floor(minutes / 60) + ' hours ago';
    }

    function playSound() {
        const audio = new Audio('/sounds/notification.mp3');
        audio.play().catch(e => console.log('Could not play sound'));
    }

    loadOrders();
    setInterval(loadOrders, 5000);
</script>
@endpush
@endsection
