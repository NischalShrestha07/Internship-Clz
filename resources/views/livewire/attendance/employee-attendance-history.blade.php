<div>
    <x-headings title="Employee Attendance History" />
    <x-notifications />
    <!-- Attendance Table -->
    <div class="col-span-3 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <!-- Table Scroll Area -->
        <div class="overflow-x-auto rounded-lg">
            <x-table title="Attendance History" :selectedStatus="$selectedStatus" :showFilterAttendance="true"
                :showSearch="true" :dateFrom="$dateFrom" :dateTo="$dateTo">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Employee</th>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Day</th>
                        <th class="px-4 py-3 font-semibold">First Check In</th>
                        <th class="px-4 py-3 font-semibold">Last Check Out</th>
                        <th class="px-4 py-3 font-semibold">Work Hours</th>
                        <th class="px-4 py-3 font-semibold">Break</th>
                        <th class="px-4 py-3 font-semibold">Overtime</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody
                    class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @forelse ($attendanceRecords as $record)
                    <tr x-data="{ showDetails: false }"
                        class="hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <td class="px-4 py-3">{{ $record['employee_name'] }}</td>
                        <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['date'])->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ Carbon\Carbon::parse($record['date'])->format('D') }}</td>
                        <td class="px-4 py-3">
                            {{ $record['check_in'] ? Carbon\Carbon::parse($record['check_in'])->format('h:i A') : '--'
                            }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $record['check_out'] ? Carbon\Carbon::parse($record['check_out'])->format('h:i A') : '--'
                            }}
                        </td>
                        <td class="px-4 py-3">{{ $record['production'] }}</td>
                        <td class="px-4 py-3">{{ $this->formatBreakTime($record['break']) }}</td>
                        <td class="px-4 py-3">{{ number_format($record['overtime'], 2) }} hrs</td>
                        <td class="px-3 py-2">
                            @if ($record['status'] === 'Checked-Out')
                            <span
                                class="inline-block text-[11px] font-medium px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100 whitespace-nowrap">
                                Checked-Out ({{ $record['record_count'] }})
                            </span>
                            @elseif ($record['status'] === 'Working')
                            <span
                                class="inline-block text-[11px] font-medium px-2 py-1 rounded bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-100 whitespace-nowrap">
                                Working ({{ $record['record_count'] }})
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-3">
                                <button wire:click="viewSessions('{{ $record['id'] }}', 'viewSessions')"
                                    class="w-6 h-6 flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded mx-auto">
                                    <span class="material-symbols-outlined text-xs leading-none">visibility</span>
                                </button>
                                <button
                                    class="w-6 h-6 flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded"
                                    wire:click="view({{ $record['id'] }})">
                                    <span class="material-symbols-outlined text-xs leading-none">edit</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="showDetails" x-collapse class="bg-gray-50 dark:bg-gray-600">
                        <td colspan="11" class="px-4 py-3">
                            <div class="pl-8">
                                <h4 class="font-medium text-sm mb-2">Session Details:</h4>
                                <ul class="space-y-1">
                                    @foreach ($record['session_details'] as $session)
                                    <li class="text-sm">
                                        {{ $session['check_in'] }} - {{ $session['check_out'] }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                            No attendance records found for the selected filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
        <div class="mt-3 flex flex-col sm:flex-row items-center justify-between gap-5">
            <!-- Pagination Limit Selector -->
            <div class="flex items-center space-x-2">
                <select wire:model="userPerPage" wire:change="applyFilters"
                    class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-center text-sm px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
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
                {{ $attendanceRecords->links() }}
            </div>
        </div>
    </div>


    {{-- view attendance sessions --}}
    <x-modal name="view-attendance-sessions" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 transition-opacity z-50 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Attendance Sessions - {{ Carbon\Carbon::parse($date)->format('M j, Y') }}
                    </h2>
                    <button x-on:click.prevent="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-hidden flex flex-col">
                    <div class="mb-6">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-medium text-gray-700 dark:text-gray-300">Name:- {{ $attendanceName
                                }}</span>
                        </div>
                    </div>

                    <!-- Sessions List -->
                    <div class="flex-1 overflow-y-auto">
                        <div class="overflow-hidden shadow rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Check In</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Check Out</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Work Hours</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Break Time</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @forelse($attendanceSessions as $index => $session)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $session['check_in'] }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $session['check_out'] }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $session['work_hours'] ?? '--' }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $session['break'] }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $session['status'] === 'Working' ?
                                                   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100' :
                                                   'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' }}">
                                                {{ $session['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No attendance sessions found for this date.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-modal>

    {{-- update attendace --}}
    <x-modal name="view-attendance" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-opacity-75 transition-opacity z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all w-full max-w-2xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit Attendance Record
                    </h2>
                    <button x-on:click.prevent="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <div class="mb-6 flex items-center space-x-3">
                        <div class="p-2 rounded-full bg-gray-100 dark:bg-gray-700">
                            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Employee</p>
                            <p class="font-medium text-gray-700 dark:text-white">{{ $attendanceName }}</p>
                        </div>
                    </div>


                    <form wire:submit.prevent="submit" wire:key="attendance-form-{{ $attendanceId }}" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Check In Field - Only visible to admins -->
                            @if(auth()->user()->hasRole('HR Manager'))
                            <div>
                                <label for="check_in"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Check In
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="datetime-local" id="check_in" wire:model.defer="check_in"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white p-2.5">
                                </div>
                                @error('check_in')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check Out Field - Only visible to admins -->
                            <div>
                                <label for="check_out"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Check Out
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="datetime-local" id="check_out" wire:model.defer="check_out"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white p-2.5">
                                </div>
                                @error('check_out')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @else
                            <!-- Display read-only check-in/check-out times for HR managers -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Check In
                                    </label>
                                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                                        {{ $check_in ? Carbon\Carbon::parse($check_in)->format('M j, Y h:i A') : '--' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Check Out
                                    </label>
                                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                                        {{ $check_out ? Carbon\Carbon::parse($check_out)->format('M j, Y h:i A') : '--'
                                        }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Break Time Field -->
                            <div>
                                <label for="break"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Break Time (minutes)
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" id="break" wire:model.defer="break" min="0"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white p-2.5">
                                </div>
                            </div>

                            <!-- Overtime Field -->
                            <div>
                                <label for="overtime"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Overtime (hours)
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" id="overtime" wire:model.defer="overtime" min="0" step="0.1"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white p-2.5">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" x-on:click.prevent="$dispatch('close')"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>
</div>
<script>
    document.addEventListener('livewire:init', () => {
        initCalendar();
    });

    document.addEventListener('livewire:navigated', () => {
        setTimeout(() => {
            initCalendar();
        }, 100);
    });

    document.addEventListener('livewire:update', () => {
        if (!document.querySelector("#calendarRangePicker")?._flatpickr) {
            setTimeout(() => {
                initCalendar();
            }, 100);
        }
    });

    function initCalendar() {
        const calendarElement = document.querySelector("#calendarRangePicker");

        if (!calendarElement) {
            setTimeout(initCalendar, 100);
            return;
        }

        if (calendarElement._flatpickr) {
            calendarElement._flatpickr.destroy();
        }

        const calendar = flatpickr(calendarElement, {
            mode: "range",
            dateFormat: "Y-m-d",
            @if($dateFrom && $dateTo)
                defaultDate: ["{{ $dateFrom }}", "{{ $dateTo }}"],
            @endif
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const start = selectedDates[0].toISOString().split('T')[0];
                    const end = selectedDates[1].toISOString().split('T')[0];

                    @this.set('dateFrom', start);
                    @this.set('dateTo', end);
                    @this.call('applyFilters');
                }
            }
        });

        calendarElement._flatpickr = calendar;
    }

    Livewire.on('resetCalendar', () => {
        const calendarElement = document.querySelector("#calendarRangePicker");
        if (calendarElement && calendarElement._flatpickr) {
            calendarElement._flatpickr.clear();
        }
    });

    Livewire.on('updateCalendar', (dates) => {
        const calendarElement = document.querySelector("#calendarRangePicker");
        if (calendarElement && calendarElement._flatpickr && dates.dateFrom && dates.dateTo) {
            calendarElement._flatpickr.setDate([dates.dateFrom, dates.dateTo]);
        }
    });
</script>
</div>