@extends('components.layouts.app')

@section('content')
    <x-headings title="Manage Leave" />

    <x-notifications />

    <div class="px-4 pb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
        $cards = [
            ['initial' => 'A', 'bg' => 'from-green-500 to-green-800', 'label' => 'Approved Leave', 'count' => $approvedCount],
            ['initial' => 'P', 'bg' => 'from-yellow-500 to-yellow-800', 'label' => 'Pending Leave', 'count' => $pendingCount],
            ['initial' => 'C', 'bg' => 'from-red-500 to-red-800', 'label' => 'Cancelled Leave', 'count' => $cancelledCount],
            ['initial' => 'D', 'bg' => 'from-red-500 to-red-800', 'label' => 'Declined Leave', 'count' => $declinedCount],
        ];
        @endphp

        @foreach ($cards as $card)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center space-x-5">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br {{ $card['bg'] }} rounded-xl flex items-center justify-center text-white shadow-sm">
                    <span class="text-xl font-bold tracking-tight">{{ $card['initial'] }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">{{ $card['label'] }}</h5>
                    <div class="mt-1">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['count'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-wrap items-center justify-between mb-4 gap-3">
            <h1 class="text-xl font-semibold text-black dark:text-white">Leave Request</h1>

            <form method="GET" action="{{ route('leave_manage') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" placeholder="Search by Employee Name"
                       value="{{ request('search') }}"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">

                <select name="leave_type"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                    <option value="">All Types</option>
                    @foreach ($leaveTypes as $lt)
                    <option value="{{ $lt->id }}" {{ request('leave_type') == $lt->id ? 'selected' : '' }}>{{ $lt->leave_type }}</option>
                    @endforeach
                </select>

                <select name="status"
                        class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
                    <option value="">All Status</option>
                    @foreach ($statuses as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">

                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">

                <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700">Filter</button>

                <a href="{{ route('leave_manage') }}" class="bg-gray-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-gray-600">Reset</a>

                <a href="{{ route('leave_manage.csv', request()->query()) }}"
                   class="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-green-700">CSV</a>
            </form>
        </div>

        <hr class="border-gray-800 dark:border-gray-600 mb-4">

        <div class="overflow-x-auto rounded-lg">
            <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
                <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
                    <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2 font-semibold">Employee Name</th>
                            <th class="px-3 py-2 font-semibold">Leave Type</th>
                            <th class="px-3 py-2 font-semibold">Start Date</th>
                            <th class="px-3 py-2 font-semibold">End Date</th>
                            <th class="px-3 py-2 font-semibold">Status</th>
                            <th class="px-3 py-2 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                        @foreach ($leaves as $leave)
                        <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600"
                            onclick="window.location='{{ route('employee.show', $leave->user->id) }}'">
                            <td class="px-3 py-2">{{ $leave->user->name }}</td>
                            <td class="px-3 py-2">{{ $leave->leaveType->leave_type }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y') }}<br>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y') }}<br>
                                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->end_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full
                                    @if ($leave->status === 'approved') bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100
                                    @elseif($leave->status === 'declined') bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100
                                    @else bg-yellow-100 text-yellow-500 dark:bg-yellow-600 dark:text-yellow-100 @endif">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <a href="{{ route('leave_manage.show', $leave->id) }}"
                                   class="w-6 h-6 inline-flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded"
                                   onclick="event.stopPropagation()">
                                    <span class="material-symbols-outlined text-xs leading-none">visibility</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-5">
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('leave_manage') }}" id="perPageForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="leave_type" value="{{ request('leave_type') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <select name="per_page" onchange="this.form.submit()"
                            class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm px-3 py-1.5">
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
