@extends('components.layouts.app')

@section('content')
    <x-headings title="Employee Attendance History" />

    <x-notifications />

    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-wrap items-center justify-between mb-4 gap-3">
            <h1 class="text-xl font-semibold text-black dark:text-white">Attendance History</h1>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('attendance.employee-history') }}" id="filterForm" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" placeholder="Search by Employee Name"
                           value="{{ request('search') }}"
                           class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">

                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}"
                           class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">

                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}"
                           class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">

                    <select name="status"
                            class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="Checked-Out" {{ request('status') == 'Checked-Out' ? 'selected' : '' }}>Checked-Out</option>
                        <option value="Working" {{ request('status') == 'Working' ? 'selected' : '' }}>Working</option>
                    </select>

                    <button type="submit"
                            class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('attendance.employee-history') }}"
                       class="bg-gray-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-gray-600">
                        Reset
                    </a>

                    <input type="hidden" name="export_csv" value="1" form="csvForm">
                </form>

                <form method="GET" action="{{ route('attendance.employee-history.csv') }}" id="csvForm" class="inline">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from', $dateFrom) }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to', $dateTo) }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <button type="submit"
                            class="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-green-700">
                        Export CSV
                    </button>
                </form>
            </div>
        </div>

        <hr class="border-gray-800 dark:border-gray-600 mb-4">

        <div class="overflow-x-auto rounded-lg">
            <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
                <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
                    <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Employee</th>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Day</th>
                            <th class="px-4 py-3 font-semibold">First Check In</th>
                            <th class="px-4 py-3 font-semibold">Last Check Out</th>
                            <th class="px-4 py-3 font-semibold">Work Hours</th>
                            <th class="px-4 py-3 font-semibold">Break</th>
                            <th class="px-4 py-3 font-semibold">Overtime</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                        @forelse ($attendanceRecords as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-4 py-3">{{ $record['employee_name'] }}</td>
                            <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['date'])->format('M j, Y') }}</td>
                            <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['date'])->format('D') }}</td>
                            <td class="px-4 py-3">
                                {{ $record['check_in'] ? Carbon\Carbon::parse($record['check_in'])->format('h:i:s A') : '--' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $record['check_out'] ? Carbon\Carbon::parse($record['check_out'])->format('h:i:s A') : '--' }}
                            </td>
                            <td class="px-4 py-3">{{ $record['production'] }}</td>
                            <td class="px-4 py-3">
                                @if($record['break'] > 0)
                                {{ App\Helpers\AttendanceHelper::decimalToHoursMinutes($record['break'] / 60) }}
                                @else
                                --
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ App\Helpers\AttendanceHelper::decimalToHoursMinutes($record['overtime']) }}</td>
                            <td class="px-3 py-2">
                                @if ($record['status'] === 'Checked-Out')
                                <span class="inline-block text-[11px] font-medium px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100 whitespace-nowrap">
                                    Checked-Out ({{ $record['record_count'] }})
                                </span>
                                @elseif ($record['status'] === 'Working')
                                <span class="inline-block text-[11px] font-medium px-2 py-1 rounded bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-100 whitespace-nowrap">
                                    Working ({{ $record['record_count'] }})
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <button type="button"
                                            onclick="toggleSessions('sessions-{{ $loop->index }}')"
                                            class="w-6 h-6 flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded mx-auto">
                                        <span class="material-symbols-outlined text-xs leading-none">visibility</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="sessions-{{ $loop->index }}" class="session-details hidden bg-gray-50 dark:bg-gray-600">
                            <td colspan="11" class="px-4 py-3">
                                <div class="pl-8">
                                    <h4 class="font-medium text-sm mb-2">Session Details:</h4>
                                    <ul class="space-y-1">
                                        @foreach ($record['session_details'] as $session)
                                        <li class="text-sm">
                                            {{ $session['check_in'] }} - {{ $session['check_out'] }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                No attendance records found for the selected filters.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('attendance.employee-history') }}" id="perPageForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from', $dateFrom) }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to', $dateTo) }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <select name="per_page" onchange="this.form.submit()"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-center text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                        <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
            <div class="flex justify-center sm:justify-end">
                {{ $attendanceRecords->links() }}
            </div>
        </div>
    </div>

    <script>
        function toggleSessions(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.toggle('hidden');
            }
        }
    </script>
@endsection
