@props([
'isClockedIn' => false,
'isOnBreak' => false,
'currentNepalTime' => '',
'totalHours' => '0',
'todayStats' => [],
'breakTime' => '0',
'overtimeToday' => '0',
])

<div
    class="bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-600 col-span-1">
    <div class="dark:bg-gray-700 border-b dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold dark:text-white">Time Tracking</h3>
    </div>

    <div class="p-6 h-auto overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-300">Current Status</p>
                <p class="font-medium flex items-center">
                    @if ($isClockedIn)
                    @if ($isOnBreak)
                    <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-yellow-500">On Break</span>
                    @else
                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-green-500">Currently Working</span>
                    @endif
                    @else
                    <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                    <span class="text-gray-600 dark:text-gray-400">Not Checked-In</span>
                    @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-300">Current Time</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $currentNepalTime }}</p>
            </div>
        </div>

        <div
            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-center border border-gray-200 dark:border-gray-600">
            <p class="text-sm text-gray-500 dark:text-gray-300 mb-1">Total Hours Today</p>
            <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ $totalHours }}</p>
            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full"
                    style="width: {{ min(100, ($todayStats['hours'] / 8) * 100) }}%"></div>
            </div>
        </div>

        <div class="flex flex-col space-y-3">
            @if (!$isClockedIn)
            <button wire:click="punchIn"
                class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-800 text-white px-6 py-4 rounded-lg text-lg font-medium flex items-center justify-center space-x-3 transition-colors shadow-md">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Check In</span>
            </button>
            @else
            <button wire:click="punchOut"
                class="w-full bg-red-600 hover:bg-red-700 dark:bg-red-800 text-white px-6 py-4 rounded-lg text-lg font-medium flex items-center justify-center space-x-3 transition-colors shadow-md">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                <span>Check Out</span>
            </button>

            @if ($isClockedIn)
            <div class="grid grid-cols-1 gap-3">
                @if (!$isOnBreak)
                <button wire:click="startBreak"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-3 rounded-lg text-base font-medium flex items-center justify-center space-x-2 transition-colors shadow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Start Break</span>
                </button>
                @endif
                @if ($isOnBreak)
                <button wire:click="endBreak"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-3 rounded-lg text-base font-medium flex items-center justify-center space-x-2 transition-colors shadow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>End Break</span>
                </button>
                @endif
            </div>
            @endif
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6">
            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Break Time</p>
                <p class="font-bold text-gray-900 dark:text-white text-xl">{{ number_format((float) $breakTime, 2) }}
                    mins</p>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Overtime</p>
                <p class="font-bold text-gray-900 dark:text-white text-xl">{{ $overtimeToday }}</p>
            </div>
        </div>

    </div>
</div>