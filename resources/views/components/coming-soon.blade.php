@props(['title', 'description', 'items' => []])

<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="flex items-center gap-3 mb-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 uppercase">
            {{ __('app.comingSoon') }}
        </span>
        <h3 class="text-xl font-semibold dark:text-white">{{ $title }}</h3>
    </div>

    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>

    @if (count($items))
        <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.plannedScreens') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($items as $item)
                <div class="flex items-center gap-2 p-3 text-sm text-gray-700 border border-gray-200 rounded-lg dark:text-gray-300 dark:border-gray-700">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $item }}
                </div>
            @endforeach
        </div>
    @endif
</div>
