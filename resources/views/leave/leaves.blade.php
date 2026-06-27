@extends('components.layouts.app')

@section('content')
    <x-headings title="Leave Details" />

    <x-notifications />

    <div class="flex justify-end mb-4">
        <button onclick="document.getElementById('createLeaveForm').classList.toggle('hidden')"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            + Apply Leave
        </button>
    </div>

    <div id="createLeaveForm" class="hidden bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6 border dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Apply Leave</h3>
        <form method="POST" action="{{ route('leave.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave Type</label>
                <select name="leave_type" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('leave_type') border-red-500 @enderror">
                    <option value="">Select Leave Type</option>
                    @foreach ($leaveTypesList as $type)
                    <option value="{{ $type->leaveType->id }}" {{ old('leave_type') == $type->leaveType->id ? 'selected' : '' }}>
                        {{ $type->leaveType->leave_type }} ({{ $type->days }} days allowed)
                    </option>
                    @endforeach
                </select>
                @error('leave_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="datetime-local" name="start_date_time" value="{{ old('start_date_time') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('start_date_time') border-red-500 @enderror">
                    @error('start_date_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="datetime-local" name="end_date_time" value="{{ old('end_date_time') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('end_date_time') border-red-500 @enderror">
                    @error('end_date_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave Reason</label>
                <textarea name="leave_reason" rows="3" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('leave_reason') border-red-500 @enderror"
                          placeholder="Enter your reason for leave">{{ old('leave_reason') }}</textarea>
                @error('leave_reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createLeaveForm').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Apply Leave
                </button>
            </div>
        </form>
    </div>

    @if ($leaves_card && $leaves_card->count())
    <div class="px-4 pb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($leaves_card->sortBy(fn($leave) => strtoupper($leave->leaveType->leave_type[0])) as $leave)
        @php
        $initial = strtoupper($leave->leaveType->leave_type[0]);
        $bgClass = match ($initial) {
            'S' => 'from-yellow-500 to-yellow-800',
            'A' => 'from-blue-500 to-blue-800',
            'C' => 'from-green-500 to-green-800',
            default => 'from-blue-500 to-blue-800',
        };
        $whole = floor($leave->remaining);
        $decimal = $leave->remaining - $whole;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center space-x-5">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br {{ $bgClass }} rounded-xl flex items-center justify-center text-white shadow-sm">
                    <span class="text-xl font-bold tracking-tight">{{ $initial }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">
                        {{ $leave->leaveType->leave_type }}
                    </h5>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $whole . ' day' . ($whole != 1 ? 's' : '') }}
                            {{ $decimal == 0.5 ? ' and half' : '' }}
                        </p>
                        <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Remaining</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-10 text-gray-500 dark:text-gray-300">
        <span class="text-lg font-medium">No leaves assigned</span>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4 mt-4">
        <div class="flex flex-wrap items-center justify-between mb-4 gap-3">
            <h1 class="text-xl font-semibold text-black dark:text-white">Leave History</h1>

            <form method="GET" action="{{ route('leave') }}" class="flex flex-wrap items-center gap-3">
                <select name="leave_type"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                    <option value="">All Types</option>
                    @foreach ($leaveTypes as $lt)
                    <option value="{{ $lt['id'] }}" {{ request('leave_type') == $lt['id'] ? 'selected' : '' }}>
                        {{ $lt['name'] }}
                    </option>
                    @endforeach
                </select>

                <select name="status"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                    <option value="">All Status</option>
                    @foreach (['approved', 'pending', 'declined', 'cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">

                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">

                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700">Filter</button>

                <a href="{{ route('leave') }}" class="bg-gray-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-gray-600">Reset</a>

                <a href="{{ route('leave.csv', request()->query()) }}"
                   class="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-green-700">CSV</a>
            </form>
        </div>

        <hr class="border-gray-800 dark:border-gray-600 mb-4">

        <div class="overflow-x-auto rounded-lg">
            <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
                <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
                    <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Leave Type</th>
                            <th class="px-4 py-3 font-semibold">Start Date Time</th>
                            <th class="px-4 py-3 font-semibold">End Date Time</th>
                            <th class="px-4 py-3 font-semibold">Leave Reason</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                        @foreach ($leaves as $leave)
                        <tr>
                            <td class="px-4 py-3">{{ $leave->leaveType->leave_type }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y') }}<br>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y') }}<br>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->end_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-[100px] truncate" title="{{ $leave->leave_reason }}">
                                {{ $leave->leave_reason }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block text-xs font-medium px-2 py-1 rounded-full
                                    @if ($leave->status === 'approved') bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100
                                    @elseif ($leave->status === 'declined') bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100
                                    @elseif ($leave->status === 'pending') bg-yellow-100 text-yellow-500 dark:bg-yellow-600 dark:text-yellow-100
                                    @else bg-red-200 text-gray-700 dark:bg-red-500 dark:text-gray-100 @endif">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if (in_array($leave->status, ['cancelled', 'declined', 'approved']))
                                <a href="{{ route('leave.show', $leave->id) }}"
                                   class="w-6 h-6 inline-flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded">
                                    <span class="material-symbols-outlined text-[10px] leading-none">visibility</span>
                                </a>
                                @else
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('leave.show', $leave->id) }}"
                                       class="w-6 h-6 inline-flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded">
                                        <span class="material-symbols-outlined text-[10px] leading-none">visibility</span>
                                    </a>
                                    <form method="POST" action="{{ route('leave.cancel', $leave->id) }}"
                                          onsubmit="return confirm('Are you sure you want to cancel this leave request?')" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-6 h-6 inline-flex items-center justify-center text-red-500 hover:bg-gray-200 rounded">
                                            <span class="material-symbols-outlined text-[10px] leading-none">cancel</span>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('leave') }}" id="perPageForm">
                    <input type="hidden" name="leave_type" value="{{ request('leave_type') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <select name="per_page" onchange="this.form.submit()"
                            class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-center text-sm px-3 py-1.5">
                        <option value="5" {{ request('per_page', $defaultPerPage) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', $defaultPerPage) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', $defaultPerPage) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page', $defaultPerPage) == 20 ? 'selected' : '' }}>20</option>
                        <option value="25" {{ request('per_page', $defaultPerPage) == 25 ? 'selected' : '' }}>25</option>
                    </select>
                </form>
            </div>
            <div class="flex justify-center sm:justify-end">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>
@endsection
