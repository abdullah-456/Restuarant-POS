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
                {{-- Table selector --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-chair mr-1 text-blue-600"></i> Select Table <span class="text-red-500">*</span>
                    </label>
                    <select name="restaurant_table_id" id="tableSelect" required
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

                {{-- Search --}}
                <div class="mb-4">
                    <div class="relative flex items-center">
                        <i class="fas fa-search absolute left-5 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text" id="menuSearch" placeholder="     Search menu items..."
                            class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400">
                    </div>
                </div>

                {{-- Categories & Menu - With scrollable area and more columns --}}
                <div class="space-y-4 max-h-[calc(100vh-300px)] overflow-y-auto pr-2">
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
        {{-- Cart items - Scrollable area with fixed width placeholders --}}
        <div id="cartItems" class="p-5 overflow-y-auto flex-1 w-full">
            {{-- Empty state with same structure as filled cart to maintain width --}}
            <div id="emptyCart" class="w-full">
                {{-- Placeholder item 1 - mimics the structure of a cart item --}}
                <div class="flex items-center gap-2 py-3 border-b border-gray-50 opacity-0 h-16">
                    <div class="flex-1 min-w-0 max-w-[160px]">
                        <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-16"></div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                        <div class="w-8 h-4 bg-gray-200 rounded"></div>
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                    </div>
                    <div class="w-20 h-4 bg-gray-200 rounded flex-shrink-0"></div>
                </div>
                
                {{-- Placeholder item 2 - to fill space --}}
                <div class="flex items-center gap-2 py-3 border-b border-gray-50 opacity-0 h-16">
                    <div class="flex-1 min-w-0 max-w-[160px]">
                        <div class="h-4 bg-gray-200 rounded w-28 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                        <div class="w-8 h-4 bg-gray-200 rounded"></div>
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                    </div>
                    <div class="w-20 h-4 bg-gray-200 rounded flex-shrink-0"></div>
                </div>               
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

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = { 
                name: name, 
                price: price, 
                qty: 1 
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

    function renderCart() {
    const cartItemsEl = document.getElementById('cartItems');
    const submitBtn = document.getElementById('submitBtn');
    const cartCount = document.getElementById('cartCount');
    const hiddenInputs = document.getElementById('hiddenCartInputs');

    hiddenInputs.innerHTML = '';

    const keys = Object.keys(cart);
    cartCount.textContent = keys.length + (keys.length === 1 ? ' item' : ' items');

    if (keys.length === 0) {
        // Empty cart - show the placeholder structure
        let emptyHtml = `
            <div id="emptyCart" class="w-full relative min-h-[300px]">
                <div class="flex items-center gap-2 py-3 border-b border-gray-50 opacity-0 h-16">
                    <div class="flex-1 min-w-0 max-w-[160px]">
                        <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-16"></div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                        <div class="w-8 h-4 bg-gray-200 rounded"></div>
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                    </div>
                    <div class="w-20 h-4 bg-gray-200 rounded flex-shrink-0"></div>
                </div>
                <div class="flex items-center gap-2 py-3 border-b border-gray-50 opacity-0 h-16">
                    <div class="flex-1 min-w-0 max-w-[160px]">
                        <div class="h-4 bg-gray-200 rounded w-28 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-20"></div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                        <div class="w-8 h-4 bg-gray-200 rounded"></div>
                        <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                    </div>
                    <div class="w-20 h-4 bg-gray-200 rounded flex-shrink-0"></div>
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none" style="margin-top: 60px;">
                    <i class="fas fa-shopping-cart text-4xl text-gray-200 mb-3"></i>
                    <span class="block font-medium text-gray-400">Cart is empty</span>
                    <span class="text-xs text-gray-300 mt-1">Click on menu items to add them</span>
                </div>
            </div>
        `;
        
        cartItemsEl.innerHTML = emptyHtml;
        submitBtn.disabled = true;
        updateTotals(0);
        return;
    }

    // Hide empty cart placeholders and show actual items
    let subtotal = 0;
    let html = '';
    
    keys.forEach((id, idx) => {
        const item = cart[id];
        const lineTotal = item.price * item.qty;
        subtotal += lineTotal;
        
        html += `
            <div class="flex items-center gap-2 py-3 border-b border-gray-50 last:border-0 w-full">
                <div class="flex-1 min-w-0 max-w-[160px]">
                    <p class="text-base font-medium text-gray-800 truncate" title="${item.name}">${item.name}</p>
                    <p class="text-sm text-blue-600">Rs. ${item.price.toFixed(2)} each</p>
                    <input type="hidden" name="items[${idx}][menu_item_id]" value="${id}">
                    <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">
                    <input type="hidden" name="items[${idx}][notes]" value="">
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button type="button" onclick="changeQty(${id}, -1)"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 flex items-center justify-center text-sm transition flex-shrink-0">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="w-8 text-center text-base font-bold flex-shrink-0">${item.qty}</span>
                    <button type="button" onclick="changeQty(${id}, 1)"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 flex items-center justify-center text-sm transition flex-shrink-0">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="text-base font-semibold text-gray-800 flex-shrink-0 w-20 text-right">Rs. ${lineTotal.toFixed(2)}</div>
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
        console.log('DOM loaded - initializing search and form validation');
        
        const searchInput = document.getElementById('menuSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase();
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
        const form = document.getElementById('orderForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!document.getElementById('tableSelect').value) {
                    e.preventDefault();
                    alert('Please select a table before placing the order.');
                    return false;
                }
                
                if (Object.keys(cart).length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to your order.');
                    return false;
                }
            });
        }

        // Test if menu item buttons have onclick handlers
        const menuButtons = document.querySelectorAll('.menu-item-btn');
        console.log('Found', menuButtons.length, 'menu buttons');
        
        menuButtons.forEach((btn, index) => {
            console.log(`Button ${index} onclick:`, btn.getAttribute('onclick'));
        });
    });
</script>
@endpush
@endsection