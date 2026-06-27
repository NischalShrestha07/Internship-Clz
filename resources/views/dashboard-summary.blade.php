<div class="main transition-all duration-400">
    <x-notifications />

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
                    Welcome Back, {{ Auth::user()->name }} &bull;
                    {{ $currentDate->format('l, F j, Y') }}
                </p>
            </div>
            <x-time-hour :current-time="$currentTime" :pending-leave-requests="$pendingLeaveRequests"
                :approved-leave-requests="$approvedLeaveRequests"
                :is-hr-manager="auth()->user()->hasRole('HR Manager')" />
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-6 mb-3">
        <x-attendance-tracker.stats-card title="Attendance Status"
            value="{{ $isClockedIn ? 'Checked-In' : 'Checked-Out' }}"
            icon="{{ $isClockedIn ? 'check' : 'clock' }}" color="{{ $isClockedIn ? 'green' : 'gray' }}" />

        <x-attendance-tracker.stats-card title="Leave Balance"
            value="{{ intval($remainingLeaveDays) }}/{{ intval($totalLeaveDays) }} days" icon="calendar"
            color="{{ $remainingLeaveDays > 0 ? 'green' : 'red' }}" />

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            @if ($isHrManager)
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium">Team Overview</p>
            <div class="grid grid-cols-4 gap-2 text-center">
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalEmployeesClockedIn }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Present</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalEmployeesOnLeave }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">On Leave</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalPendingLeaveRequests }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Pending</p>
                </div>
                <div>
                    <p class="text-base font-semibold text-gray-800 dark:text-white">{{ $totalEmployees }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Total</p>
                </div>
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Today's Hours</p>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white" id="total-hours-card">{{ $totalHoursToday }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $remainingHoursToday }} remaining</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-600">
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
                                <p class="font-medium text-gray-800 dark:text-white" id="live-clock">{{ $currentTime }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-center border border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-300 mb-1">Total Hours Today</p>
                            <p class="text-4xl font-bold text-blue-600 dark:text-blue-400" id="total-hours">{{ $totalHoursToday }}</p>
                            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" id="total-hours-bar"
                                    style="width: {{ min(100, $workedMinutesToday / 60 / 8 * 100) }}%"></div>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-3">
                            @if (!$isClockedIn)
                            <form method="POST" action="{{ route('attendance.punch-in') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-800 text-white px-6 py-4 rounded-lg text-lg font-medium flex items-center justify-center space-x-3 transition-colors shadow-md">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Check In</span>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('attendance.punch-out') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 dark:bg-red-800 text-white px-6 py-4 rounded-lg text-lg font-medium flex items-center justify-center space-x-3 transition-colors shadow-md">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                    <span>Check Out</span>
                                </button>
                            </form>

                            <div class="grid grid-cols-1 gap-3">
                                @if (!$isOnBreak)
                                <form method="POST" action="{{ route('attendance.break-start') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-3 rounded-lg text-base font-medium flex items-center justify-center space-x-2 transition-colors shadow">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Start Break</span>
                                    </button>
                                </form>
                                @endif
                                @if ($isOnBreak)
                                <form method="POST" action="{{ route('attendance.break-end') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-3 rounded-lg text-base font-medium flex items-center justify-center space-x-2 transition-colors shadow">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>End Break</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                            @endif
                        </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Break Time</p>
                                    <p class="font-bold text-gray-900 dark:text-white text-xl">{{ $breakTimeToday }}</p>
                                </div>
                                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Overtime</p>
                                    <p class="font-bold text-gray-900 dark:text-white text-xl">{{ $overtimeToday }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden w-full">
                    <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-white flex items-center">
                            Attendance Summary
                        </h3>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-5">
                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-medium text-gray-800 dark:text-white">Monthly Attendance</h4>
                                    <span class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ now()->format('F Y') }}
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-3"></span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Present</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $daysPresent }} days</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-3"></span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Absent</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $daysAbsent }} days</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-3"></span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Late Arrivals</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $lateArrivals }} times</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-800 dark:text-white">Duration</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Work Hours</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $totalHoursToday }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Break Time</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $breakTimeToday }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Overtime</span>
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $overtimeToday }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Announcements</h3>
                        <a href="{{ route('attendance.calendar') }}"
                            class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($announcements as $announcement)
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 space-y-2">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">{{ $announcement['title'] }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $announcement['description'] }}</p>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 whitespace-nowrap">
                                    {{ $announcement['type'] }}
                                </span>
                            </div>
                            <div class="flex flex-wrap justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $announcement['date']->format('M j, Y') }} at {{ $announcement['time'] }}</span>
                                <span>By: {{ $announcement['created_by'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                            No recent announcements.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Upcoming Time Off</h3>
                        @role('HR Manager')
                        <a href="{{ route('leave_manage') }}"
                            class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endrole
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($upcomingLeaves as $leave)
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 space-y-2">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white">{{ $leave['employee'] }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($leave['start'])->format('M d') }} &ndash; {{ \Carbon\Carbon::parse($leave['end'])->format('M d') }}
                                    </p>
                                </div>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 whitespace-nowrap">
                                    {{ $leave['type'] }}
                                </span>
                            </div>
                            <div class="flex flex-wrap justify-between items-center text-xs text-gray-500 dark:text-gray-400">
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
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const clockEl = document.getElementById('live-clock');
    const totalEl = document.getElementById('total-hours');
    const totalCardEl = document.getElementById('total-hours-card');
    const barEl = document.getElementById('total-hours-bar');
    if (!clockEl) return;

    const d = new Date();
    const parts = clockEl.textContent.trim().match(/(\d+):(\d+):(\d+)\s*(AM|PM)/i);
    if (!parts) return;

    let h = parseInt(parts[1], 10);
    const m = parseInt(parts[2], 10);
    const s = parseInt(parts[3], 10);
    const isPM = parts[4].toUpperCase() === 'PM';
    if (isPM && h !== 12) h += 12;
    if (!isPM && h === 12) h = 0;

    d.setHours(h, m, s, 0);

    const isClockedIn = {{ $isClockedIn ? 'true' : 'false' }};
    const isOnBreak = {{ $isOnBreak ? 'true' : 'false' }};
    const shouldTick = isClockedIn && !isOnBreak;

    let totalSeconds = 0;
    if (totalEl) {
        const tp = totalEl.textContent.trim().match(/(\d+)h\s*(\d+)m\s*(\d+)s/);
        if (tp) totalSeconds = parseInt(tp[1]) * 3600 + parseInt(tp[2]) * 60 + parseInt(tp[3]);
    }

    setInterval(() => {
        d.setSeconds(d.getSeconds() + 1);
        clockEl.textContent = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

        if (shouldTick && totalEl) {
            totalSeconds++;
            const th = Math.floor(totalSeconds / 3600);
            const tm = Math.floor((totalSeconds % 3600) / 60);
            const ts = totalSeconds % 60;
            const text = th + 'h ' + String(tm).padStart(2, '0') + 'm ' + String(ts).padStart(2, '0') + 's';
            totalEl.textContent = text;
            if (totalCardEl) totalCardEl.textContent = text;

            if (barEl) {
                const pct = Math.min(100, (totalSeconds / 3600) / 8 * 100);
                barEl.style.width = pct + '%';
            }
        }
    }, 1000);
});
</script>
@endpush
