<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'item_name',
        'item_price',
        'quantity',
        'notes',
        'subtotal',
        'is_new',
        'added_at',
        'status',
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_new' => 'boolean',
        'added_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}