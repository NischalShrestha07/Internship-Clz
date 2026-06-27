@props([
'todayStats' => [],
])

@php
function formatHoursToHHMM($decimalHours) {
if (!is_numeric($decimalHours)) return $decimalHours;

$hours = floor($decimalHours);
$minutes = floor(($decimalHours - $hours) * 60);
return sprintf('%02d:%02d', $hours, $minutes);
}
@endphp

<div
    class="bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 col-span-1">
    <div class="dark:bg-gray-700 border-b dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold dark:text-white">Work Summary</h3>
    </div>
    <div class="p-6">
        <div class="space-y-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Daily Progress</h4>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ formatHoursToHHMM($todayStats['hours']) }} of 8 Hrs
                    </span>
                    <span class="text-sm font-medium text-blue-600 dark:text-blue-700">
                        {{ number_format(($todayStats['hours'] / 8) * 100, 0) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-blue-600 dark:bg-blue-800 h-2.5 rounded-full"
                        style="width: {{ min(100, ($todayStats['hours'] / 8) * 100) }}%"></div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Weekly Progress</h4>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ formatHoursToHHMM($todayStats['week_hours']) }} of 48 Hrs
                    </span>
                    <span class="text-sm font-medium text-green-400 dark:text-green-600">
                        {{ number_format(($todayStats['week_hours'] / 48) * 100, 0) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-green-400 dark:bg-green-600 h-2.5 rounded-full"
                        style="width: {{ min(100, ($todayStats['week_hours'] / 48) * 100) }}%"></div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Monthly Progress</h4>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ formatHoursToHHMM($todayStats['month_hours']) }} of 192 Hrs
                    </span>
                    <span class="text-sm font-medium text-yellow-500 dark:text-yellow-600">
                        {{ number_format(($todayStats['month_hours'] / 192) * 100, 0) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-yellow-500 dark:bg-yellow-700 h-2.5 rounded-full"
                        style="width: {{ min(100, ($todayStats['month_hours'] / 192) * 100) }}%"></div>
                </div>
            </div>

            <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Remaining Hours</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">
                        {{ formatHoursToHHMM($todayStats['remaining_hours']) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
