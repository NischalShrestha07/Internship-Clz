@php
use Carbon\Carbon;
$today = Carbon::today('Asia/Kathmandu');
@endphp

<div class="main transition-all duration-400">
    <x-headings title="Calendar" buttonModal="openAddEventModal" buttonText="+ Add Event" />
    <x-notifications />

    <!-- Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 mt-6 gap-4">
        <!-- Navigation Buttons -->
        <div class="flex items-center gap-1 bg-gray-400 dark:bg-gray-700 rounded-lg overflow-hidden">
            <button wire:click="previousMonth"
                class="px-2 py-1 hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 rounded-l-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-sm dark:text-gray-200">chevron_left</span>
            </button>
            <button wire:click="today"
                class="px-4 py-1.5 bg-blue-700 hover:bg-blue-600 dark:bg-blue-800 dark:hover:bg-blue-700 text-white rounded text-sm transition duration-150">
                Today
            </button>
            <button wire:click="nextMonth"
                class="px-2 py-1 hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 rounded-r-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-sm dark:text-gray-200">chevron_right</span>
            </button>
        </div>

        <!-- Month and Year Display -->
        <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
            {{ $monthName }} {{ $year }}
        </h3>

        <!-- View Switch Buttons -->
        <div class="flex gap-2">
            @foreach (['month', 'week', 'day'] as $option)
            <button wire:click="setView('{{ $option }}')"
                class="px-3 py-1.5 rounded-lg text-sm transition duration-150
                    {{ $view === $option ? 'bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white' : 'bg-gray-400 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                {{ ucfirst($option) }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- MONTH VIEW -->
    @if ($view === 'month')
    <div class="grid grid-cols-7 gap-px text-center bg-gray-200 dark:bg-gray-700 rounded-lg shadow p-px text-sm">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
        <div class="font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2
                {{ $day === 'Sat' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300' : '' }}">
            {{ $day }}
        </div>
        @endforeach

        @for ($i = 0; $i < $startDay; $i++) <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded">
    </div>
    @endfor

    @php
    $currentDate = Carbon::create($year, $month, 1);
    @endphp

    @for ($day = 1; $day <= $daysInMonth; $day++) @php $currentDate=Carbon::create($year, $month, $day);
        $dateStr=$currentDate->format('Y-m-d');
        $event = $events[$dateStr] ?? null;
        $isToday = $currentDate->isSameDay($today);

        // Check if any event on this date is a holiday
        $isHoliday = false;
        if ($event) {
        foreach ($event as $e) {
        if ($e['type'] === 'Holiday') {
        $isHoliday = true;
        break;
        }
        }
        }
        @endphp

        <div
            class="p-2 bg-white dark:bg-gray-800 relative h-24 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition duration-150
            {{ $isHoliday ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700' : 'hover:border border-gray-200 dark:border-gray-600' }}">

            <button wire:click="openAddEventModal({{ $day }})" class="w-full text-left rounded p-1
                {{ $isToday ? 'bg-blue-50 dark:bg-blue-800' : '' }}
                hover:bg-gray-100 dark:hover:bg-gray-600
                {{ $isHoliday ? 'text-red-600 dark:text-red-300' : 'text-gray-800 dark:text-gray-200' }}">
                <span class="font-medium text-sm">{{ $day }}</span>
            </button>

            @if ($event)
            <div x-data="{ expanded: false }" @mouseenter="expanded = true" @mouseleave="expanded = false"
                class="absolute bottom-1 right-19">
                <div class="relative">
                    <span class="flex items-center justify-center w-6 h-6 text-sm font-semibold rounded-full
                        {{ $isHoliday ? 'bg-red-500' : 'bg-blue-500' }} text-white cursor-pointer">
                        {{ count($event) }}
                    </span>

                    <div x-show="expanded" x-transition class="absolute z-20 top-full mt-2 left-1/2 transform -translate-x-1/2
     w-52 bg-white dark:bg-gray-800
     shadow-xl rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                        <div class="text-sm font-semibold mb-2 text-gray-800 dark:text-gray-200">
                            Events on {{ $day }}
                        </div>

                        <div
                            class="space-y-1 max-h-48 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700">
                            @foreach ($event as $index => $e)
                            <div wire:click="openEditEventModal('{{ $dateStr }}', {{ $index }})"
                                class="flex flex-col p-1.5 rounded-lg cursor-pointer bg-white-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition">

                                <div class="flex justify-between items-center">
                                    <div class="font-medium truncate text-gray-900 dark:text-gray-100">{{ $e['title'] }}
                                    </div>
                                    <span class="text-xs opacity-70"><b>{{ $e['time'] ?? '--:--' }}</b></span>
                                </div>

                                <div class="flex justify-between items-center text-xs">
                                    <!-- Type badge -->
                                    <span class="px-2 py-0.5 rounded-full text-white
                                        {{ $e['type'] === 'Meeting' ? 'bg-green-500' : '' }}
                                        {{ $e['type'] === 'Leave' ? 'bg-red-500' : '' }}
                                        {{ $e['type'] === 'Notice' ? 'bg-blue-500' : '' }}
                                        {{ $e['type'] === 'Holiday' ? 'bg-yellow-500 text-black' : '' }}">
                                        {{ $e['type'] }}
                                    </span>

                                    @if($e['is_recurring'])
                                    <span class="flex items-center ml-5 text-blue-600 dark:text-blue-400">
                                        <span class="material-symbols-outlined text-sm">repeat</span>
                                        {{ $e['recurrence_description'] }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
        @endfor
</div>
@endif

<!-- WEEK VIEW -->
@if ($view === 'week')
<div class="grid grid-cols-7 gap-px text-center bg-gray-200 dark:bg-gray-700 rounded-lg shadow p-px text-sm">
    @php
    // Start from Sunday (0) instead of Monday (1)
    $weekStart = Carbon::parse($selectedDate)->startOfWeek(Carbon::SUNDAY);
    $weekDays = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));
    @endphp

    <!-- Day headers -->
    @foreach ($weekDays as $weekDay)
    <div class="font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2
            {{ $weekDay->isSaturday() ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300' : '' }}">
        {{ $weekDay->format('D') }}
    </div>
    @endforeach

    <!-- Day cells -->
    @foreach ($weekDays as $weekDay)
    @php
    $dateStr = $weekDay->format('Y-m-d');
    $event = $events[$dateStr] ?? null;
    $isToday = $weekDay->isSameDay($today);
    $isSaturday = $weekDay->isSaturday();

    // Check if any event on this date is a holiday
    $isHoliday = false;
    if ($event) {
    foreach ($event as $e) {
    if ($e['type'] === 'Holiday') {
    $isHoliday = true;
    break;
    }
    }
    }
    @endphp

    <div
        class="p-2 bg-white dark:bg-gray-800 relative h-24 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition duration-150
            {{ $isHoliday ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700' : 'hover:border border-gray-200 dark:border-gray-600' }}">

        <button wire:click="openAddEventModal({{ $weekDay->day }})" class="w-full text-left rounded p-1
                {{ $isToday ? 'bg-blue-50 dark:bg-blue-800' : '' }}
                hover:bg-gray-100 dark:hover:bg-gray-600
                {{ $isSaturday ? 'text-red-600 dark:text-red-300' : 'text-gray-800 dark:text-gray-200' }}">
            <span class="font-medium text-sm">{{ $weekDay->day }}</span>
        </button>

        @if ($event)
        <div x-data="{ expanded: false }" @mouseenter="expanded = true" @mouseleave="expanded = false"
            class="absolute bottom-1 right-19">
            <div class="relative">
                <span class="flex items-center justify-center w-6 h-6 text-sm font-semibold rounded-full
                                {{ $isHoliday ? 'bg-red-500' : 'bg-blue-500' }} text-white cursor-pointer">
                    {{ count($event) }}
                </span>

                <div x-show="expanded" x-transition class="absolute z-20 top-full mt-2 left-1/2 transform -translate-x-1/2
             w-52 bg-white dark:bg-gray-800
             shadow-xl rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                    <div class="text-sm font-semibold mb-2 text-gray-800 dark:text-gray-200">
                        Events on {{ $weekDay->format('M j') }}
                    </div>

                    <div
                        class="space-y-1 max-h-48 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700">
                        @foreach ($event as $index => $e)
                        <div wire:click="openEditEventModal('{{ $dateStr }}', {{ $index }})"
                            class="flex flex-col p-1.5 rounded-lg cursor-pointer bg-white-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition">

                            <div class="flex justify-between items-center">
                                <div class="font-medium truncate text-gray-900 dark:text-gray-100">{{ $e['title'] }}
                                </div>
                                <span class="text-xs opacity-70"><b>{{ $e['time'] ?? '--:--' }}</b></span>
                            </div>

                            <div class="flex justify-between items-center text-xs">
                                <!-- Type badge -->
                                <span class="px-2 py-0.5 rounded-full text-white
                                                {{ $e['type'] === 'Meeting' ? 'bg-green-500' : '' }}
                                                {{ $e['type'] === 'Leave' ? 'bg-red-500' : '' }}
                                                {{ $e['type'] === 'Notice' ? 'bg-blue-500' : '' }}
                                                {{ $e['type'] === 'Holiday' ? 'bg-yellow-500 text-black' : '' }}">
                                    {{ $e['type'] }}
                                </span>

                                @if($e['is_recurring'])
                                <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
                                    <span class="material-symbols-outlined text-xs">repeat</span>
                                    {{ $e['recurrence_description'] }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

<!-- DAY VIEW -->
@if ($view === 'day')
@php
$dateStr = Carbon::parse($selectedDate)->format('Y-m-d');
$eventsForDay = $events[$dateStr] ?? [];

// Check if any event on this date is a holiday
$isHoliday = false;
foreach ($eventsForDay as $event) {
if ($event['type'] === 'Holiday') {
$isHoliday = true;
break;
}
}
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200
                {{ $isHoliday ? 'text-red-600 dark:text-red-300' : '' }}">
            {{ Carbon::parse($selectedDate)->format('l, F j, Y') }}
            @if($isHoliday) <span class="text-sm">(Holiday)</span> @endif
        </h4>
    </div>

    @if (count($eventsForDay) > 0)
    <div class="space-y-3">
        @foreach ($eventsForDay as $index => $event)
        <div wire:click="openEditEventModal('{{ $dateStr }}', {{ $index }})"
            class="group p-4 rounded-lg cursor-pointer transition-all duration-200 border-l-4
                    {{ $event['type'] === 'Meeting'
                        ? 'border-green-500 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30'
                        : ($event['type'] === 'Leave'
                            ? 'border-red-500 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30'
                            : ($event['type'] === 'Holiday'
                                ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30'
                                : 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30')) }}">
            <div class="flex justify-between items-start">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm opacity-70">
                            @if($event['type'] === 'Meeting') groups
                            @elseif($event['type'] === 'Leave') event_available
                            @elseif($event['type'] === 'Holiday') celebration
                            @else notifications
                            @endif
                        </span>
                        <h3 class="font-medium text-gray-800 dark:text-gray-200 truncate">
                            {{ $event['title'] }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-3 mt-2 text-sm">
                        <span class="flex items-center gap-1 text-gray-600 dark:text-gray-300">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            {{ $event['time'] ?? 'All Day' }}
                        </span>
                        <span class="flex items-center gap-1
                                {{ $event['priority'] === 'High' ? 'text-red-600 dark:text-red-400' : '' }}
                                {{ $event['priority'] === 'Medium' ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                {{ $event['priority'] === 'Low' ? 'text-green-600 dark:text-green-400' : '' }}">
                            <span class="material-symbols-outlined text-sm">flag</span>
                            {{ $event['priority'] }}
                        </span>
                    </div>
                    @if (!empty($event['description']))
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                        {{ $event['description'] }}
                    </p>
                    @endif
                    @if($event['is_recurring'])
                    <div class="mt-2 flex items-center gap-1 text-blue-600 dark:text-blue-400 text-xs">
                        <span class="material-symbols-outlined text-xs">repeat</span>
                        {{ $event['recurrence_description'] }}
                    </div>
                    @endif
                </div>
                <button wire:click.stop="deleteEvent('{{ $dateStr }}', {{ $index }})"
                    class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-opacity duration-200">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-10">
        <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500">calendar_today</span>
        </div>
        <h5 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">No events scheduled</h5>
        <p class="text-gray-500 dark:text-gray-400 mb-4">Add an event to get started</p>
    </div>
    @endif
</div>
@endif

<!-- Add/Edit Event Modal -->
<x-modal name="openAddEventModal" :show="$showAddEventModal || $showEditEventModal" focusable>
    <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4" x-data
        x-on:click="$wire.closeModal()">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            x-on:click.stop>
            <!-- Header -->
            <div
                class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $showEditEventModal ? 'Edit Event' : 'Add Event' }}
                    </h2>
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
            <div class="p-6">
                <form wire:submit.prevent="{{ $showEditEventModal ? 'updateEvent' : 'addEvent' }}" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" wire:model.debounce.500ms="eventTitle" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    bg-white dark:bg-gray-700
                                    border-gray-300 dark:border-gray-600
                                    text-gray-900 dark:text-white
                                    placeholder-gray-400 dark:placeholder-gray-500
                                    {{ $errors->has('eventTitle') ? 'border-red-500 dark:border-red-500' : '' }}"
                            placeholder="Event title">
                        @error('eventTitle')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date & Time -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" wire:model="eventDate" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        {{ $errors->has('eventDate') ? 'border-red-500 dark:border-red-500' : '' }}">
                            @error('eventDate')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                            <input type="time" wire:model="eventTime" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        {{ $errors->has('eventTime') ? 'border-red-500 dark:border-red-500' : '' }}">
                            @error('eventTime')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Type & Priority -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select wire:model="eventType" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        {{ $errors->has('eventType') ? 'border-red-500 dark:border-red-500' : '' }}">
                                <option value="Meeting">Meeting</option>
                                <option value="Leave">Leave</option>
                                <option value="Notice">Notice</option>
                                @if(Auth::user()->hasRole('HR Manager'))
                                <option value="Holiday">Holiday</option>
                                @endif
                            </select>

                            @error('eventType')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                            <select wire:model="eventPriority"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white
                                        {{ $errors->has('eventPriority') ? 'border-red-500 dark:border-red-500' : '' }}">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                            @error('eventPriority')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea wire:model.debounce.500ms="eventDescription" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    bg-white dark:bg-gray-700
                                    border-gray-300 dark:border-gray-600
                                    text-gray-900 dark:text-white
                                    placeholder-gray-400 dark:placeholder-gray-500
                                    {{ $errors->has('eventDescription') ? 'border-red-500 dark:border-red-500' : '' }}"
                            placeholder="Event description (optional)"></textarea>
                        @error('eventDescription')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recurrence Section -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Repeat</label>
                        </div>

                        <!-- Main recurrence dropdown -->
                        <div class="mb-3">
                            <select wire:model="recurrencePattern" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600
                                   text-gray-900 dark:text-white">
                                <option value="none">Does not repeat</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <!-- Show interval only if not "none" -->
                        <div x-show="$wire.recurrencePattern !== 'none'" x-transition class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Repeat every
                            </label>
                            <div class="flex items-center">
                                <input type="number" wire:model="recurrenceInterval" min="0" max="365"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300" x-text="
                                        $wire.recurrencePattern === 'daily' ? 'day(s)' :
                                        $wire.recurrencePattern === 'weekly' ? 'week(s)' :
                                        $wire.recurrencePattern === 'monthly' ? 'month(s)' :
                                        $wire.recurrencePattern === 'yearly' ? 'year(s)' :
                                        'day(s)'
                                      ">
                                </span>
                            </div>
                        </div>

                        <!-- Weekday selection for weekly recurrence -->
                        <div x-show="$wire.recurrencePattern === 'weekly'" x-transition class="space-y-2 mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repeat
                                on</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']
                                as $day)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model="recurrenceWeekdays" value="{{ $day }}"
                                        class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ ucfirst(substr($day, 0, 3)) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- End date for recurrence -->
                        <div x-show="$wire.recurrencePattern !== 'none'" x-transition class="mb-3 mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                date (optional)</label>
                            <input type="date" wire:model="endDate" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white">
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end gap-3">
                        <button type="button" x-on:click.prevent="$dispatch('close')"
                            class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 dark:bg-blue-700 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200">
                            {{ $showEditEventModal ? 'Update Event' : 'Add Event' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-modal>
</div>