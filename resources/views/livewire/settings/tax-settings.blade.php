<div>

<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.taxSettings')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.taxSettingsDescription')</x-help-text>

        {{-- Tax Settings Tabs --}}
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap items-center -mb-px">
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'settings')" @class([
                        'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeTab != 'settings',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'settings',
                    ])>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/></svg>
                        @lang('modules.settings.taxSetting')
                    </span>
                </li>

                <li class="me-2">
                    <span wire:click="$set('activeTab', 'taxes')" @class([
                        'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeTab != 'taxes',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'taxes',
                    ])>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @lang('modules.settings.taxTable')
                    </span>
                </li>

                <li class="me-2">
                    <span wire:click="$set('activeTab', 'cbms')" @class([
                        'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeTab != 'cbms',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'cbms',
                    ])>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @lang('modules.settings.cbmsTab')
                    </span>
                </li>
            </ul>
        </div>

        @if($activeTab === 'settings')
            <div class="mt-6 space-y-6">
                <form wire:submit="saveTaxSettings">
                    {{-- Tax Mode Setting --}}
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="p-4 space-y-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">@lang('modules.settings.taxMode')</h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach([
                                    ['value' => 'order', 'label' => 'modules.settings.taxModeOrder', 'help' => 'modules.settings.taxModeOrderHelp'],
                                    ['value' => 'item', 'label' => 'modules.settings.taxModeItem', 'help' => 'modules.settings.taxModeItemHelp']
                                ] as $option)
                                    <label @class([
                                        'relative flex flex-col p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md',
                                        'border-skin-base bg-skin-base/10 dark:bg-skin-base/10' => $taxMode === $option['value'],
                                        'border-gray-200 dark:border-gray-700' => $taxMode !== $option['value']
                                    ])>
                                        <div class="flex items-center justify-between mb-2">
                                            <span @class([
                                                'font-medium',
                                                'text-skin-base' => $taxMode === $option['value'],
                                                'text-gray-900 dark:text-white' => $taxMode !== $option['value']
                                            ])>
                                                @lang($option['label'])
                                            </span>
                                            <input type="radio" wire:model.live="taxMode" value="{{ $option['value'] }}" class="w-4 h-4 text-skin-base">
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang($option['help'])</p>
                                    </label>
                                @endforeach
                            </div>

                            @if($taxMode === 'item')
                                <div class="mt-6">
                                    <h4 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">@lang('modules.settings.defaultItemTaxType')</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach([
                                            ['value' => '0', 'label' => 'modules.settings.taxExclusive'],
                                            ['value' => '1', 'label' => 'modules.settings.taxInclusive']
                                        ] as $option)
                                            <label @class([
                                                'relative flex items-center p-2 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md',
                                                'border-skin-base bg-skin-base/10 dark:bg-skin-base/10' => $itemTaxInclusive == $option['value'],
                                                'border-gray-200 dark:border-gray-700' => $itemTaxInclusive != $option['value']
                                            ])>
                                                <input type="radio" wire:model.live="itemTaxInclusive" value="{{ $option['value'] }}" class="w-4 h-4 text-skin-base mr-3">
                                                <span @class([
                                                    'font-medium',
                                                    'text-skin-base' => $itemTaxInclusive == $option['value'],
                                                    'text-gray-900 dark:text-white' => $itemTaxInclusive != $option['value']
                                                ])>
                                                    @lang($option['label'])
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm">
                                    <x-checkbox name="assignAllTaxesToItems" id="assignAllTaxesToItems"
                                        wire:model='assignAllTaxesToItems' class="mr-4" />
                                    <div>
                                        <x-label for="assignAllTaxesToItems" :value="__('modules.settings.assignAllTaxesToItems')" class="!mb-1" />
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @lang('modules.settings.assignAllTaxesToItemsDescription')
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Tax Calculation Base Setting --}}
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">@lang('modules.settings.taxCalculationBase')</h4>
                                <x-help-text class="mb-4">@lang('modules.settings.taxCalculationBaseDescription')</x-help-text>

                                <div class="grid grid-cols-1 gap-4">
                                    <label @class([
                                        'relative flex flex-col p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md',
                                        'border-skin-base bg-skin-base/10 dark:bg-skin-base/10' => $includeChargesInTaxBase,
                                        'border-gray-200 dark:border-gray-700' => !$includeChargesInTaxBase
                                    ])>
                                        <div class="flex items-center justify-between mb-2">
                                            <span @class([
                                                'font-medium',
                                                'text-skin-base' => $includeChargesInTaxBase,
                                                'text-gray-900 dark:text-white' => !$includeChargesInTaxBase
                                            ])>
                                                @lang('modules.settings.includeChargesInTaxBaseYes')
                                            </span>
                                            <input type="radio" wire:model.live="includeChargesInTaxBase" value="1" class="w-4 h-4 text-skin-base">
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @lang('modules.settings.includeChargesInTaxBaseYesHelp')
                                        </p>
                                        <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-900 rounded text-xs font-mono text-gray-600 dark:text-gray-400">
                                            @lang('modules.settings.taxBaseFormulaWithCharges')
                                        </div>
                                    </label>

                                    <label @class([
                                        'relative flex flex-col p-4 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md',
                                        'border-skin-base bg-skin-base/10 dark:bg-skin-base/10' => !$includeChargesInTaxBase,
                                        'border-gray-200 dark:border-gray-700' => $includeChargesInTaxBase
                                    ])>
                                        <div class="flex items-center justify-between mb-2">
                                            <span @class([
                                                'font-medium',
                                                'text-skin-base' => !$includeChargesInTaxBase,
                                                'text-gray-900 dark:text-white' => $includeChargesInTaxBase
                                            ])>
                                                @lang('modules.settings.includeChargesInTaxBaseNo')
                                            </span>
                                            <input type="radio" wire:model.live="includeChargesInTaxBase" value="0" class="w-4 h-4 text-skin-base">
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @lang('modules.settings.includeChargesInTaxBaseNoHelp')
                                        </p>
                                        <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-900 rounded text-xs font-mono text-gray-600 dark:text-gray-400">
                                            @lang('modules.settings.taxBaseFormulaWithoutCharges')
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-button wire:loading.attr="disabled">
                            <svg wire:loading class="w-4 h-4 mr-1 text-gray-200 animate-spin fill-skin-base" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.59c0 27.615-22.386 50.001-50 50.001s-50-22.386-50-50 22.386-50 50-50 50 22.386 50 50m-90.919 0c0 22.6 18.32 40.92 40.919 40.92s40.919-18.32 40.919-40.92c0-22.598-18.32-40.918-40.919-40.918S9.081 27.992 9.081 50.591" fill="currentColor"/><path d="M93.968 39.04c2.425-.636 3.894-3.128 3.04-5.486A50 50 0 0 0 41.735 1.279c-2.474.414-3.922 2.919-3.285 5.344s3.12 3.849 5.6 3.484a40.916 40.916 0 0 1 44.131 25.769c.902 2.34 3.361 3.802 5.787 3.165" fill="currentfill"/></svg>
                            <svg class="w-4 h-4 mr-1 inline-flex" viewBox="0 0 20 20" fill="currentColor" wire:loading.remove>
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @lang('app.save')
                        </x-button>
                    </div>
                </form>
            </div>

        @elseif($activeTab === 'taxes')
            <div class="mt-6">
                <x-alert type="info" class="mb-0">
                    @lang('messages.taxApplicableInfo')
                </x-alert>
                <div class="flex justify-between items-center mb-4">
                    <x-button type="button" wire:click="showAddCurrency">
                        <svg class="w-4 h-4 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        @lang('modules.settings.addTax')
                    </x-button>
                </div>

                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            @lang('modules.settings.taxName')
                                        </th>

                                        <th scope="col"
                                            class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            @lang('modules.settings.taxPercent')
                                        </th>

                                        <th scope="col"
                                            class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                            @lang('app.action')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                                    @forelse ($taxes as $item)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->tax_name }}
                                        </td>

                                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->tax_percent }}%
                                        </td>

                                        <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                            <x-secondary-button wire:click='showEditCurrency({{ $item->id }})' wire:key='member-edit-{{ $item->id . microtime() }}'
                                                wire:key='editmenu-item-button-{{ $item->id }}'>
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z" clip-rule="evenodd"/></svg>
                                                @lang('app.update')
                                            </x-secondary-button>

                                            <x-danger-button-table wire:click="showDeleteCurrency({{ $item->id }})"  wire:key='member-del-{{ $item->id . microtime() }}'>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                            </x-danger-button-table>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="py-2.5 px-4 space-x-6" colspan="3">
                                            @lang('messages.noCurrencyFound')
                                        </td>
                                    </tr>
                                    @endforelse

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'cbms')
            <div class="mt-6 space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-4 space-y-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">@lang('modules.settings.cbmsSettingsTitle')</h4>
                        <x-help-text>@lang('modules.settings.cbmsSettingsDescription')</x-help-text>

                        <form wire:submit="saveCbmsSettings" class="space-y-4">
                            <div class="flex items-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                                <label for="cbmsEnabled" class="flex items-center space-x-2">
                                    <x-checkbox name="cbmsEnabled" id="cbmsEnabled" wire:model="cbmsEnabled" />
                                    <span>
                                        <span class="font-medium text-gray-900 dark:text-white">@lang('modules.settings.cbmsEnable')</span>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.cbmsEnableHelp')</p>
                                    </span>
                                </label>
                            </div>

                            @if ($cbmsEnabled)
                                <div>
                                    <x-label for="cbmsMode" value="{{ __('modules.settings.cbmsMode') }}" />
                                    <x-select id="cbmsMode" class="mt-1 block w-full" wire:model="cbmsMode">
                                        <option value="test">{{ __('modules.settings.cbmsModeTest') }}</option>
                                        <option value="live">{{ __('modules.settings.cbmsModeLive') }}</option>
                                    </x-select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cbmsModeHelp')</p>
                                    <x-input-error for="cbmsMode" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <x-label for="cbmsUsername" value="{{ __('modules.settings.cbmsUsername') }}" />
                                        <x-input id="cbmsUsername" type="text" class="block mt-1 w-full" wire:model="cbmsUsername" placeholder="Test_CBMS" autocomplete="off" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cbmsUsernameHelp')</p>
                                        <x-input-error for="cbmsUsername" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="cbmsPassword" value="{{ __('modules.settings.cbmsPassword') }}" />
                                        <x-input id="cbmsPassword" type="password" class="block mt-1 w-full" wire:model="cbmsPassword" placeholder="••••••••" autocomplete="new-password" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cbmsPasswordHelp')</p>
                                        <x-input-error for="cbmsPassword" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="cbmsSellerPanOverride" value="{{ __('modules.settings.cbmsSellerPan') }}" />
                                        <x-input id="cbmsSellerPanOverride" type="text" class="block mt-1 w-full" wire:model="cbmsSellerPanOverride" placeholder="999999999" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cbmsSellerPanHelp')</p>
                                        <x-input-error for="cbmsSellerPanOverride" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="cbmsFiscalYear" value="{{ __('modules.settings.cbmsFiscalYear') }}" />
                                        <x-input id="cbmsFiscalYear" type="text" class="block mt-1 w-full" wire:model="cbmsFiscalYear" placeholder="2082.083" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">@lang('modules.settings.cbmsFiscalYearHelp')</p>
                                        <x-input-error for="cbmsFiscalYear" class="mt-2" />
                                    </div>
                                </div>
                            @endif

                            <div>
                                <x-button type="submit" wire:loading.attr="disabled" wire:target="saveCbmsSettings">
                                    @lang('app.save')
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-4">
                        <h4 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">@lang('modules.settings.cbmsLogsTitle')</h4>

                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow">
                                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.settings.cbmsLogOrder')</th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.settings.cbmsLogType')</th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.settings.cbmsLogStatus')</th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.settings.cbmsLogResponse')</th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.settings.cbmsLogSubmittedAt')</th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">@lang('app.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                            @forelse ($cbmsLogs as $log)
                                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="cbms-log-{{ $log->id }}">
                                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $log->order?->show_formatted_order_number ?? ('#' . $log->order_id) }}
                                                    </td>
                                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $log->type === 'bill' ? __('modules.settings.cbmsLogTypeBill') : __('modules.settings.cbmsLogTypeCreditNote') }}
                                                    </td>
                                                    <td class="py-2.5 px-4 text-sm whitespace-nowrap">
                                                        @if ($log->status === 'success')
                                                            <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">{{ $log->status }}</span>
                                                        @elseif ($log->status === 'failed')
                                                            <span class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $log->status }}</span>
                                                        @else
                                                            <span class="bg-yellow-100 uppercase text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{{ $log->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2.5 px-4 text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $log->response_message }}">
                                                        {{ $log->response_code }} {{ $log->response_message ? '- ' . str($log->response_message)->limit(60) : '' }}
                                                    </td>
                                                    <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $log->submitted_at?->format('Y-m-d H:i') ?? '--' }}
                                                    </td>
                                                    <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                                        @if ($log->status === 'failed')
                                                            <x-secondary-button-table wire:click="retryCbmsSubmission({{ $log->id }})" wire:key="cbms-retry-{{ $log->id }}">
                                                                @lang('modules.settings.cbmsRetry')
                                                            </x-secondary-button-table>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="py-2.5 px-4" colspan="6">
                                                        @lang('modules.settings.cbmsLogsEmpty')
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if (method_exists($cbmsLogs, 'links'))
                            <div class="mt-4">
                                {{ $cbmsLogs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-right-modal wire:model.live="showEditCurrencyModal">
        <x-slot name="title">
            {{ __("modules.settings.editCurrency") }}
        </x-slot>

        <x-slot name="content">
            @if ($tax)
            @livewire('forms.editTax', ['tax' => $tax], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditCurrencyModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showAddCurrencyModal">
        <x-slot name="title">
            {{ __("modules.settings.addTax") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addTax')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddCurrencyModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model.defer="confirmDeleteCurrencyModal">
        <x-slot name="title">
            @lang('modules.settings.deleteTax')
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteTaxMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteCurrencyModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($tax)
            <x-danger-button class="ml-3" wire:click='deleteCurrency({{ $tax->id }})' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>


</div>
