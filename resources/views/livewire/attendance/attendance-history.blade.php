<div>
    <x-headings title="Attendance" />
    <x-notifications />
    <!-- Attendance Table -->
    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="overflow-x-auto rounded-lg">
            <x-table title="Attendance History" :selectedStatus="$selectedStatus" :months="$months" :selectedMonth="$selectedMonth" :years="$years"
                :selectedYear="$selectedYear" :showFilterAttendance="true">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Day</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Check In</th>
                        <th class="px-4 py-3 font-semibold">Check Out</th>
                        <th class="px-4 py-3 font-semibold">Work Hours</th>
                        <th class="px-4 py-3 font-semibold">Break</th>
                        <th class="px-4 py-3 font-semibold">Overtime</th>
                        @if (Auth::user()->hasRole('HR Manager'))
                            <th class="px-4 py-3 font-semibold">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @forelse ($attendanceRecords as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['created_at'])->format('M j, Y') }}
                            </td>
                            <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['created_at'])->format('D') }}</td>
                            <td class="px-4 py-3">
                                @if ($record['status'] === 'Checked-Out')
                                    <span
                                        class="inline-block text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-100">
                                        Checked-Out
                                    </span>
                                @elseif ($record['status'] === 'Working')
                                    <span
                                        class="inline-block text-xs font-medium px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-100">
                                        Working
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $record['check_in'] ? Carbon\Carbon::parse($record['check_in'])->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $record['check_out'] ? Carbon\Carbon::parse($record['check_out'])->format('h:i A') : '--' }}
                            </td>
                            <td class="px-4 py-3">{{ number_format($record['production'], 2) }} hrs</td>
                            <td class="px-4 py-3">{{ $record['break'] }} mins</td>
                            <td class="px-4 py-3">{{ number_format($record['overtime'], 2) }} hrs</td>

                            @if (Auth::user()->hasRole('HR Manager'))
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-1">
                                        <button
                                            class="w-6 h-6 flex items-center justify-center  text-blue-500 hover:bg-gray-200  rounded"
                                            wire:click="view({{ $record['id'] }})">
                                            <span class="material-symbols-outlined text-xs leading-none">edit</span>
                                        </button>
                                    </div>
                                </td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->hasRole('HR Manager') ? 9 : 8 }}"
                                class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                No attendance records found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
        <div class="mt-4">
            {{ $attendanceRecords->links() }}
        </div>
    </div>


    <x-modal name="view-attendance" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">

            <div class="fixed inset-0   bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-md p-6 relative mx-2">
                    <button x-on:click.prevent="$dispatch('close')"
                        class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">
                        &times;
                    </button>

                    <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white">
                        Edit Attendance Record
                    </h2>

                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-white">Employee: {{ $attendanceName }}
                        </p>
                    </div>

                    <form wire:submit.prevent="submit" wire:key="attendance-form-{{ $attendanceId }}">
                        <div class="space-y-4">
                            <div>
                                <label for="check_in" class="block text-sm font-medium text-gray-700 dark:text-white">
                                    Check In
                                </label>
                                <input type="datetime-local" id="check_in" wire:model.defer="check_in"
                                    class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 date-input">
                                @error('check_in')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="check_out" class="block text-sm font-medium text-gray-700 dark:text-white">
                                    Check Out
                                </label>
                                <input type="datetime-local" id="check_out" wire:model.defer="check_out"
                                    class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 date-input">
                                @error('check_out')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="break" class="block text-sm font-medium text-gray-700 dark:text-white">
                                    Break Time (minutes)
                                </label>
                                <input type="number" id="break" wire:model.defer="break" min="0"
                                    class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('break')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="overtime" class="block text-sm font-medium text-gray-700 dark:text-white">
                                    Overtime
                                </label>
                                <input type="number" id="overtime" wire:model.defer="overtime" min="0"
                                    class="w-full mt-1 border rounded px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('overtime')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end space-x-4 mt-6">
                                <div class="flex justify-end mt-4">
                                    <button type="button" x-on:click.prevent="$dispatch('close')"
                                        class="px-4 py-2 text-gray-600 hover:text-black dark:text-white dark:hover:text-red-500">
                                        Cancel
                                    </button>
                                </div>
                                <button type="submit"
                                    class="bg-blue-800 text-white px-4 py-1 rounded hover:bg-blue-700">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>
</div>
</div>
