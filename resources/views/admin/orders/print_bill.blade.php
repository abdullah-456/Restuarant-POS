<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 4mm;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed { border-bottom: 1px dashed #000; margin: 5px 0; }
        .double { border-bottom: 3px double #000; margin: 6px 0; }
        .header-name { font-size: 15px; font-weight: bold; letter-spacing: 1px; }
        .invoice-label { font-size: 12px; font-weight: bold; margin: 8px 0 4px; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; border-bottom: 1px dashed #000; padding: 3px 0 5px; font-size: 10px; text-transform: uppercase; }
        td { padding: 2px 0; vertical-align: top; font-size: 11px; }
        .row { display: flex; justify-content: space-between; gap: 8px; margin-top: 2px; }
        .row-label { flex: 1; text-align: right; }
        .row-value { width: 70px; text-align: right; flex-shrink: 0; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background:#f5f5f5; padding:10px; margin-bottom:15px; text-align:center; border-radius:4px;">
        <button onclick="window.print()" style="padding:8px 20px; font-weight:bold; cursor:pointer; background:#333; color:white; border:none; border-radius:4px;">🖨️ PRINT INVOICE</button>
        &nbsp;
        <button onclick="window.close()" style="padding:8px 16px; cursor:pointer; background:#888; color:white; border:none; border-radius:4px;">✕ Close</button>
    </div>

    <div class="text-center">
        <div class="header-name">BOMBAY BYTES RESTAURANT</div>
        <div style="font-size:9px; margin-top:2px;">12/A Palace Road, Near Metro Station</div>
        <div style="font-size:9px;">Mumbai, Maharashtra</div>
        <div style="font-size:9px;">Phone: 022-45863211 | GSTIN: 27AAABR0685F1Z1</div>
        <div class="invoice-label">RETAIL INVOICE</div>
    </div>

    <div class="dashed"></div>
    <div>Date: {{ $order->created_at->format('d/m/Y  h:i A') }}</div>
    <div>Bill No: #{{ $order->order_number }}</div>
    @php $p = $order->payments->first(); @endphp
    <div>Payment: {{ strtoupper($p->payment_method ?? 'CASH') }}</div>
    @if($order->restaurant_table_id)
        <div>Table: {{ optional($order->table)->name }}</div>
    @endif
    @if($order->waiter)
        <div>Server: {{ $order->waiter->name }}</div>
    @endif
    <div class="dashed"></div>

    <table>
        <thead>
            <tr class="bold">
                <th style="width:52%;">Item</th>
                <th style="width:12%; text-align:center;">Qty</th>
                <th style="width:36%; text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                <td style="text-align:right;">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="dashed"></div>

    <div class="row">
        <div class="row-label bold">Sub Total</div>
        <div class="row-value bold">{{ number_format($order->subtotal, 2) }}</div>
    </div>

    @if($order->service_charge_amount > 0)
        <div class="row" style="font-size:9px;">
            <div class="row-label">Service Charge</div>
            <div class="row-value">{{ number_format($order->service_charge_amount, 2) }}</div>
        </div>
    @endif

    @if($order->tax_amount > 0)
        @php $halfTax = $order->tax_amount / 2; $taxRate = $order->tax_rate ?? 0; @endphp
        @if($taxRate > 0)
            <div class="row" style="font-size:9px;">
                <div class="row-label">CGST @ {{ $taxRate / 2 }}%</div>
                <div class="row-value">{{ number_format($halfTax, 2) }}</div>
            </div>
            <div class="row" style="font-size:9px;">
                <div class="row-label">SGST @ {{ $taxRate / 2 }}%</div>
                <div class="row-value">{{ number_format($halfTax, 2) }}</div>
            </div>
        @else
            <div class="row" style="font-size:9px;">
                <div class="row-label">Tax</div>
                <div class="row-value">{{ number_format($order->tax_amount, 2) }}</div>
            </div>
        @endif
    @endif

    <div class="double"></div>
    <div class="row bold" style="font-size:14px;">
        <div class="row-label">TOTAL</div>
        <div class="row-value">Rs {{ number_format($order->total, 2) }}</div>
    </div>
    <div class="double"></div>

    @if($p)
        @php
            preg_match('/Tendered: Rs\. ([\d.]+)/', $p->notes ?? '', $matchT);
            preg_match('/Change: Rs\. ([\d.]+)/', $p->notes ?? '', $matchC);
            $tendered = isset($matchT[1]) ? (float)$matchT[1] : $p->amount;
            $change   = isset($matchC[1]) ? (float)$matchC[1] : 0;
        @endphp
        <div class="row">
            <div class="row-label">Tendered ({{ strtoupper($p->payment_method) }})</div>
            <div class="row-value">Rs {{ number_format($tendered, 2) }}</div>
        </div>
        @if($change > 0)
            <div class="row bold">
                <div class="row-label">Change Due</div>
                <div class="row-value">Rs {{ number_format($change, 2) }}</div>
            </div>
        @endif
    @endif

    <div style="text-align:center; margin-top:15px;">
        <div class="bold" style="font-size:13px; letter-spacing:2px;">THANK YOU!</div>
        <div style="font-size:9px; margin-top:2px;">Please visit us again</div>
    </div>

    <script>
        window.onload = function() {
            if (!window.location.search.includes('no_auto')) {
                setTimeout(() => window.print(), 500);
            }
        }
    </script>
</body>
</html>

