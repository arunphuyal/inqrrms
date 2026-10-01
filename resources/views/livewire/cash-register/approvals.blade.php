<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.cashRegister.approvals')</h3>
    <x-help-text class="mb-6">@lang('modules.cashRegister.approvalsDescription')</x-help-text>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.register')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.closedBy')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.expectedAmount')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.countedAmount')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.difference')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">@lang('app.action')</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($sessions as $session)
                    <tr wire:key="approval-{{ $session->id }}">
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $session->cashRegister?->name }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $session->closedBy?->name }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($session->expected_closing_amount) }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($session->counted_closing_amount) }}</td>
                        <td class="py-2.5 px-4 text-sm whitespace-nowrap {{ $session->difference < 0 ? 'text-red-600' : 'text-green-600' }}">{{ currency_format($session->difference) }}</td>
                        <td class="py-2.5 px-4 text-right whitespace-nowrap">
                            <x-secondary-button-table wire:click="review({{ $session->id }})">@lang('modules.cashRegister.review')</x-secondary-button-table>
                        </td>
                    </tr>
                @empty
                    <tr><td class="py-2.5 px-4" colspan="6">@lang('modules.cashRegister.noPendingApprovals')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-dialog-modal wire:model.live="showReviewModal">
        <x-slot name="title">@lang('modules.cashRegister.review')</x-slot>
        <x-slot name="content">
            @if ($reviewingSession)
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.expectedAmount')</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ currency_format($reviewingSession->expected_closing_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.countedAmount')</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ currency_format($reviewingSession->counted_closing_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.difference')</p>
                            <p class="font-semibold {{ $reviewingSession->difference < 0 ? 'text-red-600' : 'text-green-600' }}">{{ currency_format($reviewingSession->difference) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.closedBy')</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $reviewingSession->closedBy?->name }}</p>
                        </div>
                    </div>

                    <div>
                        <x-label for="note" value="{{ __('modules.cashRegister.approvalNote') }}" />
                        <x-textarea id="note" class="block mt-1 w-full" wire:model="note" />
                        <x-input-error for="note" class="mt-2" />
                    </div>

                    <div class="flex w-full pb-4 space-x-4 mt-6">
                        <x-button type="button" wire:click="approve">@lang('modules.cashRegister.approve')</x-button>
                        <x-danger-button type="button" wire:click="reject">@lang('modules.cashRegister.reject')</x-danger-button>
                        <x-button-cancel wire:click="$set('showReviewModal', false)">@lang('app.cancel')</x-button-cancel>
                    </div>
                </div>
            @endif
        </x-slot>
    </x-dialog-modal>
</div>
