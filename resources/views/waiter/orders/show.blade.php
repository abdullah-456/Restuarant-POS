@extends('layouts.waiter')

@section('title', 'Order #' . $order->order_number)
@section('page-title', 'Order Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('waiter.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back to Orders
        </a>
    </div>

    @php
        // NEW only if is_new = 1
        $newItems = $order->items->filter(fn($i) => (int)$i->is_new === 1)->values();
        $oldItems = $order->items->filter(fn($i) => (int)$i->is_new !== 1)->values();

        $statusColors = [
            'pending'   => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'preparing' => 'bg-purple-100 text-purple-800',
            'ready'     => 'bg-green-100 text-green-800',
            'served'    => 'bg-gray-100 text-gray-800',
            'paid'      => 'bg-green-600 text-white',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-gray-800 text-white',
        ];
        $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
        $isFinal = in_array($order->status, ['paid', 'cancelled', 'completed']);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Order Info --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            <i class="fas fa-chair mr-1"></i>{{ optional($order->table)->name ?? 'No table' }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-clock mr-1"></i>{{ $order->created_at->format('d M Y H:i') }}
                            @if(!empty($order->modified_at))
                                &nbsp;·&nbsp;
                                <i class="fas fa-pencil-alt text-orange-500"></i>
                                <span class="text-orange-600">Modified {{ \Carbon\Carbon::parse($order->modified_at)->format('H:i') }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $color }}">
                            {{ ucfirst($order->status) }}
                        </span>

                        @if(!$isFinal)
                            <button type="button"
                                    onclick="openAddItemModal()"
                                    class="px-3 py-1.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 flex items-center gap-2 text-sm shadow-sm">
                                <i class="fas fa-plus"></i> Add Items
                            </button>
                        @endif
                    </div>
                </div>

                {{-- MAIN ITEMS (NO NEW TAG / NO COLOR) --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm table-fixed">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                                <th class="px-3 py-2 w-[52%]">Item</th>
                                <th class="px-3 py-2 text-center w-[12%]">Qty</th>
                                <th class="px-3 py-2 text-right w-[18%]">Price</th>
                                <th class="px-3 py-2 text-right w-[18%]">Line</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($oldItems as $item)
                                <tr>
                                    <td class="px-3 py-2.5">
                                        <p class="font-medium text-gray-800">{{ $item->item_name }}</p>
                                        @if($item->notes)
                                            <p class="text-xs text-gray-500 italic mt-0.5">{{ $item->notes }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-gray-700">{{ $item->quantity }}</td>
                                    <td class="px-3 py-2.5 text-right text-gray-600">Rs. {{ number_format($item->item_price, 2) }}</td>
                                    <td class="px-3 py-2.5 text-right font-medium text-gray-800">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                @if($newItems->count() === 0)
                                    <tr>
                                        <td colspan="4" class="px-3 py-8 text-center text-gray-400">No items in this order.</td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- DIVIDER + NEW ITEMS BELOW (WITH NEW TAG) --}}
                @if($newItems->count() > 0)
                    <div class="my-5">
                        <div class="flex items-center gap-3">
                            <div class="h-px bg-gray-200 flex-1"></div>
                            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-2">
                                <i class="fas fa-bell text-green-600"></i> Newly Added Items
                                <span class="text-[11px] font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                    {{ $newItems->count() }} line(s)
                                </span>
                            </div>
                            <div class="h-px bg-gray-200 flex-1"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            These were added using "Add Items".
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm table-fixed">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                                    <th class="px-3 py-2 w-[52%]">Item</th>
                                    <th class="px-3 py-2 text-center w-[12%]">Qty</th>
                                    <th class="px-3 py-2 text-right w-[18%]">Price</th>
                                    <th class="px-3 py-2 text-right w-[18%]">Line</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($newItems as $item)
                                    <tr>
                                        <td class="px-3 py-2.5">
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-gray-900">{{ $item->item_name }}</p>
                                                <span class="px-2 py-0.5 bg-green-600 text-white text-xs rounded-full">NEW</span>
                                                @if(!empty($item->added_at))
                                                    <span class="text-xs text-gray-500">
                                                        ({{ \Carbon\Carbon::parse($item->added_at)->format('H:i') }})
                                                    </span>
                                                @endif
                                            </div>
                                            @if($item->notes)
                                                <p class="text-xs text-gray-600 italic mt-0.5">{{ $item->notes }}</p>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-gray-700 font-semibold">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2.5 text-right text-gray-600">Rs. {{ number_format($item->item_price, 2) }}</td>
                                        <td class="px-3 py-2.5 text-right font-bold text-gray-900">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($order->notes)
                    <div class="mt-4 p-3 bg-yellow-50 rounded-xl border border-yellow-200 text-sm text-yellow-800">
                        <i class="fas fa-sticky-note mr-1"></i><strong>Notes:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Summary & Actions --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->service_charge_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Service Charge</span>
                            <span>Rs. {{ number_format($order->service_charge_amount, 2) }}</span>
                        </div>
                    @endif
                    @if($order->tax_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>Rs. {{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-base text-gray-900 border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span>Rs. {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 space-y-2">
                @if($order->status === 'pending')
                    <form action="{{ route('waiter.orders.confirm', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-check"></i> Confirm & Send to Kitchen
                        </button>
                    </form>
                @endif
                @php
    $pendingNewCount = $order->items->where('is_new', 1)->where('status', 'pending')->count();
@endphp

@if($pendingNewCount > 0)
    <form action="{{ route('waiter.orders.confirm', $order) }}" method="POST" class="mt-3">
        @csrf
        <button type="submit"
                class="w-full py-3 bg-green-600 text-white rounded-xl font-semibold text-sm hover:bg-green-700 transition flex items-center justify-center gap-2 shadow-sm">
            <i class="fas fa-paper-plane"></i>
            Send {{ $pendingNewCount }} New Item(s) to Kitchen
        </button>
    </form>
@endif

                @if(in_array($order->status, ['pending', 'confirmed', 'preparing', 'ready']))
                    <button type="button"
                            onclick="confirmCancel('{{ route('waiter.orders.cancel', $order) }}')"
                            class="w-full py-2.5 bg-red-50 text-red-700 rounded-xl font-medium text-sm hover:bg-red-100 transition flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- Add Item Modal --}}
<div id="addItemModal" class="fixed inset-0 hidden z-50 overflow-y-auto">
    
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeAddItemModal()"></div>

    {{-- Modal Wrapper --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden border border-gray-200">

            {{-- Header (Fixed) --}}
            <div class="p-5 border-b border-gray-200 flex items-center justify-between flex-shrink-0 bg-white">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">
                        Add Items · <span class="text-gray-600">#{{ $order->order_number }}</span>
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Select items → review → add to order</p>
                </div>

                <button type="button" onclick="closeAddItemModal()"
                        class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-500">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Body - Two-column scrollable layout --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 overflow-hidden">

                {{-- LEFT: Menu (Scrollable) --}}
                <div class="border-r border-gray-200 flex flex-col min-h-0 overflow-hidden">
                    {{-- Search - Fixed at top --}}
                    <div class="p-5 pb-2 bg-white flex-shrink-0">
                        <div class="relative flex items-center">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="modalMenuSearch" placeholder="Search menu items..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Categories - Scrollable area --}}
                    <div class="flex-1 overflow-y-auto min-h-0 px-5 pb-6 space-y-6 custom-scrollbar">
                        @foreach($categories as $category)
                            <div class="modal-category-block">
                                <h5 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 sticky top-0 bg-white pt-1 pb-2 z-10">
                                    {{ $category->name }}
                                </h5>

                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($category->menuItems as $item)
                                        <button type="button"
                                                onclick="addToSelectedItems({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})"
                                                class="modal-menu-item group flex items-center gap-3 p-3.5 border border-gray-200 rounded-xl hover:border-blue-400 hover:shadow-md transition text-left w-full bg-white"
                                                data-name="{{ strtolower($item->name) }}">

                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}"
                                                     class="w-14 h-14 rounded-lg object-cover flex-shrink-0"
                                                     alt="{{ $item->name }}">
                                            @else
                                                <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-utensils text-blue-400 text-xl"></i>
                                                </div>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-800 text-base truncate">{{ $item->name }}</p>
                                                <p class="text-blue-600 font-bold text-base">Rs. {{ number_format($item->price, 2) }}</p>
                                            </div>

                                            <div class="w-9 h-9 rounded-full bg-blue-50 group-hover:bg-blue-600 flex items-center justify-center transition flex-shrink-0">
                                                <i class="fas fa-plus text-blue-500 group-hover:text-white text-base transition"></i>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- RIGHT: Selected Items (Scrollable list + fixed footer) --}}
                <div class="bg-gray-50 flex flex-col min-h-0 overflow-hidden">

                    {{-- Top Summary (Fixed) --}}
                    <div class="p-5 border-b border-gray-200 bg-white flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-gray-900">Selected Items</h4>
                            <button type="button" onclick="clearSelectedItems()"
                                    class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                Clear All
                            </button>
                        </div>

                        <div id="selectedItemsSummary" class="mt-4 hidden">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">Total Items</p>
                                    <p class="text-xl font-extrabold text-gray-900" id="totalSelectedItems">0</p>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-xs text-gray-500">Subtotal</p>
                                    <p class="text-xl font-extrabold text-gray-900" id="selectedSubtotal">Rs. 0.00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Selected List - Scrollable --}}
                    <div class="flex-1 overflow-y-auto min-h-0 p-5 pt-2 custom-scrollbar">
                        <div id="selectedItemsContainer" class="space-y-4 pr-1">
                            <div id="emptySelectedItems" class="text-center py-16 text-gray-400">
                                <div class="w-20 h-20 rounded-2xl bg-white border border-gray-200 mx-auto flex items-center justify-center mb-4">
                                    <i class="fas fa-shopping-cart text-3xl"></i>
                                </div>
                                <p class="font-semibold text-lg">No items selected yet</p>
                                <p class="text-sm mt-1">Tap menu items on the left to add them here</p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer (Fixed) --}}
                    <div class="p-5 border-t border-gray-200 bg-white flex-shrink-0">
                        <div class="flex gap-3">
                            <button type="button" onclick="closeAddItemModal()"
                                    class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                                Cancel
                            </button>

                            <button type="button" onclick="confirmAllItems()"
                                    id="confirmItemsBtn"
                                    class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                    disabled>
                                <i class="fas fa-check"></i> Add to Order
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 mt-3 text-center">
                            Added items will show with a <span class="font-medium text-green-700">"NEW"</span> tag in the order.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<form id="cancelOrderForm" method="POST" style="display:none;">
    @csrf
</form>



@push('scripts')
<script>
// Global variables
let selectedItems = {}; // { id: { name, price, qty, notes } }

// Modal functions
window.openAddItemModal = function() {
    document.getElementById('addItemModal').classList.remove('hidden');
    renderSelectedItems();
    document.getElementById('modalMenuSearch')?.focus();
}

window.closeAddItemModal = function() {
    document.getElementById('addItemModal').classList.add('hidden');
    // Optional: Clear selected items when closing
    // selectedItems = {};
}

// Selected items functions
window.addToSelectedItems = function(id, name, price) {
    if (selectedItems[id]) {
        selectedItems[id].qty++;
    } else {
        selectedItems[id] = { 
            name: name, 
            price: price, 
            qty: 1, 
            notes: '' 
        };
    }
    renderSelectedItems();
}

window.updateSelectedItemQty = function(id, delta) {
    if (!selectedItems[id]) return;
    selectedItems[id].qty += delta;
    if (selectedItems[id].qty <= 0) {
        delete selectedItems[id];
    }
    renderSelectedItems();
}

window.updateSelectedItemNotes = function(id, notes) {
    if (selectedItems[id]) {
        selectedItems[id].notes = notes;
    }
}

window.removeSelectedItem = function(id) {
    delete selectedItems[id];
    renderSelectedItems();
}

window.clearSelectedItems = function() {
    if (Object.keys(selectedItems).length === 0) return;
    
    window.Alert.confirm('Clear selected items?', 'This will remove all selected items.').then((result) => {
        if (result.isConfirmed) {
            selectedItems = {};
            renderSelectedItems();
        }
    });
}

function renderSelectedItems() {
    const container = document.getElementById('selectedItemsContainer');
    const emptyDiv = document.getElementById('emptySelectedItems');
    const summaryDiv = document.getElementById('selectedItemsSummary');
    const confirmBtn = document.getElementById('confirmItemsBtn');
    const totalItemsSpan = document.getElementById('totalSelectedItems');
    const subtotalSpan = document.getElementById('selectedSubtotal');

    const ids = Object.keys(selectedItems);

    if (ids.length === 0) {
        container.innerHTML = '';
        if (emptyDiv) container.appendChild(emptyDiv);
        summaryDiv.classList.add('hidden');
        confirmBtn.disabled = true;
        return;
    }

    // Remove empty div if it exists
    if (emptyDiv && emptyDiv.parentNode === container) {
        emptyDiv.remove();
    }

    summaryDiv.classList.remove('hidden');
    confirmBtn.disabled = false;

    let totalItems = 0;
    let subtotal = 0;
    let html = '';

    ids.forEach(id => {
        const item = selectedItems[id];
        const lineTotal = item.price * item.qty;
        totalItems += item.qty;
        subtotal += lineTotal;

        html += `
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                <div class="flex justify-between items-start gap-3">
                    <div class="min-w-0">
                        <h5 class="font-semibold text-gray-900 truncate">${escapeHtml(item.name)}</h5>
                        <p class="text-sm text-blue-600 font-semibold">Rs. ${Number(item.price).toFixed(2)} each</p>
                    </div>
                    <button type="button" onclick="removeSelectedItem(${id})"
                            class="w-8 h-8 rounded-full hover:bg-red-50 text-red-600 flex items-center justify-center">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2 bg-gray-100 rounded-lg px-2 py-1">
                        <button type="button" onclick="updateSelectedItemQty(${id}, -1)"
                                class="w-7 h-7 rounded-full bg-white hover:bg-red-50 text-gray-700 hover:text-red-600 border border-gray-200 flex items-center justify-center">
                            <i class="fas fa-minus text-xs"></i>
                        </button>

                        <span class="w-8 text-center font-semibold text-gray-900">${item.qty}</span>

                        <button type="button" onclick="updateSelectedItemQty(${id}, 1)"
                                class="w-7 h-7 rounded-full bg-white hover:bg-blue-50 text-gray-700 hover:text-blue-600 border border-gray-200 flex items-center justify-center">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-gray-500">Line Total</p>
                        <p class="font-bold text-gray-900">Rs. ${lineTotal.toFixed(2)}</p>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="text-xs font-medium text-gray-600">Special instructions</label>
                    <input type="text"
                           placeholder="e.g., no onions / extra spicy"
                           value="${escapeHtml(item.notes || '')}"
                           oninput="updateSelectedItemNotes(${id}, this.value)"
                           class="mt-1 w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    totalItemsSpan.textContent = totalItems;
    subtotalSpan.textContent = 'Rs. ' + subtotal.toFixed(2);
}

window.confirmAllItems = function() {
    const ids = Object.keys(selectedItems);
    if (ids.length === 0) return;

    const btn = document.getElementById('confirmItemsBtn');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

    const requests = ids.map(id => {
        const item = selectedItems[id];
        return fetch('{{ route("waiter.orders.add-item", $order) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                menu_item_id: Number(id),
                quantity: item.qty,
                notes: item.notes || ''
            })
        }).then(async res => {
            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.error || 'Failed to add item');
            }
            return data;
        });
    });

    Promise.all(requests)
        .then(() => {
            selectedItems = {};
            closeAddItemModal();
            window.Alert.success('Items added successfully.').then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            window.Alert.error(error.message || 'Network error, please try again.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

// Search functionality
document.getElementById('modalMenuSearch')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    document.querySelectorAll('.modal-menu-item').forEach(btn => {
        const itemName = (btn.getAttribute('data-name') || '').toLowerCase();
        btn.style.display = itemName.includes(searchTerm) ? 'flex' : 'none';
    });
});

// Cancel order confirmation
window.confirmCancel = function(url) {
    window.Alert.confirm('Cancel this order?', 'This will mark the order as cancelled.').then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('cancelOrderForm');
            form.action = url;
            form.submit();
        }
    });
}

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Order details page loaded');
});
</script>
@endpush
@endsection