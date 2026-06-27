@props([
'title' => '',
'value' => '',
'icon' => 'clock',
'color' => 'blue',
'suffix' => '',
'prefix' => '',
'format' => 'decimal',
])

@php
$displayValue = $value;
if ($format === 'time' && is_numeric($value)) {
$hours = floor($value);
$minutes = floor(($value - $hours) * 60);
$displayValue = sprintf('%02d:%02d', $hours, $minutes);
}

$availableColors = ['green', 'yellow', 'blue'];
$validColor = in_array($color, $availableColors) ? $color : 'blue';

$colorClasses = [
'green' => 'from-green-500 to-green-600 dark:from-green-700 dark:to-green-800',
'yellow' => 'from-yellow-500 to-yellow-600 dark:from-yellow-700 dark:to-yellow-800',
'blue' => 'from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800',
];

$icons = [
'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
'calendar' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2
0 012-2h2a2 2 0 012 2',
'users' =>
'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018
0z',
'check' => 'M5 13l4 4L19 7',
'exclamation' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
0L3.34 16c-.77 1.333.192 3 1.732 3z',
];
@endphp

<div
    class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-by-force hover:shadow-by-force-hover transition-all duration-250 ease-out border border-gray-100 dark:border-gray-700 w-full">
    <div class="flex items-center gap-5">
        <!-- Icon Background -->
        <div
            class="flex-shrink-0 w-14 h-14 bg-gradient-to-br {{ $colorClasses[$validColor] }} rounded-xl flex items-center justify-center text-white shadow-sm">
            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="{{ $icons[$icon] ?? $icons['clock'] }}" />
            </svg>
        </div>

        <!-- Title and Value -->
        <div class="flex-1 min-w-0 space-y-1">
            <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">
                {{ $title }}
            </h5>
            <div class="flex items-baseline">
                <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">
                    {{ $prefix }}{{ $displayValue }}{{ $suffix }}
                </p>
            </div>
        </div>
    </div>
</div>
