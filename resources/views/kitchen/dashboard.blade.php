@extends('layouts.kitchen')

@section('title', 'Kitchen Display')
@section('page-title', 'Kitchen Display')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-gray-800">Live Orders</h2>
        <button onclick="loadOrders()" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-semibold">
            <i class="fas fa-rotate-right mr-1"></i> Refresh
        </button>
    </div>

    {{-- Status columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-yellow-700">Confirmed</h3>
                <span id="count-confirmed" class="text-xs font-semibold bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full">0</span>
            </div>
            <div id="col-confirmed" class="space-y-4"></div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-blue-700">Preparing</h3>
                <span id="count-preparing" class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">0</span>
            </div>
            <div id="col-preparing" class="space-y-4"></div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-green-700">Ready</h3>
                <span id="count-ready" class="text-xs font-semibold bg-green-100 text-green-800 px-2 py-0.5 rounded-full">0</span>
            </div>
            <div id="col-ready" class="space-y-4"></div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    let isUpdating = false;

    function loadOrders() {
        fetch('{{ route("kitchen.orders.list") }}', { headers: { 'Accept': 'application/json' }})
            .then(r => r.json())
            .then(orders => {
                // Clear columns
                const colConfirmed = document.getElementById('col-confirmed');
                const colPreparing = document.getElementById('col-preparing');
                const colReady = document.getElementById('col-ready');

                colConfirmed.innerHTML = '';
                colPreparing.innerHTML = '';
                colReady.innerHTML = '';

                // Normalize: only show orders kitchen cares about
                // (adjust this if you want to show more statuses)
                const visible = (orders || []).filter(o =>
                    ['confirmed', 'preparing', 'ready'].includes(String(o.status || '').toLowerCase())
                );

                // Counters
                const counts = { confirmed: 0, preparing: 0, ready: 0 };

                visible.forEach(order => {
                    const status = String(order.status || '').toLowerCase();
                    counts[status]++;

                    const card = createOrderCard(order);

                    if (status === 'confirmed') colConfirmed.appendChild(card);
                    if (status === 'preparing') colPreparing.appendChild(card);
                    if (status === 'ready') colReady.appendChild(card);
                });

                document.getElementById('count-confirmed').textContent = counts.confirmed;
                document.getElementById('count-preparing').textContent = counts.preparing;
                document.getElementById('count-ready').textContent = counts.ready;

                // Empty states
                if (!counts.confirmed) colConfirmed.innerHTML = emptyState('No confirmed orders');
                if (!counts.preparing) colPreparing.innerHTML = emptyState('No preparing orders');
                if (!counts.ready) colReady.innerHTML = emptyState('No ready orders');
            })
            .catch(err => console.error('Error loading orders:', err));
    }

    function emptyState(text) {
        return `
            <div class="bg-white border border-gray-200 rounded-xl p-5 text-center text-gray-400">
                <i class="fas fa-inbox text-2xl mb-2"></i>
                <div class="text-sm font-semibold">${text}</div>
            </div>
        `;
    }

    function createOrderCard(order) {
        const status = String(order.status || '').toLowerCase();

        const borderClass =
            status === 'confirmed' ? 'border-l-yellow-500' :
            status === 'preparing' ? 'border-l-blue-500' :
            'border-l-green-500';

        const badgeClass =
            status === 'confirmed' ? 'bg-yellow-100 text-yellow-800' :
            status === 'preparing' ? 'bg-blue-100 text-blue-800' :
            'bg-green-100 text-green-800';

        const confirmedAt = order.confirmed_at ? new Date(order.confirmed_at) : null;
        const timeAgo = confirmedAt ? getTimeAgo(confirmedAt) : '—';

        const tableName = (order.table && order.table.name) ? order.table.name : 'No table';

        // Items list
        const allItems = (order.items || []);

const oldItems = allItems.filter(i => Number(i.is_new || 0) !== 1);
const newItems = allItems.filter(i => Number(i.is_new || 0) === 1);

const renderItem = (item) => {
    const qty = item.quantity ?? 1;
    const name = item.item_name ?? 'Item';
    const notes = item.notes ? ` <span class="text-xs text-gray-500">(${escapeHtml(item.notes)})</span>` : '';
    const isNew = Number(item.is_new || 0) === 1;
    const newBadge = isNew ? `<span class="ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-600 text-white">NEW</span>` : '';

    return `
        <div class="flex justify-between gap-2 text-sm">
            <div class="text-gray-800 min-w-0">
                <span class="font-semibold">${qty}x</span> ${escapeHtml(name)} ${newBadge} ${notes}
            </div>
        </div>
    `;
};

let itemsHtml = '';

if (oldItems.length) {
    itemsHtml += oldItems.map(renderItem).join('');
}

if (newItems.length) {
    itemsHtml += `
        <div class="my-3 flex items-center gap-2">
            <div class="h-px bg-gray-200 flex-1"></div>
            <div class="text-xs font-bold uppercase tracking-wider text-green-700 bg-green-100 px-2 py-1 rounded-full">
                Newly Added
            </div>
            <div class="h-px bg-gray-200 flex-1"></div>
        </div>
    `;
    itemsHtml += newItems.map(renderItem).join('');
}

        const actionButtons = `
            <div class="flex gap-2 mt-4">
                ${status === 'confirmed' ? `
                    <button ${isUpdating ? 'disabled' : ''} onclick="updateStatus(${order.id}, 'preparing')"
                        class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-xl hover:bg-blue-700 transition font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-play mr-1"></i> Start Preparing
                    </button>
                ` : ''}

                ${status === 'preparing' ? `
                    <button ${isUpdating ? 'disabled' : ''} onclick="updateStatus(${order.id}, 'ready')"
                        class="flex-1 bg-green-600 text-white px-4 py-2.5 rounded-xl hover:bg-green-700 transition font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-1"></i> Mark Ready
                    </button>
                ` : ''}

                ${status === 'ready' ? `
                    <button disabled
                        class="flex-1 bg-gray-100 text-gray-500 px-4 py-2.5 rounded-xl font-semibold text-sm cursor-not-allowed">
                        <i class="fas fa-bell mr-1"></i> Waiting for pickup
                    </button>
                ` : ''}
            </div>
        `;

        const card = document.createElement('div');
        card.className = `bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-5 border-l-4 ${borderClass}`;

        card.innerHTML = `
            <div class="flex justify-between items-start gap-3 mb-3">
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-gray-800 truncate">${escapeHtml(order.order_number || 'ORDER')}</h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-table mr-1"></i>${escapeHtml(tableName)}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">${timeAgo}</p>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-bold ${badgeClass} flex-shrink-0">
                    ${status.charAt(0).toUpperCase() + status.slice(1)}
                </span>
            </div>

            <div class="space-y-2">
                ${itemsHtml || `<p class="text-sm text-gray-400">No items</p>`}
            </div>

            ${actionButtons}
        `;

        return card;
    }

    function updateStatus(orderId, status) {
        if (isUpdating) return;
        isUpdating = true;

        fetch(`/kitchen/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status })
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.success) {
                playSound();
                loadOrders();
            } else {
                console.error(data);
                alert(data?.message || 'Failed to update order status');
            }
        })
        .catch(err => console.error('Error updating status:', err))
        .finally(() => {
            isUpdating = false;
        });
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 10) return 'just now';
        if (seconds < 60) return seconds + ' seconds ago';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes + ' minutes ago';
        const hours = Math.floor(minutes / 60);
        return hours + ' hours ago';
    }

    function playSound() {
        const audio = new Audio('/sounds/notification.mp3');
        audio.play().catch(() => {});
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    loadOrders();
    setInterval(loadOrders, 5000);
</script>
@endpush
@endsection