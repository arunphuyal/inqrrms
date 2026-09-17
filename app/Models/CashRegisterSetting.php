<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterSetting extends BaseModel
{
    protected $fillable = [
        'restaurant_id',
        'require_denomination_count',
        'require_approval_on_discrepancy',
        'discrepancy_threshold',
        'always_require_approval',
    ];

    protected $casts = [
        'require_denomination_count' => 'boolean',
        'require_approval_on_discrepancy' => 'boolean',
        'always_require_approval' => 'boolean',
        'discrepancy_threshold' => 'float',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
