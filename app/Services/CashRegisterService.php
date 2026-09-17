<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\CashRegisterCount;
use App\Models\CashRegisterSession;
use App\Models\CashRegisterSetting;
use App\Models\CashRegisterTransaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Encapsulates the cash-drawer open/count/close/approve workflow so the
 * Livewire screens stay thin.
 */
class CashRegisterService
{
    public function settingsFor(int $restaurantId): CashRegisterSetting
    {
        return CashRegisterSetting::firstOrCreate(
            ['restaurant_id' => $restaurantId],
            []
        );
    }

    public function open(CashRegister $register, User $user, float $openingAmount, array $openingCounts = []): CashRegisterSession
    {
        return DB::transaction(function () use ($register, $user, $openingAmount, $openingCounts) {
            $session = CashRegisterSession::create([
                'restaurant_id' => $register->restaurant_id,
                'branch_id' => $register->branch_id,
                'cash_register_id' => $register->id,
                'opened_by' => $user->id,
                'opened_at' => now(),
                'opening_amount' => $openingAmount,
                'status' => 'open',
            ]);

            $this->storeCounts($session, CashRegisterCount::STAGE_OPENING, $openingCounts);

            return $session;
        });
    }

    public function addTransaction(CashRegisterSession $session, string $type, float $amount, ?string $reason, User $user): CashRegisterTransaction
    {
        return CashRegisterTransaction::create([
            'restaurant_id' => $session->restaurant_id,
            'cash_register_session_id' => $session->id,
            'type' => $type,
            'amount' => $amount,
            'reason' => $reason,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Opening float + cash sales taken during the session window + manual
     * cash-in - manual cash-out - till-paid expenses.
     */
    public function computeExpectedClosingAmount(CashRegisterSession $session, ?\DateTimeInterface $asOf = null): float
    {
        $asOf = $asOf ?? now();

        $cashSales = (float) Payment::whereHas('order', function ($query) use ($session) {
            $query->where('branch_id', $session->branch_id);
        })
            ->where('payment_method', 'cash')
            ->whereBetween('created_at', [$session->opened_at, $asOf])
            ->sum('amount');

        $cashIn = (float) $session->transactions()->where('type', CashRegisterTransaction::TYPE_CASH_IN)->sum('amount');
        $cashOut = (float) $session->transactions()->where('type', CashRegisterTransaction::TYPE_CASH_OUT)->sum('amount');
        $expenses = (float) $session->transactions()->where('type', CashRegisterTransaction::TYPE_EXPENSE)->sum('amount');

        return round((float) $session->opening_amount + $cashSales + $cashIn - $cashOut - $expenses, 2);
    }

    public function close(CashRegisterSession $session, User $user, float $countedAmount, array $closingCounts = []): CashRegisterSession
    {
        return DB::transaction(function () use ($session, $user, $countedAmount, $closingCounts) {
            $settings = $this->settingsFor($session->restaurant_id);

            $expected = $this->computeExpectedClosingAmount($session, now());
            $difference = round($countedAmount - $expected, 2);

            $needsApproval = $settings->always_require_approval
                || ($settings->require_approval_on_discrepancy && abs($difference) > (float) $settings->discrepancy_threshold);

            $session->update([
                'closed_by' => $user->id,
                'closed_at' => now(),
                'expected_closing_amount' => $expected,
                'counted_closing_amount' => $countedAmount,
                'difference' => $difference,
                'status' => $needsApproval ? 'pending_approval' : 'closed',
            ]);

            $this->storeCounts($session, CashRegisterCount::STAGE_CLOSING, $closingCounts);

            return $session->fresh();
        });
    }

    public function approve(CashRegisterSession $session, User $approver, ?string $note = null): CashRegisterSession
    {
        $session->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_note' => $note,
        ]);

        return $session->fresh();
    }

    public function reject(CashRegisterSession $session, User $approver, ?string $note = null): CashRegisterSession
    {
        $session->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_note' => $note,
        ]);

        return $session->fresh();
    }

    /**
     * @param array<int,array{denomination_id:int,quantity:int,subtotal:float}> $counts
     */
    private function storeCounts(CashRegisterSession $session, string $stage, array $counts): void
    {
        foreach ($counts as $count) {
            if ((int) ($count['quantity'] ?? 0) <= 0) {
                continue;
            }

            CashRegisterCount::updateOrCreate(
                [
                    'cash_register_session_id' => $session->id,
                    'cash_register_denomination_id' => $count['denomination_id'],
                    'stage' => $stage,
                ],
                [
                    'quantity' => $count['quantity'],
                    'subtotal' => $count['subtotal'],
                ]
            );
        }
    }
}
