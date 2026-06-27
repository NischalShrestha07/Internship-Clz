@props([
'leaveTypes' => [],
'title' => null,
'selectedLeaveType' => null,
'showSearch' => true,
'showFilter' => false,
'selectedStatus' => null,
'status' => ['Checked-Out', 'Working'],
'statuses' => [],
'dateFrom' => null,
'dateTo' => null,
'showFilterAttendance' => false,
'search' => null,
'showSearch'=>false,
'selectedOvertime'=>null
])

<div class="w-full">
    @if ($title || $search ||$showSearch|| $showFilter || $showFilterAttendance)
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        @if ($title)
        <h1 class="text-xl font-semibold text-black dark:text-white">{{ $title }}</h1>
        @endif

        @isset($action)
        <div class="ml-auto">
            {{ $action }}
        </div>
        @endisset

        <div class="flex flex-wrap items-center gap-3">

            @if ($search )
            <x-search wire:model.live.debounce.500ms="search" placeholder="Search by Employee Name" />
            @endif

            @if ($showFilter)
            <div class="flex flex-wrap gap-3 items-center">
                <x-filter :leaveTypes="$leaveTypes" :selectedLeaveType="$selectedLeaveType"
                    :selectedStatus="$selectedStatus" :statuses="$statuses" />
            </div>
            @endif



            @if ($showFilterAttendance)
            <div class="flex justify-end">
                <x-attendance-filter :search="$search ?? ''" :dateFrom="$dateFrom" :dateTo="$dateTo" :statuses="$status"
                    :selectedStatus="$selectedStatus" :showSearch="$showSearch" :showOvertimeFilter="true"
                    :selectedOvertime="$selectedOvertime" />
            </div>
            @endif
        </div>
    </div>

    <hr class="border-gray-800 dark:border-gray-600 mb-4">
    @endif

    <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
        <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
            {{ $slot }}
        </table>
    </div>
</div>