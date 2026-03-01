<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?: now()->toDateString();
        $this->endDate = $endDate ?: now()->toDateString();
    }

    public function collection()
    {
        return Order::with(['table', 'waiter'])
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                \Carbon\Carbon::parse($this->startDate)->startOfDay(),
                \Carbon\Carbon::parse($this->endDate)->endOfDay()
            ])
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
