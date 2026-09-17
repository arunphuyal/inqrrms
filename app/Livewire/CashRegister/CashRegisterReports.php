<?php

namespace App\Livewire\CashRegister;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Livewire\Component;
use Livewire\WithPagination;

class CashRegisterReports extends Component
{
    use WithPagination;

    public $registers;
    public $registerId = '';
    public $status = '';
    public $fromDate;
    public $toDate;

    public function mount()
    {
        $this->registers = CashRegister::where('restaurant_id', restaurant()->id)->where('branch_id', branch()->id)->get();
        $this->fromDate = now()->subDays(30)->toDateString();
        $this->toDate = now()->toDateString();
    }

    public function updating($property)
    {
        if (in_array($property, ['registerId', 'status', 'fromDate', 'toDate'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $sessions = CashRegisterSession::where('restaurant_id', restaurant()->id)
            ->where('branch_id', branch()->id)
            ->when($this->registerId, fn ($q) => $q->where('cash_register_id', $this->registerId))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->fromDate, fn ($q) => $q->whereDate('opened_at', '>=', $this->fromDate))
            ->when($this->toDate, fn ($q) => $q->whereDate('opened_at', '<=', $this->toDate))
            ->with(['cashRegister', 'openedBy', 'closedBy'])
            ->latest('opened_at')
            ->paginate(15);

        return view('livewire.cash-register.cash-register-reports', compact('sessions'));
    }
}
