<?php

namespace App\Livewire\CashRegister;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Livewire\Component;

class RegisterDashboard extends Component
{
    public $registers;
    public $pendingApprovalsCount;
    public $todayOpenedCount;
    public $todayClosedCount;
    public $todayNetCash;

    public function mount()
    {
        $this->registers = CashRegister::where('restaurant_id', restaurant()->id)
            ->where('branch_id', branch()->id)
            ->with(['sessions' => fn ($q) => $q->where('status', 'open')->with('openedBy')])
            ->get();

        $this->pendingApprovalsCount = CashRegisterSession::where('restaurant_id', restaurant()->id)
            ->awaitingApproval()
            ->count();

        $todaySessions = CashRegisterSession::where('restaurant_id', restaurant()->id)
            ->where('branch_id', branch()->id)
            ->whereDate('opened_at', now()->toDateString())
            ->get();

        $this->todayOpenedCount = $todaySessions->count();
        $this->todayClosedCount = $todaySessions->whereNotNull('closed_at')->count();
        $this->todayNetCash = $todaySessions->whereNotNull('counted_closing_amount')->sum(function ($session) {
            return $session->counted_closing_amount - $session->opening_amount;
        });
    }

    public function render()
    {
        return view('livewire.cash-register.register-dashboard');
    }
}
