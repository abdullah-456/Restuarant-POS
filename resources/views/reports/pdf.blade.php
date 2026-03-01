<!DOCTYPE html>
<html>

<head>
    <title>Daily Sales Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            bg-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .summary {
            margin-top: 30px;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Range: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Table</th>
                <th>Waiter</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ optional($order->table)->name ?? 'N/A' }}</td>
                    <td>{{ optional($order->waiter)->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>Rs. {{ number_format($order->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p class="total">Total Sales: Rs. {{ number_format($orders->sum('total'), 2) }}</p>
        <p>Total Orders: {{ $orders->count() }}</p>
    </div>
</body>

</html>