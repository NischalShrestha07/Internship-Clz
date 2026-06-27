@props([
    'title' => null,
    'buttonText' => null,
    'buttonAction' => null,
    'buttonText2' => null,
    'currentDate' => null,
    'currentNepalTime' => null,
    'buttonField' => null,
])

@if ($title || $currentDate || $currentNepalTime)
    <div class="flex justify-between items-center bg-[#f4f3ef] dark:bg-gray-800 rounded-md px-6 py-3 shadow-by-force mb-3">
        @if ($title)
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">
                {{ $title }}
            </h1>
        @endif

        @if ($buttonText && $buttonAction)
            <a href="{{ $buttonAction }}"
               class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm dark:bg-blue-800 dark:hover:bg-blue-700">
                {{ $buttonText }}
            </a>
        @endif

        @if ($buttonText2 && $buttonAction)
            <a href="{{ $buttonAction }}"
               class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-md whitespace-nowrap text-sm">
                {{ $buttonText2 }}
            </a>
        @endif

        @if ($currentDate && $currentNepalTime)
            <div class="flex items-center mt-2 space-x-4">
                <span class="inline-flex items-center px-3 py-2 rounded text-base bg-blue-600 text-white">
                    <span class="material-symbols-outlined text-base mr-1">calendar_today</span>
                    {{ $currentDate->format('Y-m-d') }}
                </span>
                <span class="inline-flex items-center px-3 py-2 rounded text-base bg-blue-400 text-white">
                    <span class="material-symbols-outlined text-base mr-1">schedule</span>
                    {{ $currentNepalTime }}
                </span>
            </div>
        @endif
    </div>
@endif
