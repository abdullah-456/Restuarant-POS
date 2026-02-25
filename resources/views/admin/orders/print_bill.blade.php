<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page { size: 80mm 200mm; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 72mm; 
            margin: 0 auto; 
            padding: 5mm;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed-line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .double-line { border-bottom: 2px double #000; margin: 5px 0; }
        
        .header-title { font-size: 14px; margin-bottom: 2px; }
        .invoice-title { font-size: 12px; margin: 10px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; border-bottom: 1px dashed #000; padding: 5px 0; }
        td { padding: 3px 0; vertical-align: top; }
        
        .summary-row { display: flex; justify-content: flex-end; gap: 10px; margin-top: 2px; }
        .summary-label { flex: 1; text-align: right; }
        .summary-value { width: 70px; text-align: right; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #eee; padding: 10px; margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">PRINT INVOICE</button>
    </div>

    <div class="text-center">
        <div class="bold header-title">BOMBAY BYTES RESTAURANT</div>
        <div>12/A PALACE ROAD, NEAR METRO STATION,</div>
        <div>MUMBAI, MAHARASHTRA.</div>
        <div>PHONE : 022 45863211</div>
        <div>GSTIN : 27AAABR0685F1Z1</div>
        
        <div class="bold invoice-title">Retail Invoice</div>
    </div>

    <div>Date : {{ $order->created_at->format('d/m/Y, h:i A') }}</div>
    <div>Bill No: #{{ $order->order_number }}</div>
    @php $p = $order->payments->first(); @endphp
    <div>Payment: {{ strtoupper($p->payment_method ?? 'CASH') }}</div>
    @if($order->restaurant_table_id)
        <div>Table Ref : {{ optional($order->table)->name }}</div>
    @endif
    
    <div class="dashed-line"></div>
    <table>
        <thead>
            <tr class="bold">
                <th style="width: 50%;">Item</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 35%; text-align: right;">Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="dashed-line"></div>

    <div class="summary-row">
        <div class="summary-label bold">Sub Total</div>
        <div class="summary-value bold">{{ number_format($order->subtotal, 2) }}</div>
    </div>

    @if($order->tax_amount > 0)
        @php
            $tax = $order->tax_amount;
            $halfTax = $tax / 2;
        @endphp
        <div class="summary-row" style="font-size: 9px;">
            <div class="summary-label">CGST @ {{ $order->tax_rate / 2 }}%</div>
            <div class="summary-value">{{ number_format($halfTax, 2) }}</div>
        </div>
        <div class="summary-row" style="font-size: 9px;">
            <div class="summary-label">SGST @ {{ $order->tax_rate / 2 }}%</div>
            <div class="summary-value">{{ number_format($halfTax, 2) }}</div>
        </div>
    @endif

    <div class="double-line"></div>
    <div class="summary-row bold" style="font-size: 13px;">
        <div class="summary-label">TOTAL</div>
        <div class="summary-value">Rs {{ number_format($order->total, 2) }}</div>
    </div>
    <div class="double-line"></div>

    @if($p)
    <div class="summary-row">
        <div class="summary-label">Received ({{ strtoupper($p->payment_method) }}):</div>
        <div class="summary-value">Rs {{ number_format($p->amount, 2) }}</div>
    </div>
    @endif

    <div class="text-center" style="margin-top: 20px;">
        <div class="bold">THANK YOU!</div>
        <div style="font-size: 9px;">VISIT AGAIN</div>
    </div>

    <script>
        window.onload = function() {
            if (!window.location.search.includes('no_auto')) {
                window.print();
            }
        }
    </script>
</body>
</html>
