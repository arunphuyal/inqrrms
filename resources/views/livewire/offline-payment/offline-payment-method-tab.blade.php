<div>
    <x-button  class="mt-4" wire:click="addOfflinePayMethod" wire:loading.attr="disabled">@lang('modules.billing.addPaymentMethod')</x-button>

    <div class="py-4">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.billing.name')
                                    </th>
                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.billing.description')
                                    </th>
                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        @lang('app.status')
                                    </th>
                                    <th scope="col"
                                        class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                        @lang('app.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='invoice-list-{{ microtime() }}'>
                            @forelse ($methods as $method)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='method-{{ $method->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center gap-2">
                                        @if ($method->qr_code_image_url)
                                            <img src="{{ $method->qr_code_image_url }}" alt="QR"
                                                class="object-cover border border-gray-200 rounded w-6 h-6 dark:border-gray-600"
                                                title="{{ __('modules.billing.paymentQrCode') }}">
                                        @endif
                                        <span>
                                            @if ($method->name === 'cash')
                                                {{ __('modules.order.payViaCash') }}
                                            @elseif ($method->name === 'bank_transfer')
                                                {{ __('modules.billing.payOffline') }}
                                            @else
                                                {{ $method->name }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $method->description ? str($method->description)->limit(100) : '--' }}
                                </td>
                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($method->status == 'active')
                                    <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.active')</span>
                                    @else
                                    <span class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.inactive')</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right dark:text-white">
                                    <x-secondary-button-table wire:click='editPaymentMethod({{ $method->id }})' wire:key='payment-method-edit-{{ $method->id . microtime() }}'>
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        @lang('app.update')
                                    </x-secondary-button-table>

                                    @if (!in_array($method->name, ['cash', 'bank_transfer']))
                                    <x-danger-button-table wire:click="confirmDelete({{ $method->id }})" wire:key='payment-method-del-{{ $method->id . microtime() }}'>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </x-danger-button-table>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-2.5 px-4 space-x-6 dark:text-white" colspan="5">
                                    @lang('messages.noOfflinePaymentMethodFound')
                                </td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <div wire:key='invoices-table-paginate-{{ microtime() }}'
            class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center mb-4 sm:mb-0 w-full">
                {{ $methods->links() }}
            </div>
        </div>
    </div>


    <x-dialog-modal wire:model.live="showPaymentMethodForm">
        <x-slot name="title">
            {{ $methodId ? __('app.update') : __('app.add') }} @lang('modules.billing.offlinePaymentMethod')
        </x-slot>

        <x-slot name="content">
            @if ($showPaymentMethodForm)
                <form wire:submit="submitForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-label for="name" value="{{ __('modules.billing.name') }}" />
                            @php
                                $isSystemMethod = $methodId && in_array($name, ['cash', 'bank_transfer']);
                                $displayName = $name === 'cash' ? __('modules.order.payViaCash') : ($name === 'bank_transfer' ? __('modules.billing.payOffline') : $name);
                            @endphp

                            @if ($isSystemMethod)
                                <x-input id="name" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" type="text" value="{{ $displayName }}" readonly disabled/>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('messages.systemPaymentMethodCannotBeEdited') }}
                                </p>
                            @else
                                <x-input id="name" class="block mt-1 w-full" type="text" placeholder="{{ __('placeholders.methodExamples') }}" autofocus wire:model='name' />
                            @endif
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="description" value="{{ __('modules.billing.description') }}" />
                            <x-textarea id="description" class="block mt-1 w-full" placeholder="{{ __('placeholders.methodDescription') }}" data-gramm="false"  name="description" wire:model='description' />
                            <x-input-error for="description" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="qrCodeImage" value="{{ __('modules.billing.paymentQrCode') }}" class="mb-2" />
                            <div class="flex items-center space-x-4">
                                {{-- Preview --}}
                                <div class="flex-shrink-0">
                                    <div class="relative w-24 h-24">
                                        @if ($qrCodeImage && is_object($qrCodeImage))
                                            <img src="{{ $qrCodeImage->temporaryUrl() }}" alt="QR Code Preview"
                                                class="object-cover w-24 h-24 border border-gray-200 rounded-lg dark:border-gray-700">
                                        @elseif ($existingQrCodeImage)
                                            <img src="{{ $existingQrCodeImage }}" alt="QR Code"
                                                class="object-cover w-24 h-24 border border-gray-200 rounded-lg dark:border-gray-700">
                                        @else
                                            <div class="flex items-center justify-center w-24 h-24 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
                                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        <div wire:loading wire:target="qrCodeImage" class="absolute inset-0 flex items-center justify-center rounded-lg bg-gray-900/60">
                                            <svg class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Upload --}}
                                <div class="flex-grow space-y-2">
                                    <label class="flex flex-col items-center px-4 py-4 text-gray-500 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        <span class="text-sm">{{ __('modules.billing.uploadPaymentQrCode') }}</span>
                                        <input type="file" wire:model.defer="qrCodeImage" class="hidden" accept="image/*">
                                    </label>

                                    @if ($existingQrCodeImage)
                                        <button type="button" wire:click="removeQrCodeImage" class="text-xs text-red-600 hover:underline dark:text-red-400">
                                            {{ __('app.remove') }}
                                        </button>
                                    @endif

                                    <x-input-error for="qrCodeImage" class="mt-2" />
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('modules.billing.paymentQrCodeHelp') }}
                            </p>
                        </div>

                        @if ($methodId)
                            <div>
                                <x-label for="status" value="{{ __('app.status') }}"/>
                                <x-select id="status" class="mt-1 block w-full" wire:model.defer="status">
                                    <option value="active">{{ __('app.active') }}</option>
                                    <option value="inactive">{{ __('app.inactive') }}</option>
                                </x-select>
                                <x-input-error for="status" class="mt-2"/>
                            </div>
                        @endif


                    <div class="flex w-full pb-4 space-x-4 mt-6 rtl:space-x-reverse">
                        <x-button type="submit" wire:target="submitForm" wire:loading.attr="disabled">
                            {{ $methodId ? __('app.update') : __('app.add') }}
                        </x-button>
                        <x-button-cancel wire:click="$toggle('showPaymentMethodForm')">
                            @lang('app.cancel')
                        </x-button-cancel>
                    </div>
                </form>
            @endif
        </x-slot>
    </x-dialog-modal>


    <x-confirmation-modal wire:model.defer="confirmDeleteModal">
        <x-slot name="title">
            @lang('modules.billing.deleteOfflinePaymentMethod')
        </x-slot>

        <x-slot name="content">
            @lang('modules.billing.askDeleteOfflinePaymentMethod')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($deleteId)
            <x-danger-button class="ml-3" wire:click='delete({{ $deleteId }})' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

</div>
