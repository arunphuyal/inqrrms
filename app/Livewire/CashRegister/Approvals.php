<?php

namespace App\Livewire\CashRegister;

use App\Models\CashRegisterSession;
use App\Services\CashRegisterService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Approvals extends Component
{
    use LivewireAlert;

    public $sessions;

    public $reviewingSessionId;
    public $reviewingSession;
    public $note = '';
    public $showReviewModal = false;

    public function mount()
    {
        $this->loadSessions();
    }

    private function loadSessions(): void
    {
        $this->sessions = CashRegisterSession::where('restaurant_id', restaurant()->id)
            ->where('branch_id', branch()->id)
            ->awaitingApproval()
            ->with(['cashRegister', 'openedBy', 'closedBy'])
            ->latest('closed_at')
            ->get();
    }

    public function review($sessionId)
    {
        $this->reviewingSessionId = $sessionId;
        $this->reviewingSession = CashRegisterSession::where('restaurant_id', restaurant()->id)->findOrFail($sessionId);
        $this->note = '';
        $this->showReviewModal = true;
    }

    public function approve(CashRegisterService $cashRegisterService)
    {
        $cashRegisterService->approve($this->reviewingSession, user(), $this->note);
        $this->afterReview(__('messages.cashRegisterApproved'));
    }

    public function reject(CashRegisterService $cashRegisterService)
    {
        $this->validate(['note' => 'required|string|max:1000'], [], ['note' => __('modules.cashRegister.approvalNote')]);

        $cashRegisterService->reject($this->reviewingSession, user(), $this->note);
        $this->afterReview(__('messages.cashRegisterRejected'));
    }

    private function afterReview(string $message): void
    {
        $this->showReviewModal = false;
        $this->reviewingSession = null;
        $this->note = '';
        $this->loadSessions();

        $this->alert('success', $message, ['toast' => true, 'position' => 'top-end']);
    }

    public function render()
    {
        return view('livewire.cash-register.approvals');
    }
}
