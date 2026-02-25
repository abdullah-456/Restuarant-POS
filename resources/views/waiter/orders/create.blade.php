@extends('layouts.waiter')

@section('title', 'Create Order')
@section('page-title', 'Create New Order')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-full"> {{-- Changed to max-w-full --}}
    <form id="orderForm" action="{{ route('waiter.orders.store') }}" method="POST">
        @csrf
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- ── LEFT: Menu Section - Takes remaining space --}}
            <div class="lg:flex-grow min-w-0"> {{-- Changed to flex-grow --}}
                {{-- Order Options: Dining / Takeaway / Delivery --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-truck mr-1 text-orange-600"></i> Order Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="order_type" value="dining" class="peer hidden" checked onchange="toggleOrderTypeFields()">
                                <div class="text-center py-2 px-1 border border-gray-200 rounded-lg peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 transition text-xs font-bold uppercase">
                                    Dining
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="order_type" value="takeaway" class="peer hidden" onchange="toggleOrderTypeFields()">
                                <div class="text-center py-2 px-1 border border-gray-200 rounded-lg peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 transition text-xs font-bold uppercase">
                                    Takeaway
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="order_type" value="delivery" class="peer hidden" onchange="toggleOrderTypeFields()">
                                <div class="text-center py-2 px-1 border border-gray-200 rounded-lg peer-checked:bg-orange-600 peer-checked:text-white peer-checked:border-orange-600 transition text-xs font-bold uppercase">
                                    Delivery
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="tableSelectorContainer" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-chair mr-1 text-blue-600"></i> Select Table <span class="text-red-500">*</span>
                        </label>
                        <select name="restaurant_table_id" id="tableSelect"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('restaurant_table_id') border-red-500 @enderror">
                            <option value="">— Choose a table —</option>
                            @foreach($tables as $t)
                            <option value="{{ $t->id }}"
                                @if(optional($selectedTable)->id == $t->id) selected @endif
                                @if($t->status === 'occupied') disabled class="text-gray-400" @endif>
                                {{ $t->name }} (Cap: {{ $t->capacity }})
                                @if($t->status !== 'available') — {{ ucfirst($t->status) }} @endif
                            </option>
                            @endforeach
                        </select>
                        @error('restaurant_table_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Delivery Address & Phone (Hidden by default) --}}
                <div id="deliveryInfoContainer" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Phone</label>
                            <input type="text" name="customer_phone" placeholder="Contact number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Address <span class="text-red-500">*</span></label>
                            <input type="text" name="delivery_address" id="delivery_address" placeholder="Full address for delivery"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Search --}}
                <div class="mb-4">
                    <div class="relative flex items-center">
                        <i class="fas fa-search absolute left-5 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text" id="menuSearch" placeholder="     Search menu items..."
                            class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400">
                    </div>
                </div>

                {{-- Categories & Menu --}}
                <div class="space-y-4 max-h-[calc(100vh-420px)] overflow-y-auto pr-2">
                    @foreach($categories as $category)
                    <div class="category-block" data-category="{{ strtolower($category->name) }}">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2 px-1">
                            {{ $category->name }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3"> {{-- More columns for larger screens --}}
                            @foreach($category->menuItems as $item)
                            <button type="button"
                                onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})"
                                class="menu-item-btn flex items-center gap-4 bg-white border border-gray-200 rounded-xl p-5 text-left hover:border-blue-400 hover:shadow-md transition group"
                                data-name="{{ strtolower($item->name) }}">
                                @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0" alt="{{ $item->name }}"> {{-- Larger images --}}
                                @else
                                <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-utensils text-blue-400 text-2xl"></i> {{-- Larger icon --}}
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 text-base truncate">{{ $item->name }}</p> {{-- Larger text --}}
                                    <p class="text-blue-600 font-bold text-base">Rs. {{ number_format($item->price, 2) }}</p> {{-- Larger text --}}
                                </div>
                                <div class="w-8 h-8 rounded-full bg-blue-50 group-hover:bg-blue-600 flex items-center justify-center transition flex-shrink-0">
                                    <i class="fas fa-plus text-blue-400 group-hover:text-white text-sm transition"></i> {{-- Larger icon --}}
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if($categories->isEmpty())
                    <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                        <i class="fas fa-utensils text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No menu items available. Please ask admin to add items.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── RIGHT: Cart Section - Larger fixed width --}}
