<div class="main transition-all duration-400">
    <!-- Header Box -->
    <x-headings title="Manage Leave" />
    <x-notifications />
    <div class="px-4 pb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <x-cards :initial="'A'" :bgClass="'from-green-500 to-green-800'">
            <div class="flex items-center space-x-5">
                <div
                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-green-500 to-green-800 rounded-xl flex items-center justify-center text-white shadow-sm">
                    <span class="text-xl font-bold tracking-tight">A</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">Approved Leave</h5>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $approvedCount }}</p>
                        {{-- <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Total Leaves</span>
                        --}}
                    </div>
                </div>
            </div>
        </x-cards>

        <x-cards :initial="'P'" :bgClass="'from-yellow-500 to-yellow-800'">
            <div class="flex items-center space-x-5">
                <div
                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-800 rounded-xl flex items-center justify-center text-white shadow-sm">
                    <span class="text-xl font-bold tracking-tight">P</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">Pending Leave</h5>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingCount }}</p>
                        {{-- <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Total Leaves</span>
                        --}}
                    </div>
                </div>
            </div>
        </x-cards>

        <x-cards :initial="'C'" :bgClass="'from-red-500 to-red-800'">
            <div class="flex items-center space-x-5">
                <div
                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-red-500 to-red-800 rounded-xl flex items-center justify-center text-white shadow-sm">
                    <span class="text-xl font-bold tracking-tight">C</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">Cancelled Leave</h5>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $cancelledCount }}</p>
                        {{-- <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Total Leaves</span>
                        --}}
                    </div>
                </div>
            </div>
        </x-cards>

        <x-cards :initial="'D'" :bgClass="'from-red-500 to-red-800'">
            <div class="flex items-center space-x-5">
                <div
                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-red-500 to-red-800 rounded-xl flex items-center justify-center text-white shadow-sm">
                    <span class="text-xl font-bold tracking-tight">D</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="text-md font-medium text-gray-600 dark:text-gray-300 truncate">Declined Leave</h5>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $declinedCount }}</p>
                        {{-- <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Total Leaves</span>
                        --}}
                    </div>
                </div>
            </div>
        </x-cards>

    </div>


    <div id="lev-tbl2" class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="overflow-x-auto rounded-lg">
            <x-table title="Leave Request" :leaveTypes="$leaveTypes" :selectedLeaveType="$selectedLeaveType"
                :selectedStatus="$selectedStatus" :statuses="$statuses" :showFilter="true" :search="true">
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
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @foreach ($leaves as $leave)
                    <tr wire:click="viewEmployee({{ $leave->user->id }})"
                        class="cursor-pointer transition-opacity duration-150 hover:opacity-80 hover:bg-gray-50 dark:hover:bg-gray-800"">
                            <td class=" px-3 py-2">{{ $leave->user->name }}</td>
                        <td class="px-3 py-2">{{ $leave->leaveType->leave_type }}</td>
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
                        <td class="px-3 py-2">
                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full
            @if ($leave->status === 'approved') bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100
            @elseif($leave->status === 'declined') bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100
            @else bg-yellow-100 text-yellow-500 dark:bg-yellow-600 dark:text-yellow-100 @endif">
                                {{ucfirst($leave->status )}}
                            </span>
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex justify-center gap-1">
                                <button
                                    class="w-6 h-6 flex items-center justify-center  text-blue-500 hover:bg-gray-200  rounded"
                                    wire:click.stop="view({{ $leave->id }})">
                                    <span class="material-symbols-outlined text-xs leading-none">visibility</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </x-table>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <select wire:model.live="userPerPage"
                    class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-white">
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

            <div>
                {{ $leaves->links() }}
            </div>
        </div>
    </div>

    {{-- model --}}
    {{-- <x-modal name="view-leave" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
            <div
                class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[800px] items-center p-6 relative mx-2">
                <button x-on:click.prevent="$dispatch('close')"
                    class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">
                    &times;
                </button>
                <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white">View Leave Request
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Leave Type</label>
                        <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white">
                            {{ $leave_type }}
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white">Start
                                Date</label>
                            <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white">
                                {{ $start_date_time }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white">End Date</label>
                            <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white">
                                {{ $end_date_time }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Leave Reason</label>
                        <p class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white">
                            {{ $leave_reason }}
                        </p>
                    </div>
                    <form class="space-y-4" wire:submit.prevent="submit">
                        @if ($status == 'pending')
                        <hr class="my-4 border-t">
                        <div class="grid grid-cols-2 gap-4">
                            <div x-data="{ status: @entangle('status') }">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2 dark:text-white">Status</label>
                                <div class="flex gap-4">
                                    <label @click="status = 'approved'" class="cursor-pointer border rounded px-5 py-2 text-sm font-semibold transition-all duration-150
                                              text-green-700 border-green-600" :class="{
                                                'bg-green-600 text-white': status === 'approved',
                                                'hover:bg-green-200': status !== 'approved'
                                            }">
                                        <input type="radio" value="approved" class="hidden" x-model="status">
                                        Approve
                                    </label>
                                    <label @click="status = 'declined'" class="cursor-pointer border rounded px-5 py-2 text-sm font-semibold transition-all duration-150
                                            text-red-700 border-red-600" :class="{
                                                'bg-red-600 text-white': status === 'declined',
                                                'hover:bg-red-200': status !== 'declined'
                                            }">
                                        <input type="radio" value="declined" class="hidden" x-model="status">
                                        Decline
                                    </label>
                                </div>
                                @error('status')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-white">Remarks</label>
                            <textarea wire:model.defer="remarks"
                                class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white"
                                focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"
                                placeholder="Add any remarks..."></textarea>
                            @error('remarks')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="button" x-on:click.prevent="$dispatch('close')"
                                class="px-4 py-2 text-gray-600 hover:text-black dark:text-white dark:hover:text-red-500">
                                Close
                            </button>
                            <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Submit Decision
                            </button>
                        </div>
                        @else
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold">Status:</span>
                            @if ($status === 'approved')
                            <span class="bg-green-600 text-white rounded px-4 py-2 text-sm">Approved</span>
                            @elseif($status === 'declined')
                            <span class="bg-red-600 text-white rounded px-4 py-2 text-sm">Declined</span>
                            @else
                            <span class="bg-yellow-300 text-gray-700 rounded px-4 py-2 text-sm">{{ ucfirst($status)
                                }}</span>
                            @endif
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-white">Remarks</label>
                            <p class="border rounded px-3 py-2 text-black dark:text-white">
                                {{ $remarks }}</p>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" x-on:click.prevent="$dispatch('close')"
                                class="px-4 py-2 text-gray-600 hover:text-black dark:text-white dark:hover:text-red-500">
                                Close
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </x-modal> --}}
    <x-modal name="view-leave" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] relative flex flex-col">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Request Details</h2>
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
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="space-y-4">
                        <!-- Leave Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave
                                Type</label>
                            <div
                                class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                {{ $leave_type }}
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                    Date</label>
                                <div
                                    class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $start_date_time }}
                                </div>
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                    Date</label>
                                <div
                                    class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $end_date_time }}
                                </div>
                            </div>
                        </div>

                        <!-- Leave Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 ">Leave
                                Reason</label>
                            <div
                                class="w-full px-4 py-3 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                {{ $leave_reason }}
                            </div>
                        </div>

                        <!-- Status Section -->
                        @if ($status == 'pending')
                        <form wire:submit.prevent="submit" class="space-y-6">
                            <hr class="border-gray-200 dark:border-gray-700 my-3">

                            <!-- Status Selection -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Decision</label>
                                <div class="flex gap-4" x-data="{ status: @entangle('status') }">
                                    <label @click="status = 'approved'" class="flex-1 cursor-pointer border rounded-lg px-3 py-2 text-center transition-colors duration-200
                                        border-green-500 hover:bg-green-50 dark:hover:bg-green-900/30
                                        " :class="{
                                            'bg-green-500 text-white': status === 'approved',
                                            'text-green-600 dark:text-green-400': status !== 'approved'
                                        }">
                                        <input type="radio" value="approved" class="hidden" x-model="status">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Approve
                                        </div>
                                    </label>

                                    <label @click="status = 'declined'" class="flex-1 cursor-pointer border rounded-lg px-3 py-2 text-center transition-colors duration-200
                                        border-red-500 hover:bg-red-50 dark:hover:bg-red-900/30
                                        " :class="{
                                            'bg-red-500 text-white': status === 'declined',
                                            'text-red-600 dark:text-red-400': status !== 'declined'
                                        }">
                                        <input type="radio" value="declined" class="hidden" x-model="status">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Decline
                                        </div>
                                    </label>
                                </div>
                                @error('status')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                                <textarea wire:model.defer="remarks" rows="3" class="w-full px-2 py-1 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        placeholder-gray-400 dark:placeholder-gray-500
                                        {{ $errors->has('remarks') ? 'border-red-500 dark:border-red-500' : '' }}"
                                    placeholder="Add any remarks..."></textarea>
                                @error('remarks')
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
                                    Submit Decision
                                </button>
                            </div>
                        </form>
                        @else
                        <!-- View Only Status -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                @if ($status === 'approved')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approved
                                </span>
                                @elseif($status === 'declined')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Declined
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ ucfirst($status) }}
                                </span>
                                @endif
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                                <div
                                    class="w-full px-4 py-3 border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">
                                    {{ $remarks ?: 'No remarks provided' }}
                                </div>
                            </div>

                            <!-- Footer -->
                            <div
                                class="sticky bottom-0 bg-white dark:bg-gray-800 pt-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                                <button type="button" x-on:click.prevent="$dispatch('close')"
                                    class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                    Close
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-modal>
</div>