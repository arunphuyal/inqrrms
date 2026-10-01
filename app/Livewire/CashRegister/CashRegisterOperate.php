<?php

namespace App\Livewire\CashRegister;

use App\Models\CashRegister;
use App\Models\CashRegisterDenomination;
use App\Models\CashRegisterSession;
use App\Services\CashRegisterService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CashRegisterOperate extends Component
{
    use LivewireAlert;

    public $registers;
    public $denominations;
    public $selectedRegisterId;
    public $activeSession;

    public $openingAmount = 0;
    public $openingCounts = [];

    public $showCloseForm = false;
    public $countedAmount = 0;
    public $closingCounts = [];
    public $expectedAmount = 0;

    public $showTransactionForm = false;
    public $transactionType = 'cash_in';
    public $transactionAmount = 0;
    public $transactionReason = '';

    protected $settings;

    public function mount(CashRegisterService $cashRegisterService)
    {
        $this->registers = CashRegister::where('restaurant_id', restaurant()->id)
            ->where('branch_id', branch()->id)
            ->active()
            ->get();

        $this->denominations = CashRegisterDenomination::where('restaurant_id', restaurant()->id)->active()->orderByDesc('value')->get();

        $this->settings = $cashRegisterService->settingsFor(restaurant()->id);

        $this->selectedRegisterId = $this->registers->first()?->id;
        $this->loadActiveSession();
    }

    public function updatedSelectedRegisterId()
    {
        $this->loadActiveSession();
    }

    private function loadActiveSession(): void
    {
        $this->activeSession = $this->selectedRegisterId
            ? CashRegisterSession::where('cash_register_id', $this->selectedRegisterId)->where('status', 'open')->with(['openedBy', 'transactions'])->first()
            : null;
    }

    public function openRegister(CashRegisterService $cashRegisterService)
    {
        $rules = ['openingAmount' => 'required|numeric|min:0', 'selectedRegisterId' => 'required|exists:cash_registers,id'];
        $this->validate($rules);

        $register = CashRegister::where('restaurant_id', restaurant()->id)->findOrFail($this->selectedRegisterId);

        if ($register->openSession()) {
            $this->alert('error', __('messages.cashRegisterAlreadyOpen'), ['toast' => true, 'position' => 'top-end']);
            return;
        }

        $counts = $this->settings->require_denomination_count ? $this->formattedCounts($this->openingCounts) : [];

        $cashRegisterService->open($register, user(), (float) $this->openingAmount, $counts);

        $this->openingAmount = 0;
        $this->openingCounts = [];
        $this->loadActiveSession();

        $this->alert('success', __('messages.cashRegisterOpened'), ['toast' => true, 'position' => 'top-end']);
    }

    public function addTransaction(CashRegisterService $cashRegisterService)
    {
        $this->validate([
            'transactionType' => 'required|in:cash_in,cash_out,expense',
            'transactionAmount' => 'required|numeric|min:0.01',
            'transactionReason' => 'nullable|string|max:255',
        ]);

        $cashRegisterService->addTransaction($this->activeSession, $this->transactionType, (float) $this->transactionAmount, $this->transactionReason, user());

        $this->transactionAmount = 0;
        $this->transactionReason = '';
        $this->showTransactionForm = false;
        $this->loadActiveSession();

        $this->alert('success', __('app.saved'), ['toast' => true, 'position' => 'top-end']);
    }

    public function openCloseForm(CashRegisterService $cashRegisterService)
    {
        $this->expectedAmount = $cashRegisterService->computeExpectedClosingAmount($this->activeSession);
        $this->countedAmount = $this->expectedAmount;
        $this->closingCounts = [];
        $this->showCloseForm = true;
    }

    public function closeRegister(CashRegisterService $cashRegisterService)
    {
        $this->validate(['countedAmount' => 'required|numeric|min:0']);

        $counts = $this->settings->require_denomination_count ? $this->formattedCounts($this->closingCounts) : [];

        $session = $cashRegisterService->close($this->activeSession, user(), (float) $this->countedAmount, $counts);

        $this->showCloseForm = false;
        $this->loadActiveSession();

        $message = $session->status === 'pending_approval'
            ? __('messages.cashRegisterClosedPendingApproval')
            : __('messages.cashRegisterClosed');

        $this->alert($session->status === 'pending_approval' ? 'warning' : 'success', $message, ['toast' => true, 'position' => 'top-end']);
    }

    /**
     * @param array<int,int> $rawCounts denomination_id => quantity
     * @return array<int,array{denomination_id:int,quantity:int,subtotal:float}>
     */
    private function formattedCounts(array $rawCounts): array
    {
        $formatted = [];

        foreach ($this->denominations as $denomination) {
            $qty = (int) ($rawCounts[$denomination->id] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $formatted[] = [
                'denomination_id' => $denomination->id,
                'quantity' => $qty,
                'subtotal' => round($qty * (float) $denomination->value, 2),
            ];
        }

        return $formatted;
    }

    public function closingCountedTotal(): float
    {
        return round(collect($this->formattedCounts($this->closingCounts))->sum('subtotal'), 2);
    }

    public function openingCountedTotal(): float
    {
        return round(collect($this->formattedCounts($this->openingCounts))->sum('subtotal'), 2);
    }

    public function render()
    {
        return view('livewire.cash-register.cash-register-operate');
    }
}
