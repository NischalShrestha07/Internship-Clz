@props([
'search' => null,
'showSearch'=>false,
'statuses' => ['Checked-Out', 'Working'],
'selectedStatus' => null,
'dateFrom' => null,
'dateTo' => null,
'quickRange'=>null,
'showOvertimeFilter' => false,
'selectedOvertime' => null,
])
<div class="flex flex-wrap gap-2 items-center">
    <!-- Search Input -->
    @if($showSearch)
    <div class="relative w-40">
        <input wire:model.live.debounce.300ms="search" id="search" type="text"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md"
            placeholder="Search employee...">
        <span class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">search</span>
        </span>
    </div>
    @endif

    <div class="relative w-50" wire:ignore>

        <input type="text" id="calendarRangePicker"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md"
            placeholder="Select Date Range" readonly>
    </div>

    <!-- Status Select -->
    <div class="relative w-33">
        <select wire:model="selectedStatus" wire:change="applyFilters"
            class="w-full appearance-none px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md">
            <option value="">Status </option>
            @foreach ($statuses as $status)
            <option value="{{ $status }}" {{ $selectedStatus===$status ? 'selected' : '' }}>{{ $status }}</option>
            @endforeach
        </select>
        <span class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">arrow_drop_down</span>
        </span>
    </div>

    <!-- Quick Date Range Selector -->
    <div class="relative w-33">
        <select wire:model="quickRange" wire:change="applyQuickRange"
            class="w-full appearance-none px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md">
            <option value="">Custom Range</option>
            <option value="today">Today</option>
            <option value="this_week">This Week</option>
            <option value="last_week">Last Week</option>
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
        </select>
        <span class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">arrow_drop_down</span>
        </span>
    </div>

    @if($showOvertimeFilter)
    <div class="relative w-38">
        <select wire:model="selectedOvertime" wire:change="applyFilters"
            class="w-full appearance-none px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md">
            <option value="">All Overtime</option>
            <option value="with_overtime" {{ $selectedOvertime==='with_overtime' ? 'selected' : '' }}>With Overtime
            </option>
            <option value="without_overtime" {{ $selectedOvertime==='without_overtime' ? 'selected' : '' }}>Without
                Overtime
            </option>
        </select>
        <span class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">arrow_drop_down</span>
        </span>
    </div>
    @endif

    <div>
        <button wire:click="resetFilters" class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
            Reset Filters
        </button>
    </div>
    <div class="flex justify-end">
        <button wire:click="downloadCSV"
            class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white text-sm px-2 py-2 rounded shadow transition-colors">
            <span class="material-symbols-outlined text-base">download</span>
        </button>
    </div>
</div>