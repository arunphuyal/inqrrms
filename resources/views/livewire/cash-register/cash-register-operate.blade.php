<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.cashRegister.cashRegister')</h3>

    @if ($registers->count() > 1)
        <div class="mb-4 max-w-xs">
            <x-label for="selectedRegisterId" value="{{ __('modules.cashRegister.register') }}" />
            <x-select id="selectedRegisterId" class="mt-1 block w-full" wire:model.live="selectedRegisterId">
                @foreach ($registers as $register)
                    <option value="{{ $register->id }}">{{ $register->name }}</option>
                @endforeach
            </x-select>
        </div>
    @endif

    @if ($registers->isEmpty())
        <x-alert type="warning">@lang('modules.cashRegister.noActiveRegisters')</x-alert>
    @elseif (!$activeSession)
        {{-- Open register --}}
        <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
            <h4 class="mb-3 text-lg font-medium text-gray-900 dark:text-white">@lang('modules.cashRegister.openRegister')</h4>

            <form wire:submit="openRegister" class="space-y-4">
                <div class="max-w-xs">
                    <x-label for="openingAmount" value="{{ __('modules.cashRegister.openingAmount') }}" />
                    <x-input id="openingAmount" type="number" step="0.01" min="0" class="block mt-1 w-full" wire:model="openingAmount" />
                    <x-input-error for="openingAmount" class="mt-2" />
                </div>

                @if ($denominations->isNotEmpty())
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.cashRegister.countDenominations')</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($denominations as $denomination)
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">{{ currency_format($denomination->value) }}</label>
                                    <x-input type="number" min="0" class="block mt-1 w-full" wire:model.live="openingCounts.{{ $denomination->id }}" />
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            @lang('modules.cashRegister.countedTotal'): <span class="font-semibold">{{ currency_format($this->openingCountedTotal()) }}</span>
                        </p>
                    </div>
                @endif

                <x-button type="submit">@lang('modules.cashRegister.openRegister')</x-button>
            </form>
        </div>
    @else
        {{-- Active session --}}
        <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-700">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div>
                    <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('modules.cashRegister.open')</span>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        @lang('modules.cashRegister.openedBy') {{ $activeSession->openedBy?->name }} @lang('modules.cashRegister.at') {{ $activeSession->opened_at?->format('Y-m-d H:i') }}
                    </span>
                </div>
                <div class="space-x-2">
                    <x-secondary-button wire:click="$set('showTransactionForm', true)">@lang('modules.cashRegister.addTransaction')</x-secondary-button>
                    <x-button wire:click="openCloseForm">@lang('modules.cashRegister.closeRegister')</x-button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.openingAmount')</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ currency_format($activeSession->opening_amount) }}</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.cashIn')</p>
                    <p class="text-lg font-semibold text-green-700 dark:text-green-400">{{ currency_format($activeSession->transactions->where('type', 'cash_in')->sum('amount')) }}</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.cashOutAndExpense')</p>
                    <p class="text-lg font-semibold text-red-700 dark:text-red-400">{{ currency_format($activeSession->transactions->whereIn('type', ['cash_out', 'expense'])->sum('amount')) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead>
                        <tr>
                            <th class="py-2 px-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.type')</th>
                            <th class="py-2 px-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.value')</th>
                            <th class="py-2 px-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.billing.description')</th>
                            <th class="py-2 px-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.at')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($activeSession->transactions as $transaction)
                            <tr>
                                <td class="py-2 px-3 text-sm capitalize dark:text-white">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                <td class="py-2 px-3 text-sm dark:text-white">{{ currency_format($transaction->amount) }}</td>
                                <td class="py-2 px-3 text-sm dark:text-white">{{ $transaction->reason ?? '--' }}</td>
                                <td class="py-2 px-3 text-sm dark:text-white">{{ $transaction->created_at->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-2 px-3 text-sm text-gray-500" colspan="4">@lang('modules.cashRegister.noTransactionsYet')</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <x-dialog-modal wire:model.live="showTransactionForm">
        <x-slot name="title">@lang('modules.cashRegister.addTransaction')</x-slot>
        <x-slot name="content">
            <form wire:submit="addTransaction" class="space-y-4">
                <div>
                    <x-label for="transactionType" value="{{ __('modules.cashRegister.type') }}" />
                    <x-select id="transactionType" class="mt-1 block w-full" wire:model="transactionType">
                        <option value="cash_in">@lang('modules.cashRegister.cashIn')</option>
                        <option value="cash_out">@lang('modules.cashRegister.cashOut')</option>
                        <option value="expense">@lang('modules.cashRegister.expense')</option>
                    </x-select>
                </div>
                <div>
                    <x-label for="transactionAmount" value="{{ __('modules.cashRegister.value') }}" />
                    <x-input id="transactionAmount" type="number" step="0.01" min="0.01" class="block mt-1 w-full" wire:model="transactionAmount" />
                    <x-input-error for="transactionAmount" class="mt-2" />
                </div>
                <div>
                    <x-label for="transactionReason" value="{{ __('modules.billing.description') }}" />
                    <x-input id="transactionReason" type="text" class="block mt-1 w-full" wire:model="transactionReason" />
                </div>
                <div class="flex w-full pb-4 space-x-4 mt-6">
                    <x-button type="submit">@lang('app.save')</x-button>
                    <x-button-cancel wire:click="$set('showTransactionForm', false)">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showCloseForm" maxWidth="lg">
        <x-slot name="title">@lang('modules.cashRegister.closeRegister')</x-slot>
        <x-slot name="content">
            <form wire:submit="closeRegister" class="space-y-4">
                <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.expectedAmount')</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ currency_format($expectedAmount) }}</p>
                </div>

                <div class="max-w-xs">
                    <x-label for="countedAmount" value="{{ __('modules.cashRegister.countedAmount') }}" />
                    <x-input id="countedAmount" type="number" step="0.01" min="0" class="block mt-1 w-full" wire:model="countedAmount" />
                    <x-input-error for="countedAmount" class="mt-2" />
                </div>

                @if ($denominations->isNotEmpty())
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.cashRegister.countDenominations')</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($denominations as $denomination)
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">{{ currency_format($denomination->value) }}</label>
                                    <x-input type="number" min="0" class="block mt-1 w-full" wire:model.live="closingCounts.{{ $denomination->id }}" />
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            @lang('modules.cashRegister.countedTotal'): <span class="font-semibold">{{ currency_format($this->closingCountedTotal()) }}</span>
                        </p>
                    </div>
                @endif

                <div class="flex w-full pb-4 space-x-4 mt-6">
                    <x-button type="submit">@lang('modules.cashRegister.closeRegister')</x-button>
                    <x-button-cancel wire:click="$set('showCloseForm', false)">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
        </x-slot>
    </x-dialog-modal>
</div>
