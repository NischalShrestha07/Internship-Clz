<div class="main transition-all duration-400">
    <x-notifications />
    <!-- Header Section -->
    <div class="mb-5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">
                    @if (auth()->user()->hasRole('HR Manager'))
                    HR Manager Dashboard
                    @else
                    Employee Dashboard
                    @endif
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Welcome Back, {{ Auth::user()->name }} •
                    {{ $currentDate->format('l, F j, Y') }}
                </p>
            </div>
            <x-time-hour :current-time="$currentTime" :pending-leave-requests="$pendingLeaveRequests"
                :approved-leave-requests="$approvedLeaveRequests"
                :is-hr-manager="auth()->user()->hasRole('HR Manager')" />
        </div>
    </div>

    <!-- Primary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-6 mb-3">
        <x-attendance-tracker.stats-card title="Attendance Status"
            value="{{ $isClockedIn ? 'Checked-In' : 'Checked-Out' }}"
            icon="{{ $isClockedIn ? 'check-circle' : 'clock' }}" color="{{ $isClockedIn ? 'green' : 'gray' }}"
            description="{{ $isClockedIn && $punchInTime ? 'Since ' . $punchInTime->format('g:i A') . ' • ' . $punchInTime->diffForHumans() : ($lastCheckInTime ? 'Last check-in ' . $lastCheckInTime->format('g:i A, M j') : 'Not checked in today') }}">
            <div
                class="p-3 rounded-full {{ $isClockedIn ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-100 dark:bg-gray-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 {{ $isClockedIn ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </x-attendance-tracker.stats-card>

        {{-- <x-attendance-tracker.stats-card title="Total Work Hours"
            value="{{ $this->decimalToHoursMinutes($totalWorkHours) }}" icon="clock" color="green"
            description="{{ $monthlyHours }} this month • {{ $this->decimalToHoursMinutes($totalHoursToday) }} today">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
        </x-attendance-tracker.stats-card> --}}

        <x-attendance-tracker.stats-card title="Leave Balance"
            value="{{ intval($remainingLeaveDays) }}/{{ intval($totalLeaveDays) }} days" icon="calendar"
            color="{{ $remainingLeaveDays > 0 ? 'green' : 'red' }}"
            description="{{ $pendingLeaveRequests }} pending request(s)">
            <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>

            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                <div class="bg-blue-500 dark:bg-blue-400 h-2.5 rounded-full"
                    style="width: {{ $totalLeaveDays > 0 ? intval(($remainingLeaveDays / $totalLeaveDays) * 100) : 0 }}%">
                </div>
            </div>
        </x-attendance-tracker.stats-card>


        @if ($isHrManager)
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium">Team Overview</p>
            <div class="grid grid-cols-4 gap-2 text-center">
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalEmployeesClockedIn }}
                    </p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Present</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalEmployeesOnLeave }}
                    </p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">On Leave</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                        {{ $totalPendingLeaveRequests }}
                    </p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Pending</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalEmployees }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Total</p>
                </div>
            </div>
        </div>
        @else
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-2 px-5 hover:shadow-xl transition-shadow">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Performance</p>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">
                        {{ $performanceScore ?? '--' }}%
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">This Month</p>
                </div>
                <div class="w-16 h-16">
                    <svg viewBox="0 0 36 36" class="circular-chart">
                        <path class="circle-bg"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="circle" stroke-dasharray="{{ $performanceScore ?? '0' }}, 100"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <text x="18" y="20.5" class="percentage">{{ $performanceScore ?? '--' }}%</text>
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>
    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Time Tracking Card -->
                <div
                    class="bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-600">
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
                                <p class="font-medium text-gray-800 dark:text-white">{{ $currentTime }}</p>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-center border border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300 mb-1">Total Hours Today</p>
                            <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                                {{$this->decimalToHoursMinutes($totalHoursToday) }}</p>
                            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ min(100, ($this->calculateDailyTotalHours() / 8) * 100) }}%">
                                </div>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>End Break</span>
                                </button>
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div
                                class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Break Time</p>
                                <p class="font-bold text-gray-900 dark:text-white text-xl">{{ $breakTime }}</p>
                            </div>
                            <div
                                class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Overtime</p>
                                <p class="font-bold text-gray-900 dark:text-white text-xl">{{ $overtimeToday }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Attendance Summary -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden w-full">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-white flex items-center">
                            Attendance Summary
                        </h3>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-5">
                            <!-- Stats Summary -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-800 dark:text-white">Activities Overview</h4>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ $currentDate->format('M j') }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">Work Hours</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">
                                            {{ $this->decimalToHoursMinutes($totalHoursToday) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">Break Time</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">
                                            @php
                                            $breakMins = (int) str_replace(' mins', '', $breakTimeToday);
                                            echo $this->decimalToHoursMinutes($breakMins / 60);
                                            @endphp
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">Overtime</span>
                                        </div>
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                            {{ $this->decimalToHoursMinutes(floatval($overtimeToday)) }}
                                        </span>
                                    </div>

                                    @if ($isClockedIn)
                                    <div
                                        class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">Remaining</span>
                                        </div>
                                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                            {{ $this->decimalToHoursMinutes($remainingHoursToday) }} to complete
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Attendance Rate -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-800 dark:text-white flex items-center">
                                        Monthly Attendance
                                    </h4>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ now()->format('F Y') }}
                                    </span>
                                </div>

                                <div class="flex items-center">
                                    <!-- Progress Circle -->
                                    <div class="relative w-20 h-24 mr-5">
                                        <svg viewBox="0 0 36 36" class="circular-chart block">
                                            <path class="circle-bg"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                fill="none" stroke="#e0e0e0" stroke-width="3"
                                                stroke-dasharray="100, 100" />
                                            <path class="circle"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                fill="none" stroke="#4CAF50" stroke-width="3" stroke-linecap="round"
                                                stroke-dasharray="{{ $attendanceRate }}, 100" />
                                            <text x="18" y="22" class="percentage" font-size="6" text-anchor="middle"
                                                dominant-baseline="middle">{{ $attendanceRate }}%</text>
                                        </svg>
                                    </div>

                                    <!-- Stats -->
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span
                                                    class="inline-block w-3 h-3 rounded-full bg-green-500 mr-3"></span>
                                                <span class="text-sm text-gray-600 dark:text-gray-300">Present</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-white">
                                                {{ $daysPresent }} <span class="text-xs text-gray-400">days</span>
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-3"></span>
                                                <span class="text-sm text-gray-600 dark:text-gray-300">Absent</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-white">
                                                {{ $daysAbsent }} <span class="text-xs text-gray-400">days</span>
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span
                                                    class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-3"></span>
                                                <span class="text-sm text-gray-600 dark:text-gray-300">Late
                                                    Arrivals</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-white">
                                                {{ $lateArrivals }} <span class="text-xs text-gray-400">times</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <style>
                                .circular-chart {
                                    display: block;
                                    margin: 0 auto;
                                    max-width: 100%;
                                    max-height: 100%;
                                }

                                .circle-bg {
                                    fill: none;
                                    stroke: #e0e0e0;
                                    stroke-width: 3;
                                }

                                .circle {
                                    fill: none;
                                    stroke-width: 3;
                                    stroke-linecap: round;
                                    animation: progress 1s ease-out forwards;
                                }

                                .percentage {
                                    font-family: sans-serif;
                                    font-size: 0.6em;
                                    font-weight: bold;
                                    text-anchor: middle;
                                    fill: #4a5568;
                                }

                                .dark .circle-bg {
                                    stroke: #374151;
                                }

                                .dark .circle {
                                    stroke: #4CAF50;
                                }

                                .dark .percentage {
                                    fill: #f3f4f6;
                                }

                                @keyframes progress {
                                    0% {
                                        stroke-dasharray: 0, 100;
                                    }
                                }
                            </style>
                        </div>
                    </div>
                </div>

                <style>
                    .circular-chart {
                        display: block;
                        margin: 0 auto;
                        max-width: 100%;
                        max-height: 100%;
                    }

                    .circle-bg {
                        fill: none;
                        stroke: #eee;
                        stroke-width: 3;
                    }

                    .circle {
                        fill: none;
                        stroke-width: 3;
                        stroke-linecap: round;
                        animation: progress 1s ease-out forwards;
                    }

                    @keyframes progress {
                        0% {
                            stroke-dasharray: 0, 100;
                        }
                    }

                    .dark .circle-bg {
                        stroke: #374151;
                    }

                    .dark .circle {
                        stroke: #4CAF50;
                    }

                    .dark .percentage {
                        fill: #f3f4f6;
                    }

                    .percentage {
                        font-family: sans-serif;
                        font-size: 0.5em;
                        text-anchor: middle;
                        fill: #4a5568;
                    }
                </style>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Announcements Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                    <!-- Header -->
                    <div
                        class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Announcements</h3>
                        <a href="{{ route('attendance.calendar') }}"
                            class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Body -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($announcements as $announcement)
                        <div
                            class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 space-y-2">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">
                                        {{ $announcement['title'] }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $announcement['description'] }}
                                    </p>
                                </div>
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 whitespace-nowrap">
                                    {{ $announcement['type'] }}
                                </span>
                            </div>

                            <div
                                class="flex flex-wrap justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $announcement['date']->format('M j, Y') }} at
                                    {{ $announcement['time'] }}</span>
                                <span>By:
                                    <span>{{ $announcement['created_by'] }}->{{ $announcement['posted_at'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                            No recent announcements.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Time Off Card-->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                    <!-- Header -->
                    <div
                        class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Upcoming Time Off</h3>
                        <a href="{{route('leave_manage')}}"
                            class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Body -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($upcomingLeaves as $leave)
                        <div
                            class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 space-y-2">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">
                                        {{ $leave['employee'] }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($leave['start'])->format('M d') }} – {{
                                        \Carbon\Carbon::parse($leave['end'])->format('M d') }}
                                    </p>
                                </div>
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 whitespace-nowrap">
                                    {{ $leave['type'] }}
                                </span>
                            </div>

                            <div
                                class="flex flex-wrap justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ round($leave['days']) }} day(s)</span>
                                <span>Starts: {{ \Carbon\Carbon::parse($leave['start'])->format('M j, Y') }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                            No upcoming time off.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
                <!-- Table Scroll Area -->
                <div class="overflow-x-auto rounded-lg">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <div
                            class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800 rounded-t-xl">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Recent Attendance</h3>
                            <a href="{{ Auth::user()->hasRole('HR Manager') ? route('attendance.employee-history') : route('attendance.my-history') }}"
                                class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition duration-200">
                                View All
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <x-table>
                            <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                                <tr>
                                    @if (Auth::user()->hasRole('HR Manager'))
                                    <th class="px-4 py-3 font-semibold">Employee</th>
                                    @endif
                                    <th class="px-4 py-3 font-semibold">Date</th>
                                    <th class="px-4 py-3 font-semibold">Day</th>
                                    <th class="px-4 py-3 font-semibold">First Check In</th>
                                    <th class="px-4 py-3 font-semibold">Last Check Out</th>
                                    <th class="px-4 py-3 font-semibold">Work Hours</th>
                                    <th class="px-4 py-3 font-semibold">Break</th>
                                    <th class="px-4 py-3 font-semibold">Overtime</th>
                                    <th class="px-4 py-3 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                                @forelse($attendanceRecords as $record)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                    @if (Auth::user()->hasRole('HR Manager'))
                                    <td class="px-4 py-3">{{ $record['employee_name'] }}</td>
                                    @endif
                                    <td class="px-4 py-3">
                                        {{ Carbon\Carbon::parse($record['date'])->format('M j, Y') }}
                                    </td>
                                    <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['date'])->format('D') }}</td>

                                    <td class="px-4 py-3">
                                        {{ Carbon\Carbon::parse($record['check_in'])->format('h:i A') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $record['check_out'] ?
                                        Carbon\Carbon::parse($record['check_out'])->format('h:i A') : '--' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $this->decimalToHoursMinutes($record['production']) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $this->decimalToHoursMinutes($record['break'] / 60) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $this->decimalToHoursMinutes($record['overtime']) }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @if ($record['status'] === 'Checked-Out')
                                        <span
                                            class="inline-block text-[11px] font-medium px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100 whitespace-nowrap">
                                            Checked-Out ({{ $record['record_count'] }})
                                        </span>
                                        @elseif ($record['status'] === 'Working')
                                        <span
                                            class="inline-block text-[11px] font-medium px-2 py-1 rounded bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-100 whitespace-nowrap">
                                            Working ({{ $record['record_count'] }})
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No recent attendance records found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>
                <!-- Add pagination links -->
                <div class="px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    {{ $attendanceRecords->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update the time display
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
            const formattedTime = `${formattedHours}:${formattedMinutes} ${ampm}`;

            // Update the time display
            document.getElementById('liveCurrentTime').textContent = formattedTime;
        }

        // Update time immediately
        updateTime();

        // Update time every second (or set to 60000 for every minute)
        setInterval(updateTime, 1000);

        // Optional: Sync with Livewire every minute to ensure server accuracy
        setInterval(() => {
            Livewire.dispatch('refreshTime');
        }, 60000);
    });
</script>