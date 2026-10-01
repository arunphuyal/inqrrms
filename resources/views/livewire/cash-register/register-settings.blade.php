<div>
    <div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.cashRegister.registers')</h3>
        <x-help-text class="mb-6">@lang('modules.cashRegister.registersDescription')</x-help-text>

        <x-button wire:click="addRegister" wire:loading.attr="disabled">
            <svg class="w-4 h-4 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            @lang('modules.cashRegister.addRegister')
        </x-button>

        <div class="py-4 overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.registerName')</th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.branch')</th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('app.status')</th>
                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($registers as $register)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="register-{{ $register->id }}">
                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $register->name }}</td>
                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $register->branch?->name }}</td>
                                    <td class="py-2.5 px-4 text-sm whitespace-nowrap">
                                        @if ($register->is_active)
                                            <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.active')</span>
                                        @else
                                            <span class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.inactive')</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                        <x-secondary-button-table wire:click="editRegister({{ $register->id }})">
                                            @lang('app.update')
                                        </x-secondary-button-table>
                                        <x-danger-button-table wire:click="confirmDelete({{ $register->id }})">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </x-danger-button-table>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-2.5 px-4" colspan="4">@lang('modules.cashRegister.noRegisters')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.cashRegister.approvalSettings')</h3>
        <x-help-text class="mb-6">@lang('modules.cashRegister.approvalSettingsDescription')</x-help-text>

        <form wire:submit="saveApprovalSettings" class="space-y-4">
            <div class="flex items-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                <label for="requireDenominationCount" class="flex items-center space-x-2">
                    <x-checkbox name="requireDenominationCount" id="requireDenominationCount" wire:model="requireDenominationCount" />
                    <span>
                        <span class="font-medium text-gray-900 dark:text-white">@lang('modules.cashRegister.requireDenominationCount')</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.requireDenominationCountHelp')</p>
                    </span>
                </label>
            </div>

            <div class="flex items-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                <label for="requireApprovalOnDiscrepancy" class="flex items-center space-x-2">
                    <x-checkbox name="requireApprovalOnDiscrepancy" id="requireApprovalOnDiscrepancy" wire:model.live="requireApprovalOnDiscrepancy" />
                    <span>
                        <span class="font-medium text-gray-900 dark:text-white">@lang('modules.cashRegister.requireApprovalOnDiscrepancy')</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.requireApprovalOnDiscrepancyHelp')</p>
                    </span>
                </label>
            </div>

            @if ($requireApprovalOnDiscrepancy)
                <div>
                    <x-label for="discrepancyThreshold" value="{{ __('modules.cashRegister.discrepancyThreshold') }}" />
                    <x-input id="discrepancyThreshold" type="number" step="0.01" min="0" class="block mt-1 w-full sm:w-64" wire:model="discrepancyThreshold" />
                    <x-input-error for="discrepancyThreshold" class="mt-2" />
                </div>
            @endif

            <div class="flex items-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                <label for="alwaysRequireApproval" class="flex items-center space-x-2">
                    <x-checkbox name="alwaysRequireApproval" id="alwaysRequireApproval" wire:model="alwaysRequireApproval" />
                    <span>
                        <span class="font-medium text-gray-900 dark:text-white">@lang('modules.cashRegister.alwaysRequireApproval')</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.alwaysRequireApprovalHelp')</p>
                    </span>
                </label>
            </div>

            <x-button type="submit" wire:loading.attr="disabled">@lang('app.save')</x-button>
        </form>
    </div>

    <x-dialog-modal wire:model.live="showRegisterForm">
        <x-slot name="title">
            {{ $registerId ? __('app.update') : __('app.add') }} @lang('modules.cashRegister.register')
        </x-slot>
        <x-slot name="content">
            @if ($showRegisterForm)
                <form wire:submit="submitRegister" class="space-y-4">
                    <div>
                        <x-label for="name" value="{{ __('modules.cashRegister.registerName') }}" />
                        <x-input id="name" type="text" class="block mt-1 w-full" wire:model="name" autofocus />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="branchId" value="{{ __('modules.cashRegister.branch') }}" />
                        <x-select id="branchId" class="mt-1 block w-full" wire:model="branchId">
                            <option value="">{{ __('app.select') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="branchId" class="mt-2" />
                    </div>

                    @if ($registerId)
                        <div class="flex items-center">
                            <x-checkbox name="isActive" id="isActive" wire:model="isActive" class="mr-2" />
                            <x-label for="isActive" value="{{ __('app.active') }}" />
                        </div>
                    @endif

                    <div class="flex w-full pb-4 space-x-4 mt-6">
                        <x-button type="submit">{{ $registerId ? __('app.update') : __('app.add') }}</x-button>
                        <x-button-cancel wire:click="$set('showRegisterForm', false)">@lang('app.cancel')</x-button-cancel>
                    </div>
                </form>
            @endif
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model.defer="confirmDeleteModal">
        <x-slot name="title">@lang('modules.cashRegister.deleteRegister')</x-slot>
        <x-slot name="content">@lang('modules.cashRegister.deleteRegisterConfirm')</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteModal')">@lang('app.cancel')</x-secondary-button>
            <x-danger-button class="ml-3" wire:click="delete">@lang('app.delete')</x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
