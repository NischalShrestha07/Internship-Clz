@extends('components.layouts.app')

@section('content')
    <x-headings title="Leave Details" />

    <x-notifications />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">View Leave</h2>
            <a href="{{ route('leave') }}"
               class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Leaves</a>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Leave Type</label>
                <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white border-gray-300 dark:border-gray-600">
                    {{ $leave->leaveType->leave_type }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Start Date</label>
                    <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white border-gray-300 dark:border-gray-600">
                        {{ \Carbon\Carbon::parse($leave->start_date_time)->format('j/n/Y g:i A') }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">End Date</label>
                    <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white border-gray-300 dark:border-gray-600">
                        {{ \Carbon\Carbon::parse($leave->end_date_time)->format('j/n/Y g:i A') }}
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white">Leave Reason</label>
                <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white border-gray-300 dark:border-gray-600">
                    {{ $leave->leave_reason }}</p>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold">Status:</span>
                @if ($leave->status === 'approved')
                <span class="bg-green-600 text-white rounded px-4 py-2 text-sm">Approved</span>
                @elseif($leave->status === 'declined')
                <span class="bg-red-600 text-white rounded px-4 py-2 text-sm">Declined</span>
                @else
                <span class="bg-gray-300 text-gray-700 rounded px-4 py-2 text-sm">{{ ucfirst($leave->status) }}</span>
                @endif
            </div>

            @if ($leave->status !== 'cancelled')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                <p class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-black dark:text-white">
                    {{ $leave->remarks ?? 'No remarks' }}
                </p>
            </div>
            @endif

            @if ($leave->status === 'pending')
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('leave.cancel', $leave->id) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this leave request?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Cancel Leave
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
@endsection
