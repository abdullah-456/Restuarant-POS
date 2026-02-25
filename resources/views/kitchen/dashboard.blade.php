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

    <div class="grid grid-cols-1 gap-6">
        <div class="space-y-4">
            <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-orange-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                        <i class="fas fa-fire-burner text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Current Kitchen Tickets</h3>
                        <p class="text-xs text-gray-500">Orders waiting to be printed or cooked</p>
                    </div>
                </div>
                <span id="count-current" class="text-lg font-bold bg-orange-600 text-white px-4 py-1 rounded-full shadow-sm">0</span>
            </div>
            
            <div id="col-current" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Orders will be injected here --}}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let isUpdating = false;
    let knownOrderIds = new Set();
    let isInitialLoad = true;

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
                    container.innerHTML = '<div class="col-span-full py-12 text-center text-gray-400">No active tickets</div>';
                    return;
                }

                orders.forEach(order => {
                    container.appendChild(createOrderCard(order));
                    
                    if (order.has_new_items) {
                        autoPrintOrder(order.id);
                    }
                });
                isInitialLoad = false;
            })
            .catch(err => console.error('Error loading orders:', err));
    }

    function autoPrintOrder(orderId) {
        let printFrame = document.getElementById('print-iframe');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'print-iframe';
            printFrame.style.display = 'none';
            document.body.appendChild(printFrame);
        }

        printFrame.src = `/admin/orders/${orderId}/print-kitchen?new_only=1`;

        setTimeout(() => {
            fetch(`/kitchen/orders/${orderId}/mark-printed`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).catch(e => console.error('Failed to mark as printed:', e));
        }, 3000);
    }

    function createOrderCard(order) {
        const status = String(order.status || '').toLowerCase();
        let borderClass = 'border-l-orange-500';
        let badgeClass = 'bg-orange-100 text-orange-800';
        let statusText = status.toUpperCase();
        
        if (status === 'ready') {
            borderClass = 'border-l-green-500 opacity-75';
            badgeClass = 'bg-green-100 text-green-800';
        } else if (status === 'paid') {
            borderClass = 'border-l-blue-500 opacity-60';
            badgeClass = 'bg-blue-100 text-blue-800';
        }

        const itemsHtml = (order.items || []).map(item => `
            <div class="flex justify-between gap-2 text-sm border-b border-gray-50 pb-2 mb-2 last:border-0 last:mb-0">
                <div class="text-gray-800 min-w-0">
                    <span class="font-bold text-gray-900">${item.quantity}x</span> 
                    ${item.is_new ? '<strong class="text-green-600 bg-green-50 px-1 rounded text-[10px] mr-1">NEW</strong>' : ''}
                    ${escapeHtml(item.item_name)}
                    ${item.notes ? `<div class="text-xs text-red-500 font-medium ml-5 italic">*** ${escapeHtml(item.notes)} ***</div>` : ''}
                </div>
            </div>
        `).join('');

        const card = document.createElement('div');
        card.className = `bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-5 border-l-4 ${borderClass}`;

        let actionButton = '';
        if (status === 'confirmed' || status === 'preparing') {
            actionButton = `
                <button onclick="updateStatus(${order.id}, 'ready')" 
                        class="w-full mt-4 bg-orange-600 text-white px-4 py-2 rounded-xl hover:bg-orange-700 transition font-bold text-sm shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Mark Ready
                </button>
            `;
        } else {
             actionButton = `<div class="w-full mt-4 bg-gray-50 text-gray-400 py-2 rounded-xl text-center text-[10px] font-bold">PROCESSED</div>`;
        }

        card.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-black px-2 py-0.5 rounded ${badgeClass}">${statusText}</span>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded bg-gray-800 text-white">${order.order_type.toUpperCase()}</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 truncate">#${order.order_number}</h3>
                    <div class="text-xs font-bold text-gray-700">
                        <i class="fas fa-table mr-1"></i>${order.table.name}
                    </div>
                </div>
            </div>
            ${order.delivery_address ? `<div class="mt-2 text-[10px] text-blue-700 bg-blue-50 p-2 rounded-lg border border-blue-100"><strong>Address:</strong> ${escapeHtml(order.delivery_address)}</div>` : ''}
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 mt-3">${itemsHtml}</div>
            ${actionButton}
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
        .then(() => loadOrders())
        .finally(() => isUpdating = false);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    loadOrders();
    setInterval(loadOrders, 5000);
</script>
@endpush
@endsection
