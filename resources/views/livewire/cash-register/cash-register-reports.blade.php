<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.cashRegister.reports')</h3>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
        <div>
            <x-label for="registerId" value="{{ __('modules.cashRegister.register') }}" />
            <x-select id="registerId" class="mt-1 block w-full" wire:model.live="registerId">
                <option value="">{{ __('app.all') }}</option>
                @foreach ($registers as $register)
                    <option value="{{ $register->id }}">{{ $register->name }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-label for="status" value="{{ __('app.status') }}" />
            <x-select id="status" class="mt-1 block w-full" wire:model.live="status">
                <option value="">{{ __('app.all') }}</option>
                <option value="open">@lang('modules.cashRegister.open')</option>
                <option value="pending_approval">@lang('modules.cashRegister.pendingApproval')</option>
                <option value="approved">@lang('modules.cashRegister.approved')</option>
                <option value="rejected">@lang('modules.cashRegister.rejected')</option>
                <option value="closed">@lang('modules.cashRegister.closed')</option>
            </x-select>
        </div>
        <div>
            <x-label for="fromDate" value="{{ __('app.from') }}" />
            <x-input id="fromDate" type="date" class="block mt-1 w-full" wire:model.live="fromDate" />
        </div>
        <div>
            <x-label for="toDate" value="{{ __('app.to') }}" />
            <x-input id="toDate" type="date" class="block mt-1 w-full" wire:model.live="toDate" />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.register')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.openedBy')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.openingAmount')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.expectedAmount')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.countedAmount')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.difference')</th>
                    <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('app.status')</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($sessions as $session)
                    <tr wire:key="session-{{ $session->id }}">
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $session->cashRegister?->name }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $session->openedBy?->name }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($session->opening_amount) }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $session->expected_closing_amount !== null ? currency_format($session->expected_closing_amount) : '--' }}</td>
                        <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $session->counted_closing_amount !== null ? currency_format($session->counted_closing_amount) : '--' }}</td>
                        <td class="py-2.5 px-4 text-sm whitespace-nowrap {{ $session->difference && abs($session->difference) > 0 ? ($session->difference < 0 ? 'text-red-600' : 'text-green-600') : 'text-gray-900 dark:text-white' }}">
                            {{ $session->difference !== null ? currency_format($session->difference) : '--' }}
                        </td>
                        <td class="py-2.5 px-4 text-sm whitespace-nowrap capitalize dark:text-white">{{ str_replace('_', ' ', $session->status) }}</td>
                    </tr>
                @empty
                    <tr><td class="py-2.5 px-4" colspan="7">@lang('modules.cashRegister.noSessionsFound')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
</div>
