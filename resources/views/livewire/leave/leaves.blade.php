<div class="main transition-all duration-400">
    <!-- Header Box -->
    <x-headings title="Leave Details" buttonModal="create-leave" buttonText="+ Apply Leave" />

    <x-notifications />

    <div class="px-4 pb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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

    <!-- Table 1 -->

    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="overflow-x-auto rounded-lg">
            <x-table title="Leave History" :leaveTypes="$leaveTypes" :selectedLeaveType="$selectedLeaveType"
                :selectedStatus="$selectedStatus" :statuses="$statuses" :showFilter="true">

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
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @foreach ($leaves as $leave)
                    <tr>
                        <td class="px-4 py-3">{{ $leave->leaveType->leave_type }}</td>
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y') }}<br>
                            <span class="text-sm text-gray-500">{{
                                \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y') }}<br>
                            <span class="text-sm text-gray-500">{{
                                \Carbon\Carbon::parse($leave->end_date_time)->format('g:i A') }}</span>
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
                            <div class="flex justify-center">
                                <button wire:click="view({{ $leave->id }})"
                                    class="w-6 h-6 flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded">
                                    <span class="material-symbols-outlined text-[10px] leading-none">visibility</span>
                                </button>
                            </div>
                            @else
                            <div class="flex justify-center gap-2">
                                <button wire:click="promptCancelLeave({{ $leave->id }})"
                                    class="w-6 h-6 flex items-center justify-center text-red-500 hover:bg-gray-200 rounded">
                                    <span class="material-symbols-outlined text-[10px] leading-none">cancel</span>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </x-table>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <!-- Pagination Limit Selector -->
            <div class="flex items-center space-x-2">
                <select wire:model="userPerPage" wire:change="applyFilters"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-center text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                    <option value="{{ $defaultPerPage }}">Default ({{ $defaultPerPage }})</option>
                </select>
                @if($userPerPage != $defaultPerPage)
                <button wire:click="resetToDefaultPerPage"
                    class="bg-blue-600 dark:bg-blue-800 text- ml-3 px-3 py-1 rounded hover:bg-blue-700">
                    Reset </button>
                @endif
            </div>

            <!-- Pagination Links -->
            <div class="flex justify-center sm:justify-end">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>

    <!-- modals -->
    <x-modal name="create-leave" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] relative flex flex-col">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Apply Leave</h2>
                        <button x-on:click.prevent="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <form wire:submit.prevent="submit" class="space-y-6">
                        <!-- Leave Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave
                                Type</label>
                            <select wire:model="leave_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    bg-white dark:bg-gray-700
                                    border-gray-300 dark:border-gray-600
                                    text-gray-900 dark:text-white
                                    {{ $errors->has('leave_type') ? 'border-red-500 dark:border-red-500' : '' }}"
                                required>
                                <option value="">Select Leave Type</option>
                                @foreach ($leaveTypesList as $type)
                                <option value="{{ $type->leaveType->id }}">
                                    {{ $type->leaveType->leave_type }} ({{ $type->days }} days allowed)
                                </option>
                                @endforeach
                            </select>
                            @error('leave_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                    Date</label>
                                <input type="datetime-local" wire:model="start_date_time"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        {{ $errors->has('start_date_time') ? 'border-red-500 dark:border-red-500' : '' }}" required>
                                @error('start_date_time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                    Date</label>
                                <input type="datetime-local" wire:model="end_date_time"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        {{ $errors->has('end_date_time') ? 'border-red-500 dark:border-red-500' : '' }}" required>
                                @error('end_date_time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Leave Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave
                                Reason</label>
                            <textarea wire:model="leave_reason" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    bg-white dark:bg-gray-700
                                    border-gray-300 dark:border-gray-600
                                    text-gray-900 dark:text-white
                                    placeholder-gray-400 dark:placeholder-gray-500
                                    {{ $errors->has('leave_reason') ? 'border-red-500 dark:border-red-500' : '' }}"
                                placeholder="Enter your reason for leave" required></textarea>
                            @error('leave_reason')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Footer -->
                        <div
                            class="sticky bottom-0 bg-white dark:bg-gray-800 pt-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end gap-3">
                            <button type="button" x-on:click.prevent="$dispatch('close')"
                                class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 dark:bg-blue-700 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200">
                                Apply Leave
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>


    <x-modal name="cancel-leave" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0  bg-opacity-40 flex items-center justify-center z-50">

            <div
                class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
                <!-- Close Button -->
                <button x-on:click.prevent="$dispatch('close')"
                    class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">
                    &times;
                </button>

                <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white ">Cancel Leave</h2>

                <p class="text-center text-gray-700 dark:text-white  text-lg mb-6">
                    Are you sure you want to cancel this leave request?
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="confirmCancelLeave"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600">
                        Yes, Cancel
                    </button>

                    <button type="button" x-on:click.prevent="$dispatch('close')"
                        class="px-4 py-2 text-gray-600 dark:text-gray-300  hover:text-black dark:hover:text-white ">
                        No, Go Back
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <x-modal name="view-leave" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[800px] items-center p-6 relative mx-2">
                <button x-on:click.prevent="$dispatch('close')"
                    class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">
                    &times;
                </button>

                <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white">View Leave </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Leave Type</label>
                        <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white ">
                            {{ $leave_type }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white ">Start
                                Date</label>
                            <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white ">
                                {{ $start_date_time }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white ">End
                                Date</label>
                            <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white ">
                                {{ $end_date_time }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white ">Leave
                            Reason</label>
                        <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white ">
                            {{ $leave_reason }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-semibold">Status:</span>

                        @if ($status === 'approved')
                        <span class="bg-green-600 text-white rounded px-4 py-2 text-sm">Approved</span>
                        @elseif($status === 'declined')
                        <span class="bg-red-600 text-white rounded px-4 py-2 text-sm">Declined</span>
                        @else
                        <span class="bg-gray-300 text-gray-700 rounded px-4 py-2 text-sm">{{ ucfirst($status) }}</span>
                        @endif
                    </div>
                    @if (!($status == 'cancelled'))
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                        <p
                            class=" border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-black dark:text-white ">
                            {{ $remarks }}
                        </p>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </x-modal>
</div>