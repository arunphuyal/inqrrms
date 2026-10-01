<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasRestaurant;

class CashRegisterDenomination extends BaseModel
{
    use HasRestaurant;

    protected $fillable = ['restaurant_id', 'value', 'type', 'sort_order', 'is_active'];

    protected $casts = [
        'value' => 'float',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
