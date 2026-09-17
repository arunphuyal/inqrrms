<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegisterSession extends BaseModel
{
    // Only HasBranch, not HasRestaurant: see CashRegister for why.
    use HasBranch;

    protected $guarded = ['id'];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'approved_at' => 'datetime',
        'opening_amount' => 'float',
        'expected_closing_amount' => 'float',
        'counted_closing_amount' => 'float',
        'difference' => 'float',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashRegisterTransaction::class);
    }

    public function counts(): HasMany
    {
        return $this->hasMany(CashRegisterCount::class);
    }

    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
