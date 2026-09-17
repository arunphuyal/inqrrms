<?php

namespace App\Livewire\Settings;

use App\Models\CbmsLog;
use App\Models\CbmsSetting;
use App\Models\Tax;
use App\Jobs\PostOrderToCbmsJob;
use App\Jobs\PostCreditNoteToCbmsJob;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TaxSettings extends Component
{

    use LivewireAlert, WithPagination;

    protected $listeners = ['refreshTaxes' => 'mount'];

    public $taxes;
    public $tax;
    public $showEditCurrencyModal = false;
    public $showAddCurrencyModal = false;
    public $confirmDeleteCurrencyModal = false;
    public $settings;
    public $taxMode = 'order';
    public $itemTaxInclusive = 0;
    public $includeChargesInTaxBase = true;
    public $activeTab = 'settings';
    public $assignAllTaxesToItems = false;

    public $cbmsEnabled = false;
    public $cbmsMode = 'test';
    public $cbmsUsername;
    public $cbmsPassword;
    public $cbmsSellerPanOverride;
    public $cbmsFiscalYear;

    protected function cbmsRules(): array
    {
        return [
            'cbmsEnabled' => 'boolean',
            'cbmsMode' => 'required|in:test,live',
            'cbmsUsername' => 'nullable|string|max:255',
            'cbmsPassword' => 'nullable|string|max:255',
            'cbmsSellerPanOverride' => 'nullable|string|max:20',
            'cbmsFiscalYear' => 'nullable|string|max:20',
        ];
    }

    public function mount()
    {
        $this->taxes = Tax::get();

        if ($this->settings) {
            $this->taxMode = $this->settings->tax_mode ?? 'order';
            $this->itemTaxInclusive = $this->settings->tax_inclusive ?? 0;
            $this->includeChargesInTaxBase = $this->settings->include_charges_in_tax_base ?? true;
        }

        $this->loadCbmsSettings();
    }

    private function loadCbmsSettings(): void
    {
        if (!restaurant()) {
            return;
        }

        $cbmsSetting = CbmsSetting::firstOrNew(['restaurant_id' => restaurant()->id]);

        $this->cbmsEnabled = (bool) $cbmsSetting->is_enabled;
        $this->cbmsMode = $cbmsSetting->mode ?? 'test';
        $this->cbmsUsername = $cbmsSetting->username;
        $this->cbmsPassword = $cbmsSetting->password;
        $this->cbmsSellerPanOverride = $cbmsSetting->seller_pan_override;
        $this->cbmsFiscalYear = $cbmsSetting->fiscal_year;
    }

    public function saveCbmsSettings()
    {
        $this->validate($this->cbmsRules());

        if (!restaurant()) {
            return;
        }

        CbmsSetting::updateOrCreate(
            ['restaurant_id' => restaurant()->id],
            [
                'is_enabled' => $this->cbmsEnabled,
                'mode' => $this->cbmsMode,
                'username' => $this->cbmsUsername,
                'password' => $this->cbmsPassword,
                'seller_pan_override' => $this->cbmsSellerPanOverride,
                'fiscal_year' => $this->cbmsFiscalYear,
            ]
        );

        $this->alert('success', __('app.saved'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
        ]);
    }

    public function retryCbmsSubmission($logId)
    {
        if (!restaurant()) {
            return;
        }

        $log = CbmsLog::where('restaurant_id', restaurant()->id)->findOrFail($logId);

        if ($log->type === CbmsLog::TYPE_BILL) {
            PostOrderToCbmsJob::dispatch($log->order_id);
        } else {
            PostCreditNoteToCbmsJob::dispatch($log->order_id, (string) ($log->order?->cancel_reason_text ?? ''));
        }

        $this->alert('success', __('messages.cbmsRetryQueued'), [
            'toast' => true,
            'position' => 'top-end',
        ]);
    }


    public function showAddCurrency()
    {
        $this->showAddCurrencyModal = true;
    }

    public function showEditCurrency($id)
    {
        $this->tax = Tax::findOrFail($id);
        $this->showEditCurrencyModal = true;
    }

    public function showDeleteCurrency($id)
    {
        $this->tax = Tax::findOrFail($id);
        $this->confirmDeleteCurrencyModal = true;
    }

    public function deleteCurrency($id)
    {
        Tax::destroy($id);
        $this->tax = null;

        $this->confirmDeleteCurrencyModal = false;

        $this->dispatch('refreshTaxes');

        $this->alert('success', __('messages.taxDeleted'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    #[On('hideEditCurrency')]
    public function hideEditCurrency()
    {
        $this->showEditCurrencyModal = false;
        $this->dispatch('refreshTaxes');
    }

    #[On('hideAddCurrency')]
    public function hideAddCurrency()
    {
        $this->showAddCurrencyModal = false;
        $this->dispatch('refreshTaxes');
    }

    public function saveTaxSettings()
    {
        if ($this->settings) {
            if ($this->taxMode !== 'item') {
                $this->assignAllTaxesToItems = false;
            }

            $this->settings->tax_mode = $this->taxMode;
            $this->settings->tax_inclusive = $this->itemTaxInclusive;
            $this->settings->include_charges_in_tax_base = $this->includeChargesInTaxBase;
            $this->settings->save();

            if ($this->taxMode === 'item' && $this->assignAllTaxesToItems) {
                $allTaxes = Tax::all();
                $items = \App\Models\MenuItem::doesntHave('taxes')->get();
                foreach ($items as $item) {
                    $item->taxes()->sync($allTaxes->pluck('id')->toArray());
                }
            }

            $this->alert('success', __('app.saved'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false
            ]);
        }

        session()->forget('restaurant');
    }

    public function render()
    {
        $cbmsLogs = restaurant()
            ? CbmsLog::where('restaurant_id', restaurant()->id)->latest()->paginate(10, ['*'], 'cbmsPage')
            : collect();

        return view('livewire.settings.tax-settings', compact('cbmsLogs'));
    }
}
