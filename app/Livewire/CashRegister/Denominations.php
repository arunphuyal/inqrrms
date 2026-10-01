<?php

namespace App\Livewire\CashRegister;

use App\Models\CashRegisterDenomination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Denominations extends Component
{
    use LivewireAlert;

    public $denominations;

    public $denominationId;
    public $value;
    public $type = 'note';
    public $isActive = true;
    public $showForm = false;
    public $confirmDeleteModal = false;
    public $deleteId;

    public function mount()
    {
        $this->loadDenominations();
    }

    private function loadDenominations(): void
    {
        $this->denominations = CashRegisterDenomination::where('restaurant_id', restaurant()->id)
            ->orderBy('type')
            ->orderByDesc('value')
            ->get();
    }

    protected function rules(): array
    {
        return [
            'value' => 'required|numeric|min:0.01',
            'type' => 'required|in:note,coin',
            'isActive' => 'boolean',
        ];
    }

    public function addDenomination()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editDenomination($id)
    {
        $denomination = CashRegisterDenomination::where('restaurant_id', restaurant()->id)->findOrFail($id);
        $this->denominationId = $denomination->id;
        $this->value = $denomination->value;
        $this->type = $denomination->type;
        $this->isActive = (bool) $denomination->is_active;
        $this->showForm = true;
    }

    public function submitForm()
    {
        $this->validate($this->rules());

        CashRegisterDenomination::updateOrCreate(
            ['id' => $this->denominationId],
            [
                'restaurant_id' => restaurant()->id,
                'value' => $this->value,
                'type' => $this->type,
                'is_active' => $this->isActive,
            ]
        );

        $this->loadDenominations();
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
        CashRegisterDenomination::where('restaurant_id', restaurant()->id)->where('id', $this->deleteId)->delete();
        $this->loadDenominations();
        $this->confirmDeleteModal = false;
        $this->deleteId = null;

        $this->alert('success', __('app.deleted'), ['toast' => true, 'position' => 'top-end']);
    }

    private function resetForm(): void
    {
        $this->denominationId = null;
        $this->value = '';
        $this->type = 'note';
        $this->isActive = true;
        $this->showForm = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.cash-register.denominations');
    }
}
