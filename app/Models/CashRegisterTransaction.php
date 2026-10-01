<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterTransaction extends BaseModel
{
    use HasRestaurant;

    const TYPE_CASH_IN = 'cash_in';
    const TYPE_CASH_OUT = 'cash_out';
    const TYPE_EXPENSE = 'expense';

    protected $fillable = ['restaurant_id', 'cash_register_session_id', 'type', 'amount', 'reason', 'created_by'];

    protected $casts = [
        'amount' => 'float',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class, 'cash_register_session_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
