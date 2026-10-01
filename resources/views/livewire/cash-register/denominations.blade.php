<div>
    <div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.cashRegister.denominations')</h3>
        <x-help-text class="mb-6">@lang('modules.cashRegister.denominationsDescription')</x-help-text>

        <x-button wire:click="addDenomination">
            <svg class="w-4 h-4 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            @lang('modules.cashRegister.addDenomination')
        </x-button>

        <div class="py-4 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.value')</th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.type')</th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('app.status')</th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($denominations as $denomination)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="denom-{{ $denomination->id }}">
                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($denomination->value) }}</td>
                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white capitalize">{{ $denomination->type }}</td>
                                    <td class="py-2.5 px-4 text-sm whitespace-nowrap">
                                        @if ($denomination->is_active)
                                            <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.active')</span>
                                        @else
                                            <span class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.inactive')</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                        <x-secondary-button-table wire:click="editDenomination({{ $denomination->id }})">
                                            @lang('app.update')
                                        </x-secondary-button-table>
                                        <x-danger-button-table wire:click="confirmDelete({{ $denomination->id }})">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </x-danger-button-table>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-2.5 px-4" colspan="4">@lang('modules.cashRegister.noDenominations')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-dialog-modal wire:model.live="showForm">
        <x-slot name="title">
            {{ $denominationId ? __('app.update') : __('app.add') }} @lang('modules.cashRegister.denomination')
        </x-slot>
        <x-slot name="content">
            @if ($showForm)
                <form wire:submit="submitForm" class="space-y-4">
                    <div>
                        <x-label for="value" value="{{ __('modules.cashRegister.value') }}" />
                        <x-input id="value" type="number" step="0.01" min="0.01" class="block mt-1 w-full" wire:model="value" autofocus />
                        <x-input-error for="value" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="type" value="{{ __('modules.cashRegister.type') }}" />
                        <x-select id="type" class="mt-1 block w-full" wire:model="type">
                            <option value="note">@lang('modules.cashRegister.note')</option>
                            <option value="coin">@lang('modules.cashRegister.coin')</option>
                        </x-select>
                        <x-input-error for="type" class="mt-2" />
                    </div>

                    @if ($denominationId)
                        <div class="flex items-center">
                            <x-checkbox name="isActive" id="isActive" wire:model="isActive" class="mr-2" />
                            <x-label for="isActive" value="{{ __('app.active') }}" />
                        </div>
                    @endif

                    <div class="flex w-full pb-4 space-x-4 mt-6">
                        <x-button type="submit">{{ $denominationId ? __('app.update') : __('app.add') }}</x-button>
                        <x-button-cancel wire:click="$set('showForm', false)">@lang('app.cancel')</x-button-cancel>
                    </div>
                </form>
            @endif
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model.defer="confirmDeleteModal">
        <x-slot name="title">@lang('modules.cashRegister.deleteDenomination')</x-slot>
        <x-slot name="content">@lang('modules.cashRegister.deleteDenominationConfirm')</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteModal')">@lang('app.cancel')</x-secondary-button>
            <x-danger-button class="ml-3" wire:click="delete">@lang('app.delete')</x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