<div class="lg:w-[450px] xl:w-[500px] 2xl:w-[550px] flex-shrink-0">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-20 flex flex-col w-full min-w-full" style="height: calc(100vh - 180px);">
        {{-- Header - Fixed --}}
        <div class="p-5 border-b border-gray-100 flex-shrink-0">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                Order Cart
                <span id="cartCount" class="ml-auto text-sm bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full">0 items</span>
            </h2>
        </div>
        {{-- Cart items - Scrollable area --}}
        <div id="cartItems" class="p-5 overflow-y-auto flex-1 w-full">
            <div id="emptyCart" class="text-center py-12">
                <i class="fas fa-shopping-cart text-4xl text-gray-200 mb-3 block"></i>
                <span class="block font-medium text-gray-400">Cart is empty</span>
                <span class="text-xs text-gray-300 mt-1">Click on menu items to add them</span>
            </div>
        </div>

        {{-- Totals Section --}}
        <div class="border-t border-gray-100 flex-shrink-0 w-full">
            <div class="px-5 py-4 space-y-2 text-base">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span id="subtotalDisplay" class="font-medium">Rs. 0.00</span>
                </div>
                @php
                $servicePercent = \App\Models\Setting::get('service_charge_percent', 0);
                $taxPercent = \App\Models\Setting::get('tax_percent', 0);
                @endphp
                @if($servicePercent > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Service ({{ $servicePercent }}%)</span>
                    <span id="serviceDisplay" class="font-medium">Rs. 0.00</span>
                </div>
                @endif
                @if($taxPercent > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Tax ({{ $taxPercent }}%)</span>
                    <span id="taxDisplay" class="font-medium">Rs. 0.00</span>
                </div>
                @endif
                <div class="flex justify-between font-bold text-xl border-t border-gray-200 pt-3 mt-3 text-gray-900">
                    <span>Total</span>
                    <span id="totalDisplay">Rs. 0.00</span>
                </div>
            </div>

            {{-- Notes --}}
            <div class="px-5 pb-4">
                <label class="block text-sm font-semibold text-gray-500 mb-2">Order Notes</label>
                <textarea name="notes" rows="2" placeholder="Any special instructions..."
                    class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>

            {{-- Hidden cart data --}}
            <div id="hiddenCartInputs"></div>

            {{-- Actions --}}
            <div class="p-5 border-t border-gray-100 space-y-3">
                <button type="submit" id="submitBtn" disabled
                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold text-base hover:from-blue-700 hover:to-blue-800 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-paper-plane text-lg"></i> Place Order
                </button>
                <a href="{{ route('waiter.dashboard') }}"
                    class="block w-full py-3 bg-gray-100 text-gray-700 rounded-xl font-medium text-base hover:bg-gray-200 transition-all duration-200 text-center border border-gray-300">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Helper function to create empty cart element
    function createEmptyCartEl() {
        const p = document.createElement('p');
        p.id = 'emptyCart';
        p.className = 'text-sm text-gray-400 text-center py-6';
        p.innerHTML = '<i class="fas fa-shopping-cart text-3xl text-gray-200 mb-2 block"></i>Cart is empty';
        return p;
    }

    // Get settings from PHP
    const SERVICE_PERCENT = {{ $servicePercent ?? 0 }};
    const TAX_PERCENT = {{ $taxPercent ?? 0 }};
    
    // Initialize cart
    let cart = {};

    function toggleOrderTypeFields() {
        const type = document.querySelector('input[name="order_type"]:checked').value;
        const tableCont = document.getElementById('tableSelectorContainer');
        const deliveryCont = document.getElementById('deliveryInfoContainer');
        const tableSelect = document.getElementById('tableSelect');
        const deliveryAddr = document.getElementById('delivery_address');

        if (type === 'dining') {
            tableCont.classList.remove('hidden');
            deliveryCont.classList.add('hidden');
            tableSelect.required = true;
            deliveryAddr.required = false;
        } else if (type === 'delivery') {
            tableCont.classList.add('hidden');
            deliveryCont.classList.remove('hidden');
            tableSelect.required = false;
            deliveryAddr.required = true;
        } else {
            // Takeaway
            tableCont.classList.add('hidden');
            deliveryCont.classList.add('hidden');
            tableSelect.required = false;
            deliveryAddr.required = false;
        }
    }

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = { 
                name: name, 
                price: price, 
                qty: 1,
                notes: ''
            };
        }
        renderCart();
    }

    function changeQty(id, delta) {
        if (!cart[id]) return;
        
        cart[id].qty += delta;
        
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
        
        renderCart();
    }

    function updateItemNote(id, note) {
        if (cart[id]) cart[id].notes = note;
    }

    function renderCart() {
    const cartItemsEl = document.getElementById('cartItems');
    const submitBtn = document.getElementById('submitBtn');
    const cartCount = document.getElementById('cartCount');

    const keys = Object.keys(cart);
    cartCount.textContent = keys.length + (keys.length === 1 ? ' item' : ' items');

    if (keys.length === 0) {
        cartItemsEl.innerHTML = `
            <div id="emptyCart" class="text-center py-12">
                <i class="fas fa-shopping-cart text-4xl text-gray-200 mb-3 block"></i>
                <span class="block font-medium text-gray-400">Cart is empty</span>
                <span class="text-xs text-gray-300 mt-1">Click on menu items to add them</span>
            </div>
        `;
        submitBtn.disabled = true;
        updateTotals(0);
        return;
    }

    let subtotal = 0;
    let html = '';
    
    keys.forEach((id, idx) => {
        const item = cart[id];
        const lineTotal = item.price * item.qty;
        subtotal += lineTotal;
        
        html += `
            <div class="py-4 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 truncate">${item.name}</p>
                        <p class="text-xs text-gray-500">Rs. ${item.price.toFixed(2)} / unit</p>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                        <button type="button" onclick="changeQty(${id}, -1)" class="w-6 h-6 rounded bg-white text-gray-600 hover:text-red-500 flex items-center justify-center shadow-sm transition-colors">
                            <i class="fas fa-minus text-[10px]"></i>
                        </button>
                        <span class="w-6 text-center font-bold text-sm">${item.qty}</span>
                        <button type="button" onclick="changeQty(${id}, 1)" class="w-6 h-6 rounded bg-white text-gray-600 hover:text-blue-500 flex items-center justify-center shadow-sm transition-colors">
                            <i class="fas fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" name="items[${idx}][notes]" value="${item.notes}" oninput="updateItemNote(${id}, this.value)" 
                           placeholder="Item instructions..." class="flex-1 text-xs border-0 bg-gray-50 rounded-lg h-8 px-2 focus:ring-1 focus:ring-blue-400">
                    <div class="text-sm font-bold text-gray-900">Rs. ${lineTotal.toFixed(2)}</div>
                </div>
                <input type="hidden" name="items[${idx}][menu_item_id]" value="${id}">
                <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">
            </div>
        `;
    });

    cartItemsEl.innerHTML = html;
    submitBtn.disabled = false;
    updateTotals(subtotal);
}

    function updateTotals(subtotal) {
        const service = subtotal * SERVICE_PERCENT / 100;
        const tax = (subtotal + service) * TAX_PERCENT / 100;
        const total = subtotal + service + tax;
        
        document.getElementById('subtotalDisplay').textContent = 'Rs. ' + subtotal.toFixed(2);
        
        const serviceDisplay = document.getElementById('serviceDisplay');
        if (serviceDisplay) serviceDisplay.textContent = 'Rs. ' + service.toFixed(2);
        
        const taxDisplay = document.getElementById('taxDisplay');
        if (taxDisplay) taxDisplay.textContent = 'Rs. ' + tax.toFixed(2);
        
        document.getElementById('totalDisplay').textContent = 'Rs. ' + total.toFixed(2);
    }

    // Menu search functionality
    document.addEventListener('DOMContentLoaded', function() {
        toggleOrderTypeFields(); // Initial call
        
        const searchInput = document.getElementById('menuSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                document.querySelectorAll('.menu-item-btn').forEach(btn => {
                    const name = btn.dataset.name || '';
                    btn.style.display = name.includes(q) ? 'flex' : 'none';
                });
                
                document.querySelectorAll('.category-block').forEach(block => {
                    const visible = [...block.querySelectorAll('.menu-item-btn')].some(b => b.style.display !== 'none');
                    block.style.display = visible ? 'block' : 'none';
                });
            });
        }

        // Form submit validation
        // Form submit validation & AJAX
        const form = document.getElementById('orderForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const type = document.querySelector('input[name="order_type"]:checked').value;
                
                if (type === 'dining' && !document.getElementById('tableSelect').value) {
                    window.Alert.error('Please select a table for dining orders.');
                    return false;
                }
                
                if (type === 'delivery' && !document.getElementById('delivery_address').value) {
                    window.Alert.error('Please provide a delivery address.');
                    return false;
                }

                if (Object.keys(cart).length === 0) {
                    window.Alert.error('Please add items to the cart.');
                    return false;
                }

                const btn = document.getElementById('submitBtn');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Placing Order...';

                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        if (data.errors) {
                            const errors = Object.values(data.errors).flat().join('\n');
                            throw new Error(errors);
                        }
                        throw new Error(data.message || 'Failed to place order.');
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        window.Alert.success(data.message).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    }
                })
                .catch(err => {
                    window.Alert.error(err.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        }
    });
</script>
@endpush
@endsection