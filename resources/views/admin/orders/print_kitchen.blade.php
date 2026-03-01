<!DOCTYPE html>
<html>

<head>
    <title>Kitchen Slip - {{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm 200mm;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            width: 72mm;
            margin: 0 auto;
            padding: 5mm;
            font-size: 14px;
            line-height: 1.2;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .dashed-line {
            border-bottom: 2px dashed #000;
            margin: 10px 0;
        }

        .order-head {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .type-badge {
            background: #000;
            color: #fff;
            padding: 2px 5px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 8px 0;
            border-bottom: 1px dotted #ccc;
        }

        .notes {
            font-size: 12px;
            font-style: italic;
            color: #333;
            margin-top: 4px;
            display: block;
        }

        .new-tag {
            border: 1px solid #000;
            padding: 1px 3px;
            font-size: 10px;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="text-center">
        <div class="bold order-head">KITCHEN SLIP</div>
        <div class="bold" style="font-size: 24px;">#{{ $order->order_number }}</div>
        <div class="type-badge">{{ strtoupper($order->order_type) }}</div>
    </div>
    <div style="margin-top: 10px;">
        <div>Date: {{ now()->format('d/m/Y H:i') }}</div>
        @if($order->restaurant_table_id)
            <div class="bold">Table: {{ optional($order->table)->name }}</div>
        @endif
        @if($order->order_type === 'delivery')
            <div style="font-size: 11px;">Delivery: {{ $order->delivery_address }}</div>
        @endif
    </div>
    <div class="dashed-line"></div>
    <table>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="bold" style="font-size: 18px; width: 20%;">{{ $item->quantity }} x</td>
                    <td>
                        @if($item->is_new) <span class="new-tag bold">NEW</span> @endif
                        <span class="bold" style="font-size: 16px;">{{ $item->item_name }}</span>
                        @if($item->notes) <span class="notes">*** {{ $item->notes }} ***</span> @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="dashed-line"></div>
    <div class="text-center bold" style="font-size: 10px; color: #666;">
        Printed at {{ now()->format('H:i:s') }}
        @if(request()->has('new_only')) (New Items Only) @endif
    </div>
    <script>
        window.onload = function () {
            window.electronAPI.printKitchen();
        }
    </script>
</body>

</html>