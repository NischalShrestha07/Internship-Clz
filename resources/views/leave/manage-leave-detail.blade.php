@extends('components.layouts.app')

@section('content')
    <x-headings title="Leave Details" />

    <x-notifications />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg">
        @if ($leave->status === 'pending')
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Review Leave Request</h2>
        </div>

        <div class="p-6">
            <div class="mb-6 flex items-center space-x-3">
                <div class="p-2 rounded-full bg-gray-100 dark:bg-gray-700">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee</p>
                    <p class="font-medium text-gray-700 dark:text-white">{{ $leave->user->name }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leave Type</label>
                    <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">{{ $leave->leaveType->leave_type }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y g:i A') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                        <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y g:i A') }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leave Reason</label>
                    <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">{{ $leave->leave_reason }}</p>
                </div>

                <form method="POST" action="{{ route('leave_manage.update', $leave->id) }}" class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Decision</label>
                        <div class="flex gap-4">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="status" value="approved" required
                                       class="form-radio h-4 w-4 text-green-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Approve</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="status" value="declined" required
                                       class="form-radio h-4 w-4 text-red-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Decline</span>
                            </label>
                        </div>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                        <textarea name="remarks" rows="3" required
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('remarks') border-red-500 @enderror"
                                  placeholder="Enter your remarks">{{ old('remarks') }}</textarea>
                        @error('remarks') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('leave_manage') }}"
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            Submit Decision
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Details</h2>
                <a href="{{ route('leave_manage') }}"
                   class="text-sm text-blue-600 hover:text-blue-800">&larr; Back</a>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <div class="mb-6 flex items-center space-x-3">
                <div class="p-2 rounded-full bg-gray-100 dark:bg-gray-700">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee</p>
                    <p class="font-medium text-gray-700 dark:text-white">{{ $leave->user->name }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leave Type</label>
                <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">{{ $leave->leaveType->leave_type }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                    <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y g:i A') }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                    <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y g:i A') }}
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leave Reason</label>
                <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">{{ $leave->leave_reason }}</p>
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

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                <p class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-900 dark:text-white">{{ $leave->remarks ?? 'No remarks provided' }}</p>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('leave_manage') }}"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    Close
                </a>
            </div>
        </div>
        @endif
    </div>
@endsection
