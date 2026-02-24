<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'status',
        'is_active',
    ];

    public function activeOrders()
    {
        return $this->hasMany(Order::class, 'restaurant_table_id')
            ->whereIn('status', ['draft', 'confirmed', 'preparing', 'ready']);
    }
}

