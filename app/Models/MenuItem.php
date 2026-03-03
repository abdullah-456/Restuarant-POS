<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_active',
        'is_deal',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_deal' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function dealItems()
    {
        return $this->hasMany(DealItem::class, 'deal_id');
    }

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'deal_items', 'deal_id', 'menu_item_id')->withPivot('quantity');
    }
}

