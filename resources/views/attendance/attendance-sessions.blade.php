@extends('components.layouts.app')

@section('content')
    <x-headings title="Attendance Sessions - {{ Carbon\Carbon::parse($date)->format('M j, Y') }}" />

    <x-notifications />

    <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-lg font-medium text-gray-700 dark:text-gray-300">Name: {{ $employeeName }}</span>
            </div>
            <a href="{{ route('attendance.my-history') }}"
               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                &larr; Back to History
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="w-full table-auto text-sm shadow-xl border border-gray-300 dark:border-gray-700">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-left">Check In</th>
                        <th class="px-4 py-3 font-semibold text-left">Check Out</th>
                        <th class="px-4 py-3 font-semibold text-left">Work Hours</th>
                        <th class="px-4 py-3 font-semibold text-left">Break Time</th>
                        <th class="px-4 py-3 font-semibold text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $session['check_in'] }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $session['check_out'] }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $session['work_hours'] ?? '--' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $session['break'] }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $session['status'] === 'Working' ?
                                   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100' :
                                   'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' }}">
                                {{ $session['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No attendance sessions found for this date.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
