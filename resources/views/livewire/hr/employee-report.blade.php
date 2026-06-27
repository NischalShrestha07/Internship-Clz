<div class="main transition-all duration-400">
    <!-- Header Box -->
    <x-headings title="Leave Details" />

    <x-notifications />
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
        <div
            class="mb-6 p-6 border border-gray-200 dark:border-gray-700 rounded-lg d-flex justify-between shadow-by-force ">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white ">
                    Leaves Remaining
                </h3>
                <button wire:click="back"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white px-2 py-1 rounded-md">
                    Back
                </button>
            </div>
            <hr class="border-t border-gray-800 dark:border-gray-600 mb-5">
            <div class="px-4 pb-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @if ($leaves_card && $leaves_card->count())
                    @foreach ($leaves_card->sortBy(fn($leave) => strtoupper($leave->LeaveType->leave_type[0])) as $leave)
                        @php
                            $initial = strtoupper($leave->LeaveType->leave_type[0]);
                            $bgClass = match ($initial) {
                                'S' => 'from-yellow-500 to-yellow-800',
                                'A' => 'from-blue-500 to-blue-800',
                                'C' => 'from-green-500 to-green-800',
                                default => 'from-blue-500 to-blue-800',
                            };
                        @endphp

                        <x-cards :initial="$initial" :bgClass="$bgClass">
                            <div class="flex items-center space-x-5">
                                <div
                                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br {{ $bgClass }} rounded-xl flex items-center justify-center text-white shadow-sm">
                                    <span class="text-xl font-bold tracking-tight">
                                        {{ $initial }}
                                    </span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">
                                        {{ $leave->LeaveType->leave_type }}
                                    </h5>
                                    <div class="mt-1 flex items-baseline">
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        <div>
                                            @php
                                                $whole = floor($leave->remaining);
                                                $decimal = $leave->remaining - $whole;
                                            @endphp
                                            <b>
                                                {{ $whole . ' day' . ($whole != 1 ? 's' : '') }}
                                                {{ $decimal == 0.5 ? ' and half' : '' }}
                                            </b>
                                        </div>
                                        </p>
                                        <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Remaining
                                        </span>
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

        <!-- Table 1 -->
        <div id="lev-tbl1" class="col-span-3 bg-white dark:bg-gray-800 shadow-by-force rounded-lg p-4 overflow-auto">
            <x-table title="Leave History" :leaveTypes="$leaveTypes" :selectedLeaveType="$selectedLeaveType" :selectedStatus="$selectedStatus" :statuses="$statuses"
                :showFilter="true">
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
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @foreach ($leaves as $leave)
                        <tr>
                            <td class="px-4 py-3">{{ $leave->user->name }}</td>
                            <td class="px-4 py-3">{{ $leave->leaveType->leave_type }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y') }}<br>
                                <span
                                    class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y') }}<br>
                                <span
                                    class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($leave->end_date_time)->format('g:i
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                A') }}</span>
                            </td>
                            <td class="px-4 py-3 max-w-[100px] truncate" title="{{ $leave->leave_reason }}">
                                {{ $leave->leave_reason }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block text-xs font-medium px-2 py-1 rounded-full
                                @if ($leave->status === 'approved') bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100
                                @elseif ($leave->status === 'declined') bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100
                                @elseif ($leave->status === 'pending') bg-yellow-100 text-yellow-500 dark:bg-yellow-600 dark:text-yellow-100
                                @else bg-red-200 text-gray-700 dark:bg-red-500 dark:text-gray-100 @endif">
                                    {{ $leave->status }}
                                </span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            {{-- @livewire('pagination') --}}
            <div class="mt-4">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>
</div>
</div>
