{{-- ══════════════════════════════════════════════════════
     Cart Panel – Desktop sidebar only.
     Mobile cart is inlined directly in create.blade.php.
════════════════════════════════════════════════════ --}}

{{-- Header --}}
<div class="px-4 pt-4 pb-2.5 border-b border-gray-100 flex-shrink-0">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-emerald-600" style="font-size:12px;"></i>
            </div>
            <h3 class="font-bold text-gray-900 text-sm">Order Cart</h3>
        </div>
        <span class="cart-count text-xs text-gray-400 font-medium">0 items</span>
    </div>
</div>

{{-- Items list --}}
<div id="cart-items-desktop" class="flex-1 overflow-y-auto px-4 py-2" style="min-height:0;">
    <div class="text-center py-10 px-3">
        <i class="fas fa-shopping-cart text-3xl text-gray-200 mb-2 block"></i>
        <p class="text-sm text-gray-400 font-medium">Cart is empty</p>
        <p class="text-xs text-gray-300 mt-1">Tap items to add them</p>
    </div>
</div>

{{-- Totals + submit --}}
<div class="px-4 pb-4 pt-2 border-t border-gray-100 flex-shrink-0 space-y-1.5">
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
    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
        <span class="text-sm font-bold text-gray-900">Total</span>
        <span class="cart-total text-base font-black text-emerald-600">Rs. 0.00</span>
    </div>

    <button type="button"
            onclick="submitOrder()"
            class="submit-order-btn w-full py-2.5 mt-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 opacity-50 cursor-not-allowed"
            style="min-height:unset;"
            disabled>
        <i class="fas fa-paper-plane text-xs"></i>
        Place Order
        <span class="cart-count text-xs font-normal opacity-80">(0 items)</span>
    </button>
</div>
