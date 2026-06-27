@props([
    'showCustom' => false,
    'employeeName' => '',
    'selectedRange' => '',
    'selectedDepartment' => '',
    'ranges' => ['Last Day', 'Last 7 days', 'Last 30 days', 'Last Month', 'Last year', 'Custom'],
    'departments' => ['HR', 'Employee'],
])

<div x-data="{ showCustom: @json($showCustom) }" class="flex flex-col gap-3 pb-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-3 items-center">
            <!-- Employee Name -->
            <input type="text" name="employee_name" value="{{ $employeeName }}"
                placeholder="Employee Name"
                class="px-3 py-2 text-sm border border-gray-900 rounded w-48 text-black" />

            <!-- Range Dropdown -->
            <div class="relative w-40">
                <select name="range"
                    @change="showCustom = $event.target.value === 'Custom'"
                    class="w-full appearance-none px-3 py-2 text-sm border text-black border-gray-900 rounded">
                    @foreach ($ranges as $range)
                        <option value="{{ $range }}" {{ $selectedRange === $range ? 'selected' : '' }}>
                            {{ $range }}
                        </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
                    <span class="material-symbols-outlined text-base">arrow_drop_down</span>
                </span>
            </div>

            <!-- Department Dropdown -->
            <div class="relative w-40">
                <select name="department"
                    class="w-full appearance-none px-3 py-2 text-sm border text-black border-gray-900 rounded">
                    <option value="">Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept }}" {{ $selectedDepartment === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-500">
                    <span class="material-symbols-outlined text-base">arrow_drop_down</span>
                </span>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end items-center gap-4">
            <button type="reset" class="text-sm text-black font-medium hover:underline">
                Clear Filters
            </button>
            <button type="submit"
                class="bg-blue-500 text-white text-sm font-semibold px-4 py-2 rounded hover:bg-blue-700">
                Apply
            </button>
        </div>
    </div>

    <!-- Custom Date Range Fields -->
    <div x-show="showCustom" x-transition class="flex gap-4 w-full max-w-md ml-32">
        <div class="w-1/2">
            <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
            <input type="date" name="from_date"
                class="w-full px-3 py-2 border border-gray-400 rounded text-sm text-black" />
        </div>
        <div class="w-1/2">
            <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
            <input type="date" name="to_date"
                class="w-full px-3 py-2 border border-gray-400 rounded text-sm text-black" />
        </div>
    </div>
</div>
