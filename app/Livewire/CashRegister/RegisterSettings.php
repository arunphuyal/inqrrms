<?php

namespace App\Livewire\CashRegister;

use App\Models\Branch;
use App\Models\CashRegister;
use App\Services\CashRegisterService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RegisterSettings extends Component
{
    use LivewireAlert;

    public $registers;
    public $branches;

    public $registerId;
    public $name;
    public $branchId;
    public $isActive = true;
    public $showRegisterForm = false;
    public $confirmDeleteModal = false;
    public $deleteId;

    public $requireDenominationCount = true;
    public $requireApprovalOnDiscrepancy = true;
    public $discrepancyThreshold = 0;
    public $alwaysRequireApproval = false;

    public function mount(CashRegisterService $cashRegisterService)
    {
        $this->branches = Branch::where('restaurant_id', restaurant()->id)->get();
        $this->loadRegisters();

        $settings = $cashRegisterService->settingsFor(restaurant()->id);
        $this->requireDenominationCount = (bool) $settings->require_denomination_count;
        $this->requireApprovalOnDiscrepancy = (bool) $settings->require_approval_on_discrepancy;
        $this->discrepancyThreshold = $settings->discrepancy_threshold;
        $this->alwaysRequireApproval = (bool) $settings->always_require_approval;
    }

    private function loadRegisters(): void
    {
        $this->registers = CashRegister::where('restaurant_id', restaurant()->id)->with('branch')->orderBy('name')->get();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'branchId' => 'required|exists:branches,id',
            'isActive' => 'boolean',
        ];
    }

    public function addRegister()
    {
        $this->resetForm();
        $this->showRegisterForm = true;
    }

    public function editRegister($id)
    {
        $register = CashRegister::where('restaurant_id', restaurant()->id)->findOrFail($id);
        $this->registerId = $register->id;
        $this->name = $register->name;
        $this->branchId = $register->branch_id;
        $this->isActive = (bool) $register->is_active;
        $this->showRegisterForm = true;
    }

    public function submitRegister()
    {
        $this->validate($this->rules());

        CashRegister::updateOrCreate(
            ['id' => $this->registerId],
            [
                'restaurant_id' => restaurant()->id,
                'branch_id' => $this->branchId,
                'name' => $this->name,
                'is_active' => $this->isActive,
            ]
        );

        $this->loadRegisters();
        $this->resetForm();

        $this->alert('success', __('app.saved'), ['toast' => true, 'position' => 'top-end']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmDeleteModal = true;
    }

    public function delete()
    {
        CashRegister::where('restaurant_id', restaurant()->id)->where('id', $this->deleteId)->delete();
        $this->loadRegisters();
        $this->confirmDeleteModal = false;
        $this->deleteId = null;

        $this->alert('success', __('app.deleted'), ['toast' => true, 'position' => 'top-end']);
    }

    private function resetForm(): void
    {
        $this->registerId = null;
        $this->name = '';
        $this->branchId = null;
        $this->isActive = true;
        $this->showRegisterForm = false;
        $this->resetErrorBag();
    }

    public function saveApprovalSettings(CashRegisterService $cashRegisterService)
    {
        $this->validate([
            'requireDenominationCount' => 'boolean',
            'requireApprovalOnDiscrepancy' => 'boolean',
            'discrepancyThreshold' => 'nullable|numeric|min:0',
            'alwaysRequireApproval' => 'boolean',
        ]);

        $settings = $cashRegisterService->settingsFor(restaurant()->id);
        $settings->update([
            'require_denomination_count' => $this->requireDenominationCount,
            'require_approval_on_discrepancy' => $this->requireApprovalOnDiscrepancy,
            'discrepancy_threshold' => $this->discrepancyThreshold ?: 0,
            'always_require_approval' => $this->alwaysRequireApproval,
        ]);

        $this->alert('success', __('app.saved'), ['toast' => true, 'position' => 'top-end']);
    }

    public function render()
    {
        return view('livewire.cash-register.register-settings');
    }
}
