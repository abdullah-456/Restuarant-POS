@extends('layouts.waiter')

@section('title', 'Order #' . $order->order_number)
@section('page-title', 'Order Details')

@push('styles')
    <style>
        /* Qty/action buttons: keep them compact regardless of responsive.css */
        .qty-btn-sm {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #f3f4f6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            flex-shrink: 0;
            transition: background .15s;
            min-height: unset !important;
            min-width: unset !important;
        }

        .qty-btn-sm:hover {
            background: #e5e7eb;
        }

        .qty-btn-sm.dec:hover {
            background: #fee2e2;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto">

        {{-- Back --}}
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('waiter.orders.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-emerald-600 hover:text-emerald-800 font-medium">
                <i class="fas fa-arrow-left text-xs"></i> Back to Orders
            </a>
        </div>

        @php
            $newItems = $order->items->filter(fn($i) => (int) $i->is_new === 1)->values();
            $oldItems = $order->items->filter(fn($i) => (int) $i->is_new !== 1)->values();

            $statusColors = [
                'pending' => 'bg-amber-100 text-amber-800',
                'draft' => 'bg-gray-100 text-gray-700',
                'confirmed' => 'bg-blue-100 text-blue-800',
                'preparing' => 'bg-purple-100 text-purple-800',
                'ready' => 'bg-emerald-100 text-emerald-800',
                'served' => 'bg-gray-100 text-gray-800',
                'paid' => 'bg-emerald-600 text-white',
                'cancelled' => 'bg-red-100 text-red-800',
                'completed' => 'bg-gray-800 text-white',
            ];
            $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
            $isFinal = in_array($order->status, ['paid', 'cancelled', 'completed']);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- ── LEFT: Items ──────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5">

                    {{-- Header --}}
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="text-[10px] uppercase font-black px-2 py-0.5 rounded bg-gray-800 text-white">
                                    {{ $order->order_type ?? 'dining' }}
                                </span>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">#{{ $order->order_number }}</h2>
                            <p class="text-sm text-gray-500 mt-0.5">
                                @if($order->order_type === 'dining')
                                    <i
                                        class="fas fa-chair mr-1 text-gray-400"></i>{{ optional($order->table)->name ?? 'No table' }}
                                @elseif($order->order_type === 'delivery')
                                    <i class="fas fa-truck mr-1 text-gray-400"></i>Delivery
                                @else
                                    <i class="fas fa-shopping-bag mr-1 text-gray-400"></i>Takeaway
                                @endif
                                &nbsp;·&nbsp;
                                <i class="fas fa-clock mr-1 text-gray-400"></i>{{ $order->created_at->format('d M Y H:i') }}
                            </p>
                            @if($order->order_type === 'delivery')
                                <div class="mt-2 text-xs bg-blue-50 text-blue-800 p-2 rounded-lg border border-blue-100">
                                    <div><i class="fas fa-phone mr-1"></i><strong>Phone:</strong>
                                        {{ $order->customer_phone ?? 'N/A' }}</div>
                                    <div class="mt-0.5"><i class="fas fa-map-marker-alt mr-1"></i><strong>Address:</strong>
                                        {{ $order->delivery_address ?? 'N/A' }}</div>
                                </div>
                            @endif
                        </div>

                        @if(!$isFinal)
                            <button type="button" onclick="openAddItemModal()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm font-semibold shadow-sm transition flex-shrink-0">
                                <i class="fas fa-plus text-xs"></i> Add Items
                            </button>
                        @endif
                    </div>

                    {{-- Existing items table --}}
                    @if($oldItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm" style="table-layout:fixed;">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                                        <th class="px-3 py-2" style="width:52%">Item</th>
                                        <th class="px-3 py-2 text-center" style="width:12%">Qty</th>
                                        <th class="px-3 py-2 text-right" style="width:18%">Price</th>
                                        <th class="px-3 py-2 text-right" style="width:18%">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($oldItems as $item)
                                        <tr>
                                            <td class="px-3 py-2.5">
                                                <p class="font-medium text-gray-800">{{ $item->item_name }}</p>
                                                @if($item->notes)
                                                    <p class="text-xs text-gray-400 italic mt-0.5">{{ $item->notes }}</p>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-center text-gray-700">{{ $item->quantity }}</td>
                                            <td class="px-3 py-2.5 text-right text-gray-600">Rs.
                                                {{ number_format($item->item_price, 2) }}</td>
                                            <td class="px-3 py-2.5 text-right font-semibold text-gray-900">Rs.
                                                {{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- New items section --}}
                    @if($newItems->count() > 0)
                        <div class="mt-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="h-px bg-gray-200 flex-1"></div>
                                <div
                                    class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-emerald-700">
                                    <i class="fas fa-bell text-emerald-500"></i> Newly Added
                                    <span
                                        class="bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full font-semibold">{{ $newItems->count() }}</span>
                                </div>
                                <div class="h-px bg-gray-200 flex-1"></div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm" style="table-layout:fixed;">
                                    <thead>
                                        <tr class="bg-emerald-50 text-left text-xs font-semibold text-emerald-600">
                                            <th class="px-3 py-2" style="width:52%">Item</th>
                                            <th class="px-3 py-2 text-center" style="width:12%">Qty</th>
                                            <th class="px-3 py-2 text-right" style="width:18%">Price</th>
                                            <th class="px-3 py-2 text-right" style="width:18%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-50">
                                        @foreach($newItems as $item)
                                            <tr class="bg-emerald-50/30">
                                                <td class="px-3 py-2.5">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <p class="font-semibold text-gray-900">{{ $item->item_name }}</p>
                                                        <span
                                                            class="px-1.5 py-0.5 bg-emerald-600 text-white text-[9px] font-black rounded-full uppercase">NEW</span>
                                                        @if(!empty($item->added_at))
                                                            <span
                                                                class="text-[10px] text-gray-400">({{ \Carbon\Carbon::parse($item->added_at)->format('H:i') }})</span>
                                                        @endif
                                                    </div>
                                                    @if($item->notes)
                                                        <p class="text-xs text-gray-500 italic mt-0.5">{{ $item->notes }}</p>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2.5 text-center text-gray-700 font-semibold">
                                                    {{ $item->quantity }}</td>
                                                <td class="px-3 py-2.5 text-right text-gray-600">Rs.
                                                    {{ number_format($item->item_price, 2) }}</td>
                                                <td class="px-3 py-2.5 text-right font-bold text-gray-900">Rs.
                                                    {{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($oldItems->count() === 0 && $newItems->count() === 0)
                        <div class="py-8 text-center text-gray-400 text-sm">No items in this order.</div>
                    @endif

                    @if($order->notes)
                        <div class="mt-4 p-3 bg-amber-50 rounded-xl border border-amber-200 text-sm text-amber-800">
                            <i class="fas fa-sticky-note mr-1"></i><strong>Notes:</strong> {{ $order->notes }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── RIGHT: Summary + Actions ─────────────────── --}}
            <div class="space-y-4">

                {{-- Summary card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5">
                    <h3 class="font-bold text-gray-900 mb-3">Order Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-800">Rs. {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->service_charge_amount > 0)
                            <div class="flex justify-between text-gray-600">
                                <span>Service Charge</span>
                                <span class="font-medium text-gray-800">Rs.
                                    {{ number_format($order->service_charge_amount, 2) }}</span>
                            </div>
                        @endif
                        @if($order->tax_amount > 0)
                            <div class="flex justify-between text-gray-600">
                                <span>Tax</span>
                                <span class="font-medium text-gray-800">Rs. {{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between font-bold text-base text-gray-900 border-t border-gray-200 pt-2 mt-2">
                            <span>Total</span>
                            <span class="text-emerald-600">Rs. {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5 space-y-2.5">
                    @if($order->status === 'pending' || $order->status === 'draft')
                        <form action="{{ route('waiter.orders.confirm', $order) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-check"></i> Confirm & Send to Kitchen
                            </button>
                        </form>
                    @endif

                    @php
                        $pendingNewCount = $order->items->where('is_new', 1)->where('status', 'pending')->count();
                    @endphp
                    @if($pendingNewCount > 0)
                        <form action="{{ route('waiter.orders.confirm', $order) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-paper-plane"></i>
                                Send {{ $pendingNewCount }} New Item(s) to Kitchen
                            </button>
                        </form>
                    @endif

                    @if(in_array($order->status, ['pending', 'draft', 'confirmed', 'preparing', 'ready']))
                        <button type="button" onclick="confirmCancel('{{ route('waiter.orders.cancel', $order) }}')"
                            class="w-full py-2 bg-red-50 text-red-700 rounded-xl text-sm font-medium hover:bg-red-100 transition flex items-center justify-center gap-2">
                            <i class="fas fa-times text-xs"></i> Cancel Order
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- ════════════════════════════════════════════════
    Add Item Modal – clean two-pane on desktop,
    stacked single-scroll on mobile
    ════════════════════════════════════════════════ --}}
    <div id="addItemModal" class="fixed inset-0 hidden z-50">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeAddItemModal()"></div>

        {{-- Modal --}}
        <div class="relative min-h-screen flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-white w-full sm:rounded-2xl shadow-2xl sm:max-w-5xl flex flex-col border border-gray-200"
                style="height: 92vh; max-height: 92vh;">

                {{-- Header --}}
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Add Items · <span class="text-gray-500 font-semibold">#{{ $order->order_number }}</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Select items on the left, review on the right</p>
                    </div>
                    <button type="button" onclick="closeAddItemModal()"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0"
                        style="min-height:unset;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 flex flex-col md:flex-row overflow-hidden min-h-0">

                    {{-- ── LEFT: Menu list ────────────────────────── --}}
                    <div class="md:w-3/5 flex flex-col border-b md:border-b-0 md:border-r border-gray-200 min-h-0"
                        style="max-height: 45vh; min-height: 0;" id="menuPaneWrapper">
                        {{-- Search --}}
                        <div class="px-4 pt-3 pb-2 flex-shrink-0">
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <input type="text" id="modalMenuSearch" placeholder="Search menu…"
                                    class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50">
                            </div>
                        </div>
                        {{-- Items --}}
                        <div class="flex-1 overflow-y-auto px-4 pb-4 space-y-4 custom-scrollbar min-h-0">
                            @foreach($categories as $category)
                                <div class="modal-category-block">
                                    <h5
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 sticky top-0 bg-white pt-1 pb-1 z-10">
                                        {{ $category->name }}
                                    </h5>
                                    <div class="space-y-1.5">
                                        @foreach($category->menuItems as $item)
                                            <button type="button"
                                                onclick="addToSelectedItems({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})"
                                                class="modal-menu-item group flex items-center gap-3 p-2.5 border border-gray-200 rounded-xl hover:border-emerald-400 hover:bg-emerald-50/50 transition text-left w-full bg-white"
                                                style="min-height:unset;" data-name="{{ strtolower($item->name) }}">

                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}"
                                                        class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="{{ $item->name }}">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-utensils text-emerald-400 text-sm"></i>
                                                    </div>
                                                @endif

                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $item->name }}</p>
                                                    <p class="text-emerald-600 font-bold text-xs">Rs.
                                                        {{ number_format($item->price, 0) }}</p>
                                                </div>

                                                <div class="w-7 h-7 rounded-full bg-gray-100 group-hover:bg-emerald-500 flex items-center justify-center transition flex-shrink-0"
                                                    style="min-height:unset;min-width:unset;">
                                                    <i
                                                        class="fas fa-plus text-gray-500 group-hover:text-white text-xs transition"></i>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── RIGHT: Selected items ───────────────────── --}}
                    <div class="md:w-2/5 flex flex-col bg-gray-50 min-h-0 overflow-hidden">

                        <div
                            class="px-4 py-3 bg-white border-b border-gray-200 flex-shrink-0 flex items-center justify-between">
                            <h4 class="font-bold text-gray-900 text-sm">Selected Items</h4>
                            <button type="button" onclick="clearSelectedItems()"
                                class="text-xs font-semibold text-gray-500 hover:text-red-600 transition"
                                style="min-height:unset;">
                                Clear All
                            </button>
                        </div>

                        {{-- Summary stats --}}
                        <div id="selectedItemsSummary"
                            class="px-4 py-2 bg-white border-b border-gray-100 hidden flex-shrink-0">
                            <div class="flex gap-3">
                                <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-center">
                                    <p class="text-[10px] text-gray-400">Items</p>
                                    <p class="text-lg font-black text-gray-900" id="totalSelectedItems">0</p>
                                </div>
                                <div class="flex-1 bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 text-center">
                                    <p class="text-[10px] text-emerald-600">Subtotal</p>
                                    <p class="text-sm font-black text-emerald-700" id="selectedSubtotal">Rs. 0</p>
                                </div>
                            </div>
                        </div>

                        {{-- List --}}
                        <div class="flex-1 overflow-y-auto px-4 py-3 custom-scrollbar min-h-0">
                            <div id="selectedItemsContainer" class="space-y-2.5">
                                <div id="emptySelectedItems" class="text-center py-12 text-gray-300">
                                    <i class="fas fa-shopping-cart text-4xl mb-3 block"></i>
                                    <p class="font-semibold text-sm text-gray-400">No items selected</p>
                                    <p class="text-xs mt-1 text-gray-300">Tap items on the left</p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="px-4 py-3 border-t border-gray-200 bg-white flex-shrink-0">
                            <div class="flex gap-2">
                                <button type="button" onclick="closeAddItemModal()"
                                    class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-gray-200 transition"
                                    style="min-height:unset;">
                                    Cancel
                                </button>
                                <button type="button" onclick="confirmAllItems()" id="confirmItemsBtn"
                                    class="flex-1 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed"
                                    style="min-height:unset;" disabled>
                                    <i class="fas fa-check text-xs"></i> Add to Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="cancelOrderForm" method="POST" style="display:none;">@csrf</form>

    @push('scripts')
        <script>
            let selectedItems = {};

            // ── Modal open/close ──────────────────────────────────────────
            window.openAddItemModal = function () {
                document.getElementById('addItemModal').classList.remove('hidden');
                renderSelectedItems();
                document.getElementById('modalMenuSearch')?.focus();

                // On desktop: set left pane to auto height (flex handles it)
                const w = document.getElementById('menuPaneWrapper');
                if (window.innerWidth >= 768) {
                    w.style.maxHeight = '';
                } else {
                    w.style.maxHeight = '45vh';
                }
            }

            window.closeAddItemModal = function () {
                document.getElementById('addItemModal').classList.add('hidden');
            }

            // ── Add / remove items ────────────────────────────────────────
            window.addToSelectedItems = function (id, name, price) {
                if (selectedItems[id]) {
                    selectedItems[id].qty++;
                } else {
                    selectedItems[id] = { name, price, qty: 1, notes: '' };
                }
                renderSelectedItems();
            }

            window.updateSelectedItemQty = function (id, delta) {
                if (!selectedItems[id]) return;
                selectedItems[id].qty += delta;
                if (selectedItems[id].qty <= 0) delete selectedItems[id];
                renderSelectedItems();
            }

            window.updateSelectedItemNotes = function (id, notes) {
                if (selectedItems[id]) selectedItems[id].notes = notes;
            }

            window.removeSelectedItem = function (id) {
                delete selectedItems[id];
                renderSelectedItems();
            }

            window.clearSelectedItems = function () {
                if (Object.keys(selectedItems).length === 0) return;
                window.Alert.confirm('Clear selected items?', 'This will remove all selected items.').then(r => {
                    if (r.isConfirmed) { selectedItems = {}; renderSelectedItems(); }
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

                if (emptyDiv?.parentNode === container) emptyDiv.remove();
                summaryDiv.classList.remove('hidden');
                confirmBtn.disabled = false;

                let totalItems = 0, subtotal = 0, html = '';

                ids.forEach(id => {
                    const item = selectedItems[id];
                    const lineTotal = item.price * item.qty;
                    totalItems += item.qty;
                    subtotal += lineTotal;

                    html += `
                <div class="bg-white rounded-xl border border-gray-200 p-3 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 text-sm truncate">${escapeHtml(item.name)}</p>
                            <p class="text-xs text-emerald-600 font-semibold">Rs. ${Number(item.price).toFixed(0)} each</p>
                        </div>
                        <button type="button" onclick="removeSelectedItem(${id})"
                                class="w-6 h-6 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 flex items-center justify-center flex-shrink-0 transition"
                                style="min-height:unset;min-width:unset;">
                            <i class="fas fa-trash-alt" style="font-size:10px;"></i>
                        </button>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="inline-flex items-center gap-1.5 bg-gray-100 rounded-lg px-2 py-0.5">
                            <button type="button" onclick="updateSelectedItemQty(${id}, -1)"
                                    class="qty-btn-sm dec"
                                    style="min-height:unset;min-width:unset;">
                                <i class="fas fa-minus" style="font-size:8px;color:#9ca3af;"></i>
                            </button>
                            <span class="w-6 text-center font-bold text-sm text-gray-900">${item.qty}</span>
                            <button type="button" onclick="updateSelectedItemQty(${id}, 1)"
                                    class="qty-btn-sm"
                                    style="min-height:unset;min-width:unset;">
                                <i class="fas fa-plus" style="font-size:8px;color:#9ca3af;"></i>
                            </button>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">Rs. ${lineTotal.toFixed(0)}</p>
                    </div>
                    <input type="text" placeholder="Special instructions (optional)"
                           value="${escapeHtml(item.notes || '')}"
                           oninput="updateSelectedItemNotes(${id}, this.value)"
                           class="mt-2 w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 text-gray-700">
                </div>`;
                });

                container.innerHTML = html;
                totalItemsSpan.textContent = totalItems;
                subtotalSpan.textContent = 'Rs. ' + subtotal.toFixed(0);
            }

            // ── Confirm & submit items ────────────────────────────────────
            window.confirmAllItems = function () {
                const ids = Object.keys(selectedItems);
                if (ids.length === 0) return;

                const btn = document.getElementById('confirmItemsBtn');
                const originalHTML = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Adding…';

                const requests = ids.map(id => {
                    const item = selectedItems[id];
                    return fetch('{{ route("waiter.orders.add-item", $order) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ menu_item_id: Number(id), quantity: item.qty, notes: item.notes || '' })
                    }).then(async res => {
                        const data = await res.json();
                        if (!res.ok || !data.success) throw new Error(data.error || 'Failed to add item');
                        return data;
                    });
                });

                Promise.all(requests)
                    .then(() => {
                        selectedItems = {};
                        closeAddItemModal();
                        window.Alert.success('Items added successfully!');
                        loadOrderData();
                    })
                    .catch(err => {
                        window.Alert.error(err.message || 'Network error, please try again.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    });
            }

            function loadOrderData() {
                window.location.reload();
            }

            // ── Search ────────────────────────────────────────────────────
            document.getElementById('modalMenuSearch')?.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                document.querySelectorAll('.modal-menu-item').forEach(btn => {
                    const name = (btn.getAttribute('data-name') || '').toLowerCase();
                    btn.style.display = name.includes(q) ? 'flex' : 'none';
                });
                document.querySelectorAll('.modal-category-block').forEach(block => {
                    const any = [...block.querySelectorAll('.modal-menu-item')].some(b => b.style.display !== 'none');
                    block.style.display = any ? '' : 'none';
                });
            });

            // ── Cancel order ──────────────────────────────────────────────
            window.confirmCancel = function (url) {
                window.Alert.confirm('Cancel this order?', 'This cannot be undone.').then(r => {
                    if (r.isConfirmed) {
                        const form = document.getElementById('cancelOrderForm');
                        form.action = url;
                        form.submit();
                    }
                });
            }

            // ── Escape helper ─────────────────────────────────────────────
            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            document.addEventListener('DOMContentLoaded', function () {
                @if(session('print_order_id'))
                    @php
                        $params = [];
                        if (session('print_new_only'))
                            $params['new_only'] = 1;
                        $printUrl = route('admin.orders.print-kitchen', array_merge(['order' => session('print_order_id')], $params));
                    @endphp
                    const printUrl = "{{ $printUrl }}";
                    window.open(printUrl, '_blank');
                @endif
        });
        </script>
    @endpush
@endsection