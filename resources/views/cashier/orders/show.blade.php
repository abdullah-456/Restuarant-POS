@extends('layouts.cashier')

@section('title', 'Process Payment - ' . $order->order_number)
@section('page-title', 'Process Payment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('cashier.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
        </a>
    </div>

    <div class="space-y-4">
        {{-- Order summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        <i class="fas fa-chair mr-1"></i>{{ optional($order->table)->name ?? 'N/A' }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-user mr-1"></i>{{ optional($order->waiter)->name ?? 'N/A' }}
                    </p>
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            {{-- Items --}}
            <div class="overflow-x-auto mb-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                            <th class="px-3 py-2">Item</th>
                            <th class="px-3 py-2 text-center">Qty</th>
                            <th class="px-3 py-2 text-right">Price</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-3 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="itemsTableBody">
                        @foreach($order->items as $item)
                            <tr id="item-row-{{ $item->id }}">
                                <td class="px-3 py-2.5 text-gray-800">
                                    <div class="font-medium text-gray-900">{{ $item->item_name }}</div>
                                    @if($item->menuItem && $item->menuItem->is_deal)
                                        <div id="constituents-{{ $item->id }}" class="text-[10px] text-gray-500 mt-0.5 italic">
                                            (
                                            @foreach($item->menuItem->dealItems as $di)
                                                <span data-base-qty="{{ $di->quantity }}">{{ $di->quantity * $item->quantity }}</span>x {{ $di->menuItem->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            )
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="updateQty({{ $item->id }}, -1)"
                                            class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full text-gray-600 transition">
                                            <i class="fas fa-minus text-[10px]"></i>
                                        </button>
                                        <span id="qty-{{ $item->id }}" class="w-6 text-center font-bold text-gray-800">{{ $item->quantity }}</span>
                                        <button type="button" onclick="updateQty({{ $item->id }}, 1)"
                                            class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full text-gray-600 transition">
                                            <i class="fas fa-plus text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-right text-gray-600">Rs. {{ number_format($item->item_price, 2) }}</td>
                                <td class="px-3 py-2.5 text-right font-medium text-gray-800" id="subtotal-{{ $item->id }}">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <button type="button" onclick="removeItem({{ $item->id }})"
                                        class="text-red-400 hover:text-red-600 p-1.5 transition">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="border-t border-gray-100 pt-3 space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span id="label-subtotal">Rs. {{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Discount</span>
                        <span id="label-discount">-Rs. {{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->service_charge_amount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Service Charge ({{ number_format($order->service_charge_rate) }}%)</span>
                        <span id="label-service">Rs. {{ number_format($order->service_charge_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->tax_amount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Tax ({{ number_format($order->tax_rate) }}%)</span>
                        <span id="label-tax">Rs. {{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-xl text-gray-900 border-t border-gray-200 pt-2 mt-2">
                    <span>Amount Due</span>
                    <span class="text-green-600" id="label-total">Rs. {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-credit-card mr-2 text-blue-600"></i>Collect Payment
            </h3>

            <form action="{{ route('cashier.orders.payment', $order) }}" method="POST" id="paymentForm">
                @csrf

                {{-- Payment method --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['cash' => 'fa-money-bill-wave', 'card' => 'fa-credit-card', 'online' => 'fa-globe'] as $method => $icon)
                            <label class="flex flex-col items-center gap-1.5 border-2 rounded-xl p-3 cursor-pointer transition payment-method-label
                                         {{ old('payment_method', 'cash') === $method ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                                <input type="radio" name="payment_method" value="{{ $method }}"
                                       class="sr-only" {{ old('payment_method', 'cash') === $method ? 'checked' : '' }}
                                       onchange="highlightMethod(this)">
                                <i class="fas {{ $icon }} text-xl {{ old('payment_method', 'cash') === $method ? 'text-blue-600' : 'text-gray-400' }}"></i>
                                <span class="text-xs font-medium capitalize {{ old('payment_method', 'cash') === $method ? 'text-blue-700' : 'text-gray-600' }}">{{ $method }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Amount tendered --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Amount Tendered <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rs.</span>
                        <input type="number" name="amount_tendered" id="amountTendered"
                               value="{{ old('amount_tendered', number_format($order->total, 2, '.', '')) }}"
                               min="{{ $order->total }}" step="0.01"
                               class="w-full pl-7 pr-4 py-3 border border-gray-300 rounded-xl text-lg font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount_tendered') border-red-500 @enderror"
                               oninput="calcChange()">
                    </div>
                    @error('amount_tendered')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quick amounts --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    @php
                        $total = $order->total;
                        $suggestions = [
                        ceil($total), ceil($total/5)*5, ceil($total/10)*10, ceil($total/20)*20
                        ];
                        $suggestions = array_unique($suggestions);
                    @endphp
                    @foreach($suggestions as $s)
                        <button type="button" onclick="setAmount({{ $s }})"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-blue-700 rounded-lg text-sm font-medium transition">
                            Rs. {{ number_format($s, 2) }}
                        </button>
                    @endforeach
                </div>

                {{-- Change due --}}
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 mb-5">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-green-700">Change Due</span>
                        <span id="changeDue" class="text-2xl font-bold text-green-700">Rs. 0.00</span>
                    </div>
                </div>

                {{-- Reference --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reference / Note <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="reference" value="{{ old('reference') }}"
                           placeholder="Card last 4 digits, UPI ref, etc."
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-base transition flex items-center justify-center gap-2 shadow-sm shadow-green-200">
                    <i class="fas fa-check-circle"></i> Confirm Payment
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('submitBtn');
    const formData = new FormData(form);
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

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
                // Handle validation errors
                const errors = Object.values(data.errors).flat().join('\n');
                throw new Error(errors);
            }
            throw new Error(data.message || 'Payment failed');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            // Trigger Print
            const printUrl = `/admin/orders/${data.print_order_id}/print-bill`;
            window.open(printUrl, '_blank');
            
            window.Alert.success(data.message).then(() => {
                window.location.href = "{{ route('cashier.dashboard') }}";
            });
        }
    })
    .catch(err => {
        window.Alert.error(err.message);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

@push('scripts')
<script>
let ORDER_TOTAL = {{ $order->total }};

function updateQty(itemId, delta) {
    const qtySpan = document.getElementById(`qty-${itemId}`);
    let newQty = parseInt(qtySpan.textContent) + delta;
    if (newQty < 1) return;

    fetch(`{{ route('cashier.orders.update-item', [$order->id, ':item']) }}`.replace(':item', itemId), {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateUI(data.order);
        } else {
            window.Alert.error(data.message);
        }
    })
    .catch(err => window.Alert.error('Failed to update quantity'));
}

function removeItem(itemId) {
    window.Alert.confirm('Remove Item', 'Are you sure you want to remove this item from the bill?').then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('cashier.orders.remove-item', [$order->id, ':item']) }}`.replace(':item', itemId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        document.getElementById(`item-row-${itemId}`).remove();
                        updateUI(data.order);
                    }
                } else {
                    window.Alert.error(data.message);
                }
            });
        }
    });
}

function updateUI(order) {
    ORDER_TOTAL = parseFloat(order.total);
    
    // Update labels
    document.getElementById('label-subtotal').textContent = 'Rs. ' + parseFloat(order.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('label-total').textContent = 'Rs. ' + ORDER_TOTAL.toLocaleString(undefined, {minimumFractionDigits: 2});
    
    if (document.getElementById('label-tax')) {
        document.getElementById('label-tax').textContent = 'Rs. ' + parseFloat(order.tax_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
    }
    if (document.getElementById('label-service')) {
        document.getElementById('label-service').textContent = 'Rs. ' + parseFloat(order.service_charge_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
    }
    if (document.getElementById('label-discount')) {
        document.getElementById('label-discount').textContent = '-Rs. ' + parseFloat(order.discount_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    // Update item row subtotals and qties
    order.items.forEach(item => {
        const qtySpan = document.getElementById(`qty-${item.id}`);
        const subtotalSpan = document.getElementById(`subtotal-${item.id}`);
        if (qtySpan) qtySpan.textContent = item.quantity;
        if (subtotalSpan) subtotalSpan.textContent = 'Rs. ' + parseFloat(item.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2});
        
        // Update deal constituents if they exist
        const constituentsDiv = document.getElementById(`constituents-${item.id}`);
        if (constituentsDiv) {
            constituentsDiv.querySelectorAll('span[data-base-qty]').forEach(span => {
                const baseQty = parseInt(span.getAttribute('data-base-qty'));
                span.textContent = baseQty * item.quantity;
            });
        }
    });

    // Update payment form
    document.getElementById('amountTendered').value = ORDER_TOTAL.toFixed(2);
    calcChange();
}

function calcChange() {
    const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
    const change = Math.max(0, tendered - ORDER_TOTAL);
    document.getElementById('changeDue').textContent = 'Rs. ' + change.toFixed(2);
}

function setAmount(val) {
    document.getElementById('amountTendered').value = val.toFixed(2);
    calcChange();
}

function highlightMethod(radio) {
    document.querySelectorAll('.payment-method-label').forEach(label => {
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-200');
        label.querySelector('i').classList.remove('text-blue-600');
        label.querySelector('i').classList.add('text-gray-400');
        label.querySelector('span').classList.remove('text-blue-700');
        label.querySelector('span').classList.add('text-gray-600');
    });
    const selected = radio.closest('.payment-method-label');
    selected.classList.add('border-blue-500', 'bg-blue-50');
    selected.classList.remove('border-gray-200');
    selected.querySelector('i').classList.add('text-blue-600');
    selected.querySelector('i').classList.remove('text-gray-400');
    selected.querySelector('span').classList.add('text-blue-700');
    selected.querySelector('span').classList.remove('text-gray-600');
}

// Init
calcChange();
</script>
@endpush
@endsection
