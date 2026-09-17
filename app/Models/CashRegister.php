<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends BaseModel
{
    // Only HasBranch, not HasRestaurant: both traits define booted() and
    // can't be combined on one model (see KotPlace for the same pattern).
    use HasBranch;

    protected $fillable = ['restaurant_id', 'branch_id', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CashRegisterSession::class);
    }

    public function openSession(): ?CashRegisterSession
    {
        return $this->sessions()->where('status', 'open')->latest('opened_at')->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
