@extends('components.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Report</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->name }} &bull; {{ now()->format('F Y') }}</p>
        </div>
        <a href="{{ route('hr.employees') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">&larr; Back to Employee List</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-900/30">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">Approved</p>
                    <p class="text-2xl font-semibold text-green-900 dark:text-green-100 mt-1">{{ $summary['approved'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-100 dark:border-red-900/30">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">Rejected</p>
                    <p class="text-2xl font-semibold text-red-900 dark:text-red-100 mt-1">{{ $summary['rejected'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-100 dark:border-yellow-900/30">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100 mt-1">{{ $summary['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-900/30">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Days</p>
                    <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100 mt-1">{{ number_format($summary['total_days'], 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reason</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($records as $leave)
                    @php
                    $startDt = $leave->start_date_time ? \Carbon\Carbon::parse($leave->start_date_time) : null;
                    $endDt = $leave->end_date_time ? \Carbon\Carbon::parse($leave->end_date_time) : null;
                    $statusColors = [
                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100',
                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                        'declined' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                    ];
                    $st = strtolower($leave->status ?? '');
                    $sc = $statusColors[$st] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $leave->leaveType->leave_type ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $startDt ? $startDt->format('M d, Y') : '--' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $startDt ? $startDt->format('h:i:s A') : '' }} - {{ $endDt ? $endDt->format('h:i:s A') : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sc }}">{{ $leave->status ? ucfirst($leave->status) : 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">{{ $leave->leave_reason ?? '' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No leave records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
