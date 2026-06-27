@extends('components.layouts.app')

@section('content')
<x-headings title="Leave Details" />
<x-notifications />

<div class="bg-white dark:bg-gray-800 rounded-lg p-6">
    <div class="mb-6 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Leaves Remaining</h3>
            <a href="{{ route('leave_manage') }}"
                class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white px-2 py-1 rounded-md">
                Back
            </a>
        </div>
        <hr class="border-t border-gray-800 dark:border-gray-600 mb-5" />
        <div class="px-4 pb-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($leavesCard && $leavesCard->count())
                @foreach($leavesCard->sortBy(fn($l) => strtoupper($l->leaveType->leave_type[0])) as $card)
                    @php
                        $initial = strtoupper($card->leaveType->leave_type[0]);
                        $bgClass = match ($initial) {
                            'S' => 'from-yellow-500 to-yellow-800',
                            'A' => 'from-blue-500 to-blue-800',
                            'C' => 'from-green-500 to-green-800',
                            default => 'from-blue-500 to-blue-800',
                        };
                        $whole = floor($card->remaining);
                        $decimal = $card->remaining - $whole;
                        $remainingText = $whole . ' day' . ($whole != 1 ? 's' : '') . ($decimal == 0.5 ? ' and half' : '');
                    @endphp
                    <x-cards :initial="$initial" :bgClass="$bgClass">
                        <div class="flex items-center space-x-5">
                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br {{ $bgClass }} rounded-xl flex items-center justify-center text-white shadow-sm">
                                <span class="text-xl font-bold tracking-tight">{{ $initial }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">{{ $card->leaveType->leave_type }}</h5>
                                <div class="mt-1 flex items-baseline">
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        <b>{{ $remainingText }}</b>
                                    </p>
                                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Remaining</span>
                                </div>
                            </div>
                        </div>
                    </x-cards>
                @endforeach
            @else
                <div class="col-span-full text-center py-10 text-gray-500 dark:text-gray-300">
                    <span class="text-lg font-medium">No leaves assigned</span>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 overflow-auto">
        <div class="flex flex-wrap items-center justify-between mb-4 gap-3">
            <h1 class="text-xl font-semibold text-black dark:text-white">Leave History</h1>
            <form method="GET" action="{{ route('employee.show', $employee->id) }}" class="flex flex-wrap items-center gap-3">
                <select name="leave_type_id"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                    <option value="">All Leave Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->leave_type }}</option>
                    @endforeach
                </select>
                <select name="status"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                    <option value="">All Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700">Filter</button>
                <a href="{{ route('employee.show', $employee->id) }}" class="bg-gray-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-gray-600">Reset</a>
                <a href="{{ route('employee.report.csv', array_merge(['employeeId' => $employee->id], request()->query())) }}"
                    class="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-green-700 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    CSV
                </a>
            </form>
        </div>
        <hr class="border-gray-800 dark:border-gray-600 mb-4" />
        <div class="overflow-x-auto rounded-lg">
            <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
                <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
                    <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Employee Name</th>
                            <th class="px-4 py-3 font-semibold">Leave Type</th>
                            <th class="px-4 py-3 font-semibold">Start Date Time</th>
                            <th class="px-4 py-3 font-semibold">End Date Time</th>
                            <th class="px-4 py-3 font-semibold">Leave Reason</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                        @forelse($leaves as $leave)
                        <tr>
                            <td class="px-4 py-3">{{ $leave->user->name }}</td>
                            <td class="px-4 py-3">{{ $leave->leaveType->leave_type }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y') }}<br>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y') }}<br>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->end_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-[100px] truncate" title="{{ $leave->leave_reason }}">{{ $leave->leave_reason }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block text-xs font-medium px-2 py-1 rounded-full
                                    @if($leave->status === 'approved') bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100
                                    @elseif($leave->status === 'declined') bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100
                                    @elseif($leave->status === 'pending') bg-yellow-100 text-yellow-500 dark:bg-yellow-600 dark:text-yellow-100
                                    @else bg-red-200 text-gray-700 dark:bg-red-500 dark:text-gray-100 @endif">
                                    {{ $leave->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No leave records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">
            {{ $leaves->links() }}
        </div>
    </div>
</div>
@endsection
