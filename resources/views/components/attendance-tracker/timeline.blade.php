@props([
'todayActivities' => [],
])

<div
    class="bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 col-span-1">
    <div class="dark:bg-gray-700 border-b dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold dark:text-white">Today's Timeline</h3>
    </div>

    <div class="p-6 h-[400px] overflow-y-auto">
        <ul class="-mb-8">
            @if (count($todayActivities) > 0)
            @foreach ($todayActivities as $index => $activity)
            <li>
                <div class="relative pb-8">
                    @if ($index < count($todayActivities) - 1) <span
                        class="absolute top-5 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-600"
                        aria-hidden="true"></span>
                        @endif

                        <div class="relative flex items-start space-x-4">
                            <!-- Icon -->
                            <div>
                                <span
                                    class="h-8 w-8 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800
                                        @if ($activity['action'] == 'Check In') bg-blue-500 dark:bg-blue-700
                                        @elseif ($activity['action'] == 'Check Out') bg-red-500 dark:bg-red-700
                                        @elseif ($activity['action'] == 'Break Start') bg-yellow-500 dark:bg-yellow-700
                                        @elseif ($activity['action'] == 'Break End') bg-green-500 dark:bg-green-700  @endif">
                                    @switch($activity['action'])
                                    @case('Check In')
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    @break

                                    @case('Check Out')
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                    @break

                                    @case('Break Start')
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @break

                                    @case('Break End')
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    @break
                                    @endswitch
                                </span>
                            </div>

                            <!-- Card -->
                            <div
                                class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $activity['action'] }}
                                        </p>
                                        @if (isset($activity['duration']))
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Duration: {{ number_format((float) $activity['duration'], 2) }} mins
                                        </p>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($activity['time'])->format('d M Y, h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </li>
            @endforeach
            @else
            <!-- No Activity -->
            <div class="text-center py-12">
                <svg class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No activities today</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Your timeline will appear here once you check
                    in.</p>
            </div>
            @endif
        </ul>
    </div>
</div>