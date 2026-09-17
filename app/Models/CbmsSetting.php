<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbmsSetting extends BaseModel
{
    protected $fillable = [
        'restaurant_id',
        'is_enabled',
        'mode',
        'username',
        'password',
        'seller_pan_override',
        'fiscal_year',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'password' => 'encrypted',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
