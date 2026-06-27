@extends('components.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attendance Report</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->name }} &bull; {{ now()->format('F Y') }}</p>
        </div>
        <a href="{{ route('hr.employees') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">&larr; Back to Employee List</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-900/30">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">Present Days</p>
                    <p class="text-2xl font-semibold text-green-900 dark:text-green-100 mt-1">{{ $summary['present'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-900/30">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Hours</p>
                    @php
                    $totalMinutes = round($summary['total_hours'] * 60);
                    $h = intdiv($totalMinutes, 60);
                    $m = $totalMinutes % 60;
                    @endphp
                    <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100 mt-1">{{ $h }}h {{ $m }}m</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours Worked</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($records as $record)
                    @php
                    $checkIn = $record->check_in ? \Carbon\Carbon::parse($record->check_in) : null;
                    $checkOut = $record->check_out ? \Carbon\Carbon::parse($record->check_out) : null;
                    $statusClass = $checkOut ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100';
                    $statusText = $checkOut ? 'Checked-Out' : 'Working';
                    $hours = $checkIn && $checkOut ? (function() use ($checkIn, $checkOut) {
                        $mins = round($checkIn->diffInMinutes($checkOut));
                        return intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm';
                    })() : '--';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $checkIn ? $checkIn->format('M d, Y') : '--' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $checkIn ? $checkIn->format('l') : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $checkIn ? $checkIn->format('h:i:s A') : '--' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $checkOut ? $checkOut->format('h:i:s A') : '--' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $hours }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No attendance records found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
