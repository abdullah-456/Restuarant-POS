@extends('layouts.waiter')

@section('title', 'Create Order')
@section('page-title', 'New Order')

@push('styles')
<style>
    /* ────── Layout: menu + persistent cart ──────────────────── */
    /*
     * On mobile: the page is split top (menu) / bottom (cart).
     * The cart is ALWAYS visible – it never slides away.
     * On desktop (lg+): side-by-side flex layout.
     */

    /* mobile: order-type card padding fix */
    .order-type-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        padding: 8px 4px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        transition: border-color .15s, background .15s;
        cursor: pointer;
        user-select: none;
    }
    input[type="radio"]:checked + .order-type-card {
        border-color: #10b981;
        background: #ecfdf5;
    }

    /* ── Menu item card ──────────────────────────────────────── */
    .menu-item-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s, background .15s;
        width: 100%;
        text-align: left;
    }
    .menu-item-card:hover,
    .menu-item-card:active {
        border-color: #10b981;
        background: #f0fdf4;
        box-shadow: 0 2px 8px rgba(16,185,129,.12);
    }
    .menu-item-card .item-icon {
        width: 40px; height: 40px;
        border-radius: 8px;
        flex-shrink: 0;
        overflow: hidden;
        background: linear-gradient(135deg,#d1fae5,#a7f3d0);
        display: flex; align-items: center; justify-content: center;
    }
    .menu-item-card .item-icon img { width:100%; height:100%; object-fit:cover; }
    .menu-item-card .plus-btn {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: background .15s, border-color .15s;
        min-height: unset !important; min-width: unset !important;
    }
    .menu-item-card:hover .plus-btn { background: #10b981; border-color: #10b981; }
    .menu-item-card:hover .plus-btn i { color: #fff; }
    .plus-btn i { color: #10b981; font-size: 11px; }

    /* ── Cart items ──────────────────────────────────────────── */
    .cart-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 8px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .cart-item:last-child { border-bottom: none; }
    .qty-btn {
        width: 24px; height: 24px;
        border-radius: 6px;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: none;
        flex-shrink: 0;
        transition: background .15s;
        min-height: unset !important; min-width: unset !important;
    }
    .qty-btn:hover { background: #e5e7eb; }
    .qty-btn.minus:hover { background: #fee2e2; }

    /* ── Mobile bottom cart panel ────────────────────────────── */
    @media (max-width: 1023px) {
        /* Remove desktop cart, show mobile panel */
        /* #desktop-cart { display: none !important; } */

        /* Reserve bottom space so content doesn't hide behind cart */
        #menu-section { padding-bottom: 0; }

        /* The persistent bottom cart */
        #mobile-cart-panel {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-top: 2px solid #e5e7eb;
            max-height: 42vh;
        }
    }
    @media (min-width: 1024px) {
    #desktop-cart { 
        display: flex !important; 
        width: 220px;
        flex-shrink: 0;
        height: 600px; /* Fixed height */
        overflow: hidden;
        align-self: flex-start;
    }
    /* #mobile-cart-panel { display: none !important; } */
}

    @media (min-width: 1024px) {
        #desktop-cart { display: flex !important; }
        #mobile-cart-panel { display: none !important; }
    }

    /* ── "Add to cart" flash on the item card ────────────────── */
    @keyframes addedFlash {
        0%   { transform: scale(1); }
        40%  { transform: scale(1.06); }
        100% { transform: scale(1); }
    }
    .added-flash { animation: addedFlash 0.22s ease; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <form id="orderForm" action="{{ route('waiter.orders.store') }}" method="POST">
        @csrf

        {{-- ── MOBILE LAYOUT: stacked (menu on top, cart below) ── --}}
        {{-- ── DESKTOP LAYOUT: flex side-by-side ─────────────────── --}}
        <div class="flex flex-col lg:flex-row gap-4 lg:items-start">

            {{-- ══════════ LEFT / MAIN: Menu Section ══════════ --}}
            <div class="flex-1 min-w-0 space-y-3" id="menu-section">

                {{-- Order Type + Table --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">

                    {{-- Order Type radio buttons --}}
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Order Type</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['dining' => ['icon' => 'fa-chair', 'label' => 'Dining'],
                                      'takeaway' => ['icon' => 'fa-shopping-bag', 'label' => 'Takeaway'],
                                      'delivery' => ['icon' => 'fa-truck', 'label' => 'Delivery']] as $type => $cfg)
                            <label class="cursor-pointer block">
                                <input type="radio" name="order_type" value="{{ $type }}" class="peer sr-only"
                                       {{ $type === 'dining' ? 'checked' : '' }}
                                       onchange="toggleOrderTypeFields()">
                                <div class="flex flex-col items-center gap-1 py-2 px-1 border-2 border-gray-200 rounded-xl
                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition select-none">
                                    <i class="fas {{ $cfg['icon'] }} text-gray-400 peer-checked:text-emerald-600 text-sm"></i>
                                    <span class="text-xs font-semibold text-gray-600 peer-checked:text-emerald-700">{{ $cfg['label'] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Table selector --}}
                    <div id="tableSelectorContainer">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">
                            <i class="fas fa-chair text-emerald-500 mr-1"></i>Select Table <span class="text-red-500">*</span>
                        </label>
                        <select name="restaurant_table_id" id="tableSelect"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('restaurant_table_id') border-red-500 @enderror">
                            <option value="">— Choose a table —</option>
                            @foreach($tables as $t)
                            <option value="{{ $t->id }}"
                                @if(optional($selectedTable)->id == $t->id) selected @endif
                                @if($t->status === 'occupied') disabled @endif>
                                {{ $t->name }} ({{ $t->capacity }} seat{{ $t->capacity > 1 ? 's' : '' }})
                                @if($t->status !== 'available') — {{ ucfirst($t->status) }} @endif
                            </option>
                            @endforeach
                        </select>
                        @error('restaurant_table_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Delivery info --}}
                    <div id="deliveryInfoContainer" class="hidden space-y-2 mt-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Customer Phone</label>
                            <input type="text" name="customer_phone" placeholder="e.g. 0300-1234567"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                Delivery Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="delivery_address" id="delivery_address"
                                   placeholder="Full street address"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                {{-- Search --}}
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" id="menuSearch" placeholder="Search menu items…"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white shadow-sm">
                </div>

                {{-- Menu categories + items --}}
                <div class="space-y-4" id="menuList">
                    @foreach($categories as $category)
                    <div class="category-block" data-category="{{ strtolower($category->name) }}">
                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1 flex items-center gap-2">
                            <span class="flex-1 h-px bg-gray-100"></span>
                            {{ $category->name }}
                            <span class="flex-1 h-px bg-gray-100"></span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2">
                            @foreach($category->menuItems as $item)
                            <button type="button"
                                    onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, this)"
                                    class="menu-item-card menu-item-btn"
                                    data-name="{{ strtolower($item->name) }}">
                                <div class="item-icon">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                                    @else
                                        <i class="fas fa-utensils text-emerald-400 text-sm"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $item->name }}</p>
                                    <p class="text-emerald-600 font-bold text-sm">Rs. {{ number_format($item->price, 0) }}</p>
                                </div>
                                <div class="plus-btn"><i class="fas fa-plus"></i></div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if($categories->isEmpty())
                    <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                        <i class="fas fa-utensils text-4xl text-gray-200 mb-3 block"></i>
                        <p class="text-gray-400 text-sm">No menu items. Ask admin to add categories & items.</p>
                    </div>
                    @endif
                </div>

                {{-- Bottom spacer on mobile so items aren't hidden behind the cart --}}
                <div class="lg:hidden h-4"></div>
            </div>

            {{-- ══════════ RIGHT: Cart (Desktop sticky sidebar) ══════════ --}}
            <div id="desktop-cart" class="lg:w-80 xl:w-96 flex-shrink-0">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-20 flex flex-col"
                     style="max-height: calc(100vh - 100px);">
                    @include('waiter.orders._cart_panel')
                </div>
            </div>
        </div>

        <div id="hiddenCartInputs"></div>
    </form>
</div>

{{-- ══════════ MOBILE: Always-visible bottom cart ══════════ --}}
<div id="mobile-cart-panel" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 shadow-2xl"
     style="display:none;">
    {{-- Drag handle + toggle --}}
    <div class="flex items-center justify-between bg-emerald-600 px-4 py-2 cursor-pointer"
         onclick="toggleMobileCart()">
        <div class="flex items-center gap-2">
            <i class="fas fa-shopping-cart text-white text-sm"></i>
            <span class="text-white text-sm font-bold">Cart</span>
            <span id="mobile-cart-badge" class="bg-white text-emerald-700 text-xs font-black w-5 h-5 rounded-full flex items-center justify-center leading-none">0</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-white text-xs font-semibold" id="mobile-cart-total-preview">Rs. 0</span>
            <i class="fas fa-chevron-up text-white text-xs transition-transform duration-200" id="cart-chevron"></i>
        </div>
    </div>

    {{-- Cart content (collapsible) --}}
    <div id="mobile-cart-content" class="bg-white overflow-hidden transition-all duration-300"
         style="max-height: 50vh; display: flex; flex-direction: column;">
        <div id="cart-items-mobile" class="flex-1 overflow-y-auto px-4 py-2 min-h-0"
             style="max-height: 25vh;">
            <div class="text-center py-6 text-gray-300">
                <i class="fas fa-shopping-cart text-2xl mb-1 block"></i>
                <p class="text-xs text-gray-400">Tap items above to add them</p>
            </div>
        </div>

        {{-- Totals + submit --}}
        <div class="px-4 pt-2 pb-safe border-t border-gray-100 bg-white flex-shrink-0 space-y-1">
            <div class="flex justify-between text-xs text-gray-500">
                <span>Subtotal</span>
                <span class="cart-subtotal font-medium text-gray-700">Rs. 0.00</span>
            </div>
            @if(\App\Models\Setting::get('service_charge_percent', 0) > 0)
            <div class="flex justify-between text-xs text-gray-500">
                <span>Service ({{ \App\Models\Setting::get('service_charge_percent', 0) }}%)</span>
                <span class="cart-service font-medium text-gray-700">Rs. 0.00</span>
            </div>
            @endif
            @if(\App\Models\Setting::get('tax_percent', 0) > 0)
            <div class="flex justify-between text-xs text-gray-500">
                <span>Tax ({{ \App\Models\Setting::get('tax_percent', 0) }}%)</span>
                <span class="cart-tax font-medium text-gray-700">Rs. 0.00</span>
            </div>
            @endif
            <div class="flex justify-between items-center pt-1.5 border-t border-gray-200">
                <span class="text-sm font-bold text-gray-900">Total</span>
                <span class="cart-total text-base font-black text-emerald-600">Rs. 0.00</span>
            </div>
            <button type="button" onclick="submitOrder()"
                    class="submit-order-btn w-full py-2.5 mt-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 opacity-50 cursor-not-allowed"
                    disabled>
                <i class="fas fa-paper-plane text-xs"></i> Place Order
                <span class="cart-count text-xs font-normal opacity-80">(0 items)</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const SERVICE_PERCENT = {{ \App\Models\Setting::get('service_charge_percent', 0) }};
const TAX_PERCENT     = {{ \App\Models\Setting::get('tax_percent', 0) }};
let cart = {};
let mobileCartExpanded = true;

// ── Mobile cart toggle ────────────────────────────────────────
function toggleMobileCart() {
    mobileCartExpanded = !mobileCartExpanded;
    const content  = document.getElementById('mobile-cart-content');
    const chevron  = document.getElementById('cart-chevron');
    content.style.display = mobileCartExpanded ? 'flex' : 'none';
    chevron.style.transform = mobileCartExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
}

// ── Show mobile cart on load (small screens only) ─────────────
function initMobileCart() {
    if (window.innerWidth < 1024) {
        document.getElementById('mobile-cart-panel').style.display = 'flex';
        document.getElementById('mobile-cart-panel').style.flexDirection = 'column';
    }
}

// ── Order type toggle ─────────────────────────────────────────
function toggleOrderTypeFields() {
    const type         = document.querySelector('input[name="order_type"]:checked')?.value;
    const tableCont    = document.getElementById('tableSelectorContainer');
    const deliveryCont = document.getElementById('deliveryInfoContainer');
    const tableSelect  = document.getElementById('tableSelect');
    const deliveryAddr = document.getElementById('delivery_address');

    if (type === 'dining') {
        tableCont.classList.remove('hidden');
        deliveryCont.classList.add('hidden');
        tableSelect.required = true;
        if (deliveryAddr) deliveryAddr.required = false;
    } else if (type === 'delivery') {
        tableCont.classList.add('hidden');
        deliveryCont.classList.remove('hidden');
        tableSelect.required = false;
        if (deliveryAddr) deliveryAddr.required = true;
    } else {
        tableCont.classList.add('hidden');
        deliveryCont.classList.add('hidden');
        tableSelect.required = false;
        if (deliveryAddr) deliveryAddr.required = false;
    }
}

// ── Cart functions ────────────────────────────────────────────
function addToCart(id, name, price, btnEl) {
    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { name, price, qty: 1, notes: '' };
    }
    renderCart();

    // Flash animation on the card
    if (btnEl) {
        btnEl.classList.add('added-flash');
        setTimeout(() => btnEl.classList.remove('added-flash'), 300);
    }

    // Auto-expand mobile cart if collapsed
    if (window.innerWidth < 1024 && !mobileCartExpanded) {
        mobileCartExpanded = true;
        document.getElementById('mobile-cart-content').style.display = 'flex';
        document.getElementById('cart-chevron').style.transform = 'rotate(0deg)';
    }
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function updateItemNote(id, note) {
    if (cart[id]) cart[id].notes = note;
}

function renderCart() {
    const keys     = Object.keys(cart);
    const totalQty = keys.reduce((s, k) => s + cart[k].qty, 0);

    // Mobile badge + total preview
    const badge = document.getElementById('mobile-cart-badge');
    if (badge) badge.textContent = totalQty;

    // Build item HTML
    function buildItemsHTML() {
        if (keys.length === 0) {
            return `<div class="text-center py-6 text-gray-300">
                        <i class="fas fa-shopping-cart text-2xl mb-1 block"></i>
                        <p class="text-xs text-gray-400">Tap items above to add them</p>
                    </div>`;
        }
        let html = '';
        keys.forEach((id, idx) => {
            const item      = cart[id];
            const lineTotal = item.price * item.qty;
            html += `
            <div class="cart-item">
                <div class="flex items-center gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-xs truncate">${item.name}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="changeQty(${id}, -1)" class="qty-btn minus"
                                style="min-height:unset;min-width:unset;">
                            <i class="fas fa-minus" style="font-size:9px;color:#9ca3af;"></i>
                        </button>
                        <span class="w-5 text-center font-bold text-sm text-gray-800">${item.qty}</span>
                        <button type="button" onclick="changeQty(${id}, 1)" class="qty-btn"
                                style="min-height:unset;min-width:unset;">
                            <i class="fas fa-plus" style="font-size:9px;color:#9ca3af;"></i>
                        </button>
                    </div>
                    <span class="text-xs font-bold text-gray-900 w-14 text-right flex-shrink-0">Rs.${lineTotal.toFixed(0)}</span>
                </div>
                <input type="text" placeholder="Note (optional)" value="${item.notes}"
                       oninput="updateItemNote(${id}, this.value)"
                       class="w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1 focus:ring-1 focus:ring-emerald-400 bg-gray-50 text-gray-700">
                <input type="hidden" name="items[${idx}][menu_item_id]" value="${id}">
                <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">
                ${item.notes ? `<input type="hidden" name="items[${idx}][notes]" value="${item.notes}">` : ''}
            </div>`;
        });
        return html;
    }

    // Update desktop panel
    const desktopEl = document.getElementById('cart-items-desktop');
    if (desktopEl) desktopEl.innerHTML = buildItemsHTML();

    // Update mobile panel
    const mobileEl = document.getElementById('cart-items-mobile');
    if (mobileEl) mobileEl.innerHTML = buildItemsHTML();

    // Totals
    let subtotal = keys.reduce((s, k) => s + cart[k].price * cart[k].qty, 0);
    const service = subtotal * SERVICE_PERCENT / 100;
    const tax     = (subtotal + service) * TAX_PERCENT / 100;
    const total   = subtotal + service + tax;

    document.querySelectorAll('.cart-subtotal').forEach(el => el.textContent = 'Rs. ' + subtotal.toFixed(2));
    document.querySelectorAll('.cart-service').forEach(el  => el.textContent = 'Rs. ' + service.toFixed(2));
    document.querySelectorAll('.cart-tax').forEach(el      => el.textContent = 'Rs. ' + tax.toFixed(2));
    document.querySelectorAll('.cart-total').forEach(el    => el.textContent = 'Rs. ' + total.toFixed(2));
    document.querySelectorAll('.cart-count').forEach(el    => el.textContent = '(' + totalQty + ' item' + (totalQty !== 1 ? 's' : '') + ')');

    // Mobile total preview in header
    const preview = document.getElementById('mobile-cart-total-preview');
    if (preview) preview.textContent = 'Rs. ' + total.toFixed(0);

    const disabled = keys.length === 0;
    document.querySelectorAll('.submit-order-btn').forEach(btn => {
        btn.disabled = disabled;
        btn.classList.toggle('opacity-50', disabled);
        btn.classList.toggle('cursor-not-allowed', disabled);
    });
}

// ── Submit ────────────────────────────────────────────────────
function submitOrder() {
    const type = document.querySelector('input[name="order_type"]:checked')?.value;
    if (type === 'dining' && !document.getElementById('tableSelect').value) {
        window.Alert.error('Please select a table for dining orders.');
        return;
    }
    if (type === 'delivery' && !document.getElementById('delivery_address')?.value) {
        window.Alert.error('Please provide a delivery address.');
        return;
    }
    if (Object.keys(cart).length === 0) {
        window.Alert.error('Please add at least one item to the cart.');
        return;
    }

    const form = document.getElementById('orderForm');
    document.querySelectorAll('.submit-order-btn').forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i>Placing…';
    });

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(form)
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) {
            const errors = data.errors
                ? Object.values(data.errors).flat().join('\n')
                : (data.message || 'Order submission failed.');
            throw new Error(errors);
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
        window.Alert.error(err.message || 'Something went wrong. Please try again.');
        document.querySelectorAll('.submit-order-btn').forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-1.5"></i>Place Order <span class="cart-count text-xs font-normal opacity-80">(0 items)</span>';
        });
        renderCart(); // re-sync count label
    });
}

// ── Search ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    toggleOrderTypeFields();
    renderCart();
    initMobileCart();

    window.addEventListener('resize', () => {
        const panel = document.getElementById('mobile-cart-panel');
        if (!panel) return;
        if (window.innerWidth < 1024) {
            panel.style.display = 'flex';
            panel.style.flexDirection = 'column';
        } else {
            panel.style.display = 'none';
        }
    });

    document.getElementById('menuSearch').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.menu-item-btn').forEach(btn => {
            btn.style.display = (btn.dataset.name || '').includes(q) ? '' : 'none';
        });
        document.querySelectorAll('.category-block').forEach(block => {
            const anyVisible = [...block.querySelectorAll('.menu-item-btn')].some(b => b.style.display !== 'none');
            block.style.display = anyVisible ? '' : 'none';
        });
    });
});
</script>
@endpush