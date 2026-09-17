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
 * Reports a cancelled order (that had already been successfully synced to
 * CBMS as a bill) as a credit note / sales return, per the "Credit notes or
 * Sales return should be synced in real time" requirement in the CBMS API
 * documentation. Dispatched from OrderObserver when a previously-synced
 * order's status becomes 'canceled'.
 */
class PostCreditNoteToCbmsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 120, 600];

    public function __construct(
        public readonly int $orderId,
        public readonly string $reason = '',
    ) {
    }

    public function handle(CbmsService $cbmsService): void
    {
        $order = Order::find($this->orderId);
        if (!$order) {
            return;
        }

        $response = $cbmsService->postCreditNote($order, $this->reason);

        if ($response === null || $response->isSuccess()) {
            return;
        }

        throw new \RuntimeException('CBMS credit note submission failed for order ' . $order->id . ': ' . $response->message);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CBMS credit note submission permanently failed', [
            'order_id' => $this->orderId,
            'error' => $exception->getMessage(),
        ]);
    }
}
