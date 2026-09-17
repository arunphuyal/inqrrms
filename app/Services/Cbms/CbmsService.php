<?php

namespace App\Services\Cbms;

use App\Models\CbmsLog;
use App\Models\CbmsSetting;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates a single CBMS submission for an order: builds the payload,
 * calls the API, and records the outcome on both CbmsLog (audit trail /
 * retry queue) and the order itself (cbms_status, cbms_synced_at).
 */
class CbmsService
{
    public function __construct(
        private readonly CbmsClient $client,
        private readonly CbmsPayloadBuilder $payloadBuilder,
    ) {
    }

    /**
     * Post an issued bill/invoice to CBMS. Returns null if CBMS sync isn't
     * enabled or configured for this order's restaurant (not an error - the
     * feature is simply off).
     */
    public function postBill(Order $order): ?CbmsApiResponse
    {
        $setting = $this->readySettingFor($order);
        if (!$setting) {
            return null;
        }

        $log = $this->startLog($order, CbmsLog::TYPE_BILL);

        try {
            $payload = $this->payloadBuilder->forBill($order, $setting);
        } catch (\Throwable $e) {
            $this->finishLog($log, CbmsLog::STATUS_FAILED, null, $e->getMessage(), null);
            $this->markOrder($order, 'failed');
            throw $e;
        }

        $log->update(['request_payload' => $this->redact($payload)]);

        $response = $this->client->postBill($payload);

        // 101 = "bill already exists": a prior attempt already landed in
        // CBMS, so a retry hitting this is a success from our side.
        $synced = $response->isSuccess() || $response->code === '101';

        $this->finishLog($log, $synced ? CbmsLog::STATUS_SUCCESS : CbmsLog::STATUS_FAILED, $response->code, $response->message, $response->raw);
        $this->markOrder($order, $synced ? 'synced' : 'failed');

        return $response;
    }

    /**
     * Post a credit note (sales return) for a previously-billed order.
     */
    public function postCreditNote(Order $order, string $reason = ''): ?CbmsApiResponse
    {
        $setting = $this->readySettingFor($order);
        if (!$setting) {
            return null;
        }

        $log = $this->startLog($order, CbmsLog::TYPE_CREDIT_NOTE);
        $creditNoteNumber = $order->id . '-CN-' . ($order->cbmsLogs()->where('type', CbmsLog::TYPE_CREDIT_NOTE)->count());

        try {
            $payload = $this->payloadBuilder->forCreditNote($order, $setting, $creditNoteNumber, $reason);
        } catch (\Throwable $e) {
            $this->finishLog($log, CbmsLog::STATUS_FAILED, null, $e->getMessage(), null);
            throw $e;
        }

        $log->update(['request_payload' => $this->redact($payload)]);

        $response = $this->client->postCreditNote($payload);

        $this->finishLog($log, $response->isSuccess() ? CbmsLog::STATUS_SUCCESS : CbmsLog::STATUS_FAILED, $response->code, $response->message, $response->raw);

        return $response;
    }

    /**
     * True if this order was successfully synced to CBMS (so a later
     * cancellation needs a credit note, not a silent skip).
     */
    public function wasSynced(Order $order): bool
    {
        return $order->cbms_status === 'synced';
    }

    private function readySettingFor(Order $order): ?CbmsSetting
    {
        $restaurant = $order->branch?->restaurant;
        if (!$restaurant) {
            return null;
        }

        $setting = $restaurant->cbmsSetting;
        if (!$setting || !$setting->is_enabled) {
            return null;
        }

        $sellerPan = $setting->seller_pan_override ?: $order->branch->vat_number;

        if (blank($setting->username) || blank($setting->password) || blank($setting->fiscal_year) || blank($sellerPan)) {
            Log::warning('CBMS sync skipped: settings incomplete', ['order_id' => $order->id, 'restaurant_id' => $restaurant->id]);
            return null;
        }

        return $setting;
    }

    private function startLog(Order $order, string $type): CbmsLog
    {
        return CbmsLog::create([
            'restaurant_id' => $order->branch?->restaurant_id,
            'branch_id' => $order->branch_id,
            'order_id' => $order->id,
            'type' => $type,
            'status' => CbmsLog::STATUS_PENDING,
            'attempts' => 1,
        ]);
    }

    private function finishLog(CbmsLog $log, string $status, ?string $code, ?string $message, mixed $raw): void
    {
        $log->update([
            'status' => $status,
            'response_code' => $code,
            'response_message' => is_string($raw) ? $message . ' | raw: ' . str($raw)->limit(500) : $message,
            'submitted_at' => now(),
        ]);
    }

    private function markOrder(Order $order, string $status): void
    {
        $order->forceFill([
            'cbms_status' => $status,
            'cbms_synced_at' => $status === 'synced' ? now() : $order->cbms_synced_at,
        ])->saveQuietly();
    }

    private function redact(array $payload): array
    {
        $payload['password'] = '***redacted***';

        return $payload;
    }
}
