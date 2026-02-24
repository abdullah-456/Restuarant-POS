<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date;

    public function __construct($date = null)
    {
        $this->date = $date ?: now()->toDateString();
    }

    public function collection()
    {
        return Order::with(['table', 'waiter'])
            ->whereDate('created_at', $this->date)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Table',
            'Waiter',
            'Status',
            'Subtotal',
            'Tax',
            'Service Charge',
            'Total',
            'Date'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            optional($order->table)->name ?? 'N/A',
            optional($order->waiter)->name ?? 'N/A',
            ucfirst($order->status),
            'Rs. ' . number_format($order->subtotal, 2),
            'Rs. ' . number_format($order->tax_amount, 2),
            'Rs. ' . number_format($order->service_charge_amount, 2),
            'Rs. ' . number_format($order->total, 2),
            $order->created_at->format('Y-m-d H:i')
        ];
    }
}
