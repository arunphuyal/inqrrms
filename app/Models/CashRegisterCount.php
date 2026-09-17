<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterCount extends BaseModel
{
    const STAGE_OPENING = 'opening';
    const STAGE_CLOSING = 'closing';

    protected $fillable = ['cash_register_session_id', 'cash_register_denomination_id', 'stage', 'quantity', 'subtotal'];

    protected $casts = [
        'quantity' => 'integer',
        'subtotal' => 'float',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class, 'cash_register_session_id');
    }

    public function denomination(): BelongsTo
    {
        return $this->belongsTo(CashRegisterDenomination::class, 'cash_register_denomination_id');
    }
}
