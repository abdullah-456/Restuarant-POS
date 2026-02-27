<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'restaurant_table_id',
        'waiter_id',
        'order_type',
        'delivery_address',
        'customer_phone',
        'status',
        'notes',
        'subtotal',
        'discount_amount',
        'service_charge_rate',
        'service_charge_amount',
        'tax_rate',
        'tax_amount',
        'total',
        'total_paid',
        'remaining_amount',
        'confirmed_at',
        'ready_at',
        'paid_at',
        'modified_at',
        'modified_by',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'ready_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function table()
{
    return $this->belongsTo(\App\Models\RestaurantTable::class, 'restaurant_table_id');
}

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

