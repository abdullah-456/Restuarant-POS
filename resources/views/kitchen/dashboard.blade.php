@extends('layouts.kitchen')

@section('title', 'Kitchen Display')
@section('page-title', 'Kitchen Display')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header bar --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-orange-100 text-orange-700 px-4 py-2 rounded-xl font-semibold text-sm">
                <i class="fas fa-fire-burner"></i>
                <span>Live Orders</span>
                <span id="count-current" class="bg-orange-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">0</span>
            </div>
        </div>
        <button onclick="loadOrders()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl text-sm font-semibold text-gray-700 shadow-sm transition">
            <i class="fas fa-rotate-right"></i> Refresh
        </button>
    </div>

    {{-- Orders Grid --}}
    <div id="col-current" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        {{-- Orders injected here --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
    let isUpdating = false;
    let printedOrderIds = new Set();

    function loadOrders() {
        if (isUpdating) return;

        fetch('{{ route("kitchen.orders.list") }}', { headers: { 'Accept': 'application/json' }})
            .then(r => r.json())
            .then(orders => {
                const container = document.getElementById('col-current');
                const countBadge = document.getElementById('count-current');

                container.innerHTML = '';
                countBadge.textContent = orders.length;

                if (orders.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-400">
                            <div class="w-20 h-20 bg-orange-50 rounded-3xl flex items-center justify-center mb-4">
                                <i class="fas fa-fire-burner text-4xl text-orange-200"></i>
                            </div>
                            <p class="font-semibold text-lg text-gray-400">No active tickets</p>
                            <p class="text-sm text-gray-300 mt-1">New orders will appear here automatically.</p>
                        </div>`;
                    return;
                }

                orders.forEach(order => {
                    container.appendChild(createOrderCard(order));
                    if (order.has_new_items && !printedOrderIds.has(order.id)) {
                        printedOrderIds.add(order.id);
                        autoPrintOrder(order.id);
                    }
                });
            })
            .catch(err => console.error('Error loading orders:', err));
    }

    function autoPrintOrder(orderId) {
        let printFrame = document.getElementById('print-iframe');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'print-iframe';
            printFrame.style.cssText = 'display:none;position:absolute;width:0;height:0;';
            document.body.appendChild(printFrame);
        }
        printFrame.src = `/admin/orders/${orderId}/print-kitchen?new_only=1`;
        setTimeout(() => markPrinted(orderId), 4000);
    }

    function markPrinted(orderId) {
        fetch(`/kitchen/orders/${orderId}/mark-printed`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).catch(e => console.error('Failed to mark as printed:', e));
    }

    function createOrderCard(order) {
        const status = String(order.status || '').toLowerCase();
        let cardBorder = 'border-l-orange-500';
        let badgeCls = 'bg-orange-100 text-orange-800';
        let statusText = status.toUpperCase();

        if (status === 'ready') {
            cardBorder = 'border-l-green-500';
            badgeCls = 'bg-green-100 text-green-800';
        } else if (status === 'paid') {
            cardBorder = 'border-l-blue-400';
            badgeCls = 'bg-blue-100 text-blue-800';
        } else if (status === 'confirmed') {
            cardBorder = 'border-l-yellow-500';
            badgeCls = 'bg-yellow-100 text-yellow-800';
        }

        const itemsHtml = (order.items || []).map(item => `
            <div class="flex justify-between gap-2 py-2 border-b border-gray-100 last:border-0 last:pb-0">
                <div class="text-sm text-gray-800 min-w-0">
                    <span class="font-black text-gray-900">${item.quantity}×</span>
                    ${item.is_new ? '<span class="inline-flex items-center px-1.5 py-0.5 bg-green-500 text-white text-[9px] font-bold rounded mr-1">NEW</span>' : ''}
                    <span class="break-words">${escapeHtml(item.item_name)}</span>
                    ${item.notes ? `<div class="text-xs text-red-500 font-medium mt-0.5 italic">⚠ ${escapeHtml(item.notes)}</div>` : ''}
                </div>
            </div>
        `).join('');

        let actionBtn = '';
        if (status === 'confirmed' || status === 'preparing') {
            actionBtn = `
                <button onclick="updateStatus(${order.id}, 'ready')"
                        class="w-full mt-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> Mark as Ready
                </button>`;
        } else {
            actionBtn = `<div class="w-full mt-4 bg-gray-100 text-gray-400 py-2 rounded-xl text-center text-xs font-bold tracking-wide">PROCESSED</div>`;
        }

        const minutesAgo = order.confirmed_at
            ? Math.max(0, Math.floor((Date.now() - new Date(order.confirmed_at)) / 60000))
            : null;
        const timeLabel = minutesAgo !== null
            ? (minutesAgo === 0 ? 'Just now' : `${minutesAgo}m ago`)
            : '';
        const isUrgent = minutesAgo !== null && minutesAgo >= 15;

        const card = document.createElement('div');
        card.className = `bg-white rounded-2xl shadow-sm border border-gray-200 border-l-4 ${cardBorder} overflow-hidden ${status === 'paid' ? 'opacity-60' : ''}`;

        card.innerHTML = `
            <div class="p-4 md:p-5">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-md ${badgeCls}">${statusText}</span>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-gray-100 text-gray-700">${order.order_type.toUpperCase()}</span>
                            ${isUrgent ? '<span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-red-100 text-red-700 animate-pulse">⏰ URGENT</span>' : ''}
                        </div>
                        <h3 class="text-xl font-black text-gray-900">#${order.order_number}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-table mr-1"></i>${order.table.name}
                            ${timeLabel ? `<span class="ml-2 ${isUrgent ? 'text-red-500 font-semibold' : 'text-gray-400'}">${timeLabel}</span>` : ''}
                        </p>
                    </div>
                    <button onclick="markPrinted(${order.id}); loadOrders();"
                            class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 transition flex-shrink-0"
                            title="Re-print kitchen ticket">
                        <i class="fas fa-print text-xs"></i>
                    </button>
                </div>
                ${order.delivery_address ? `<div class="mb-3 text-xs bg-blue-50 text-blue-700 p-2.5 rounded-lg border border-blue-100"><i class="fas fa-truck mr-1"></i><strong>Delivery:</strong> ${escapeHtml(order.delivery_address)}</div>` : ''}
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                    ${itemsHtml || '<p class="text-xs text-gray-400 text-center py-2">No items</p>'}
                </div>
                ${actionBtn}
            </div>
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
        .then(() => loadOrders())
        .catch(e => console.error('Error updating status:', e))
        .finally(() => isUpdating = false);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    loadOrders();
    setInterval(loadOrders, 6000);
</script>
@endpush
