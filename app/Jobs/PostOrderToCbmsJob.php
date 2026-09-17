<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Cbms\CbmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Reports a paid order to IRD's CBMS as an issued bill, per the "Invoice
 * issued ... should be synced in real time" requirement in the CBMS API
 * documentation. Dispatched from OrderObserver when an order's status
 * becomes 'paid'.
 */
class PostOrderToCbmsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 120, 600];

    public function __construct(public readonly int $orderId)
    {
    }

    public function handle(CbmsService $cbmsService): void
    {
        $order = Order::find($this->orderId);
        if (!$order) {
            return;
        }

        $response = $cbmsService->postBill($order);

        if ($response === null || $response->isSuccess() || $response->code === '101') {
            return;
        }

        // Transient/unknown failures are retried by the queue; the CbmsLog
        // row from this attempt is already recorded either way.
        throw new \RuntimeException('CBMS bill submission failed for order ' . $order->id . ': ' . $response->message);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CBMS bill submission permanently failed', [
            'order_id' => $this->orderId,
            'error' => $exception->getMessage(),
        ]);
    }
}
