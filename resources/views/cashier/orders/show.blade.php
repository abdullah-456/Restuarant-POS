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
                            <th class="px-3 py-2 text-right">Unit</th>
                            <th class="px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-3 py-2.5 text-gray-800">{{ $item->item_name }}</td>
                                <td class="px-3 py-2.5 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-600">Rs. {{ number_format($item->item_price, 2) }}</td>
                                <td class="px-3 py-2.5 text-right font-medium text-gray-800">Rs. {{ number_format($item->item_price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="border-t border-gray-100 pt-3 space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rs. {{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Discount</span>
                        <span>-Rs. {{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
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
                <div class="flex justify-between font-bold text-xl text-gray-900 border-t border-gray-200 pt-2 mt-2">
                    <span>Amount Due</span>
                    <span class="text-green-600">Rs. {{ number_format($order->total, 2) }}</span>
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
const ORDER_TOTAL = {{ $order->total }};

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
