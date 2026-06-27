@props([
'search' => '',
'leaveTypes' => [],
'selectedLeaveType' => '',
'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
'selectedStatus' => '',
'quickRange' => '',
])

<div class="flex flex-wrap gap-3 items-center">
    <!-- Leave Type Select -->
    <div class="relative w-48">
        <select wire:model="selectedLeaveType" wire:change="applyFilters"
            class="w-full appearance-none px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md">
            <option value="">All Leave Types</option>
            @foreach ($leaveTypes as $type)
            @php
            // Determine label and ID depending on format (global or configured)
            $typeId = $type['id'] ?? $type->id;
            $typeName = $type['name'] ?? ($type->leave_type ?? 'Unknown');
            @endphp
            <option value="{{ $typeId }}" {{ $selectedLeaveType==$typeId ? 'selected' : '' }}>
                {{ $typeName }}
            </option>
            @endforeach
        </select>
        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">arrow_drop_down</span>
        </span>
    </div>

    <!-- Status Select -->
    <div class="relative w-48">
        <select wire:model="selectedStatus" wire:change="applyFilters"
            class="w-full appearance-none px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md">
            <option value="">All Status</option>
            @foreach ($statuses as $status)
            <option value="{{ $status }}" {{ $selectedStatus===$status ? 'selected' : '' }}>
                {{ ucfirst($status) }}
            </option>
            @endforeach
        </select>
        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">arrow_drop_down</span>
        </span>
    </div>

    <!-- Quick Date Range Selector -->
    <div class="relative w-48">
        <select wire:model="quickRange" wire:change="applyQuickRange"
            class="w-full appearance-none px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-md">
            <option value="">Custom Range</option>
            <option value="today">Today</option>
            <option value="this_week">This Week</option>
            <option value="last_week">Last Week</option>
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
        </select>
        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
            <span class="material-symbols-outlined text-base">arrow_drop_down</span>
        </span>
    </div>

    <div>
        <button wire:click="resetFilters" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
            Reset Filters
        </button>
    </div>

    <div class="flex justify-end">
        <button wire:click="exportCsv"
            class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white text-sm px-2 py-2 rounded shadow transition-colors">
            <span class="material-symbols-outlined text-base">download</span>
        </button>
    </div>
</div>