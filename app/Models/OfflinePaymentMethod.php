<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OfflinePaymentMethod extends BaseModel
{
    protected $fillable = ['restaurant_id', 'name', 'description', 'status', 'qr_code_image'];

    protected $appends = ['qr_code_image_url'];

    const QR_CODE_FOLDER = 'offline-payment-qr-codes';

    /**
     * Relationship with Restaurant
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function qrCodeImageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->qr_code_image ? asset_url_local_s3(self::QR_CODE_FOLDER . '/' . $this->qr_code_image) : '';
        });
    }

    /**
     * Relationship with OfflinePlanChange
     */
    public function offlinePlanChanges()
    {
        return $this->hasMany(OfflinePlanChange::class, 'offline_method_id');
    }

    /**
     * Scope to get only active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only enabled payment methods (alias for active)
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', 'active');
    }
}
