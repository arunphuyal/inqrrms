<div class="p-4 mx-4 mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.registersOpenNow')</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $registers->filter(fn($r) => $r->sessions->isNotEmpty())->count() }} / {{ $registers->count() }}</p>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.sessionsToday')</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $todayOpenedCount }}</p>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.closedToday')</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $todayClosedCount }}</p>
        </div>
        <div class="p-4 bg-white border {{ $pendingApprovalsCount > 0 ? 'border-yellow-300 dark:border-yellow-700' : 'border-gray-200 dark:border-gray-700' }} rounded-lg shadow-sm dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.cashRegister.pendingApprovals')</p>
            <p class="mt-1 text-2xl font-semibold {{ $pendingApprovalsCount > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white' }}">{{ $pendingApprovalsCount }}</p>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-lg font-semibold dark:text-white">@lang('modules.cashRegister.registers')</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.registerName')</th>
                        <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('app.status')</th>
                        <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.openedBy')</th>
                        <th class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.cashRegister.openingAmount')</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse ($registers as $register)
                        @php $openSession = $register->sessions->first(); @endphp
                        <tr>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $register->name }}</td>
                            <td class="py-2.5 px-4 text-sm whitespace-nowrap">
                                @if ($openSession)
                                    <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('modules.cashRegister.open')</span>
                                @else
                                    <span class="bg-gray-100 uppercase text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">@lang('modules.cashRegister.closed')</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $openSession?->openedBy?->name ?? '--' }}</td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">{{ $openSession ? currency_format($openSession->opening_amount) : '--' }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-2.5 px-4" colspan="4">@lang('modules.cashRegister.noRegisters')</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
