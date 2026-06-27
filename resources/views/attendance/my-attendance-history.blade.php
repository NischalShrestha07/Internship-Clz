@extends('components.layouts.app')

@section('content')
    <x-headings title="My Attendance History" />

    <x-notifications />

    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="overflow-x-auto rounded-lg">
            <div class="w-full">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <h1 class="text-xl font-semibold text-black dark:text-white">Attendance History</h1>
                </div>

                <hr class="border-gray-800 dark:border-gray-600 mb-4">

                <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
                    <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
                        <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                            <tr>
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
                                <td class="px-4 py-3 text-center align-middle">
                                    <a href="{{ route('attendance.my-history.sessions', $record['id']) }}"
                                       class="w-6 h-6 inline-flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded mx-auto">
                                        <span class="material-symbols-outlined text-xs leading-none">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    No attendance records found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('attendance.my-history') }}" id="perPageForm">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
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
@endsection
