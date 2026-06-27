@php
use Carbon\Carbon;
$todayDate = $today;
@endphp

@extends('components.layouts.app')

@section('content')
    <x-headings title="Calendar" />

    <x-notifications />

    <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-4">
            <div class="flex items-center gap-1 bg-gray-400 dark:bg-gray-700 rounded-lg overflow-hidden">
                <a href="{{ route('attendance.calendar', ['month' => Carbon::create($year, $month, 1)->subMonth()->month, 'year' => Carbon::create($year, $month, 1)->subMonth()->year]) }}"
                   class="px-2 py-1 hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 rounded-l-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-sm dark:text-gray-200">chevron_left</span>
                </a>
                <a href="{{ route('attendance.calendar') }}"
                   class="px-4 py-1.5 bg-blue-700 hover:bg-blue-600 dark:bg-blue-800 dark:hover:bg-blue-700 text-white rounded text-sm transition duration-150">
                    Today
                </a>
                <a href="{{ route('attendance.calendar', ['month' => Carbon::create($year, $month, 1)->addMonth()->month, 'year' => Carbon::create($year, $month, 1)->addMonth()->year]) }}"
                   class="px-2 py-1 hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150 rounded-r-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-sm dark:text-gray-200">chevron_right</span>
                </a>
            </div>

            <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                {{ $monthName }} {{ $year }}
            </h3>

            <div></div>
        </div>

        <div class="grid grid-cols-7 gap-px text-center bg-gray-200 dark:bg-gray-700 rounded-lg shadow p-px text-sm">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2
                {{ $day === 'Sat' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300' : '' }}">
                {{ $day }}
            </div>
            @endforeach

            @for ($i = 0; $i < $startDay; $i++)
            <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded"></div>
            @endfor

            @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
            $currentDate = Carbon::create($year, $month, $day, 0, 0, 0, 'Asia/Kathmandu');
            $dateStr = $currentDate->format('Y-m-d');
            $event = $events[$dateStr] ?? null;
            $isToday = $currentDate->isSameDay($todayDate);

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

            <div class="p-2 bg-white dark:bg-gray-800 relative h-24 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition duration-150
                {{ $isHoliday ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700' : 'hover:border border-gray-200 dark:border-gray-600' }}">

                <div class="w-full text-left rounded p-1
                    {{ $isToday ? 'bg-blue-50 dark:bg-blue-800' : '' }}
                    hover:bg-gray-100 dark:hover:bg-gray-600
                    {{ $isHoliday ? 'text-red-600 dark:text-red-300' : 'text-gray-800 dark:text-gray-200' }}">
                    <span class="font-medium text-sm">{{ $day }}</span>
                </div>

                @if ($event)
                <div class="absolute bottom-1 right-1 flex flex-wrap gap-1">
                    @php
                    $visibleEvents = array_slice($event, 0, 2);
                    $remaining = count($event) - 2;
                    @endphp
                    @foreach ($visibleEvents as $e)
                    <span class="inline-block w-2 h-2 rounded-full
                        {{ $e['type'] === 'Holiday' ? 'bg-red-500' : '' }}
                        {{ $e['type'] === 'Meeting' ? 'bg-green-500' : '' }}
                        {{ $e['type'] === 'Leave' ? 'bg-yellow-500' : '' }}
                        {{ $e['type'] === 'Notice' ? 'bg-blue-500' : '' }}"
                          title="{{ $e['title'] }}"></span>
                    @endforeach
                    @if ($remaining > 0)
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">+{{ $remaining }}</span>
                    @endif
                </div>
                @endif
            </div>
            @endfor
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Events & Holidays</h3>

            @php
            $hasEvents = false;
            foreach ($events as $dateStr => $dateEvents) {
                foreach ($dateEvents as $e) {
                    if ($e['editable']) {
                        $hasEvents = true;
                        break;
                    }
                }
                if ($hasEvents) break;
            }
            @endphp

            @if ($hasEvents)
            <div class="space-y-3">
                @foreach ($events as $dateStr => $dateEvents)
                @php
                $userEvents = array_filter($dateEvents, fn($e) => $e['editable']);
                if (empty($userEvents)) continue;
                @endphp
                <div>
                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        {{ Carbon::parse($dateStr)->format('M j, Y') }}
                    </h4>
                    <div class="space-y-2">
                        @foreach ($userEvents as $e)
                        <div class="flex items-center justify-between p-3 rounded-lg border-l-4
                            {{ $e['type'] === 'Meeting' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : '' }}
                            {{ $e['type'] === 'Leave' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : '' }}
                            {{ $e['type'] === 'Notice' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : '' }}
                            {{ $e['type'] === 'Holiday' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $e['title'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @if ($e['time'])
                                    <span class="mr-3">{{ $e['time'] }}</span>
                                    @endif
                                    <span>{{ $e['type'] }}</span>
                                    @if ($e['is_recurring'])
                                    <span class="ml-3 text-blue-600 dark:text-blue-400">{{ $e['recurrence_description'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('attendance.calendar.event.edit', $e['id']) }}?month={{ $month }}&year={{ $year }}"
                                   class="text-blue-500 hover:text-blue-700 text-sm">Edit</a>
                                <form method="POST" action="{{ route('attendance.calendar.event.destroy', $e['id']) }}"
                                      onsubmit="return confirm('Delete this event?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
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
                <p class="text-gray-500 dark:text-gray-400">Add an event using the form.</p>
            </div>
            @endif

            @php
            $holidayEvents = [];
            foreach ($events as $dateStr => $dateEvents) {
                foreach ($dateEvents as $e) {
                    if (!$e['editable']) {
                        $holidayEvents[] = ['date' => $dateStr] + $e;
                    }
                }
            }
            @endphp

            @if (!empty($holidayEvents))
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Holidays</h3>
                <div class="space-y-2">
                    @foreach ($holidayEvents as $e)
                    <div class="flex items-center gap-3 p-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700">
                        <span class="material-symbols-outlined text-yellow-600">celebration</span>
                        <div>
                            <div class="font-medium text-gray-800 dark:text-gray-200 text-sm">{{ $e['title'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Carbon::parse($e['date'])->format('M j, Y') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Add Event</h3>

            <form method="POST" action="{{ route('attendance.calendar.event.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" name="eventTitle" value="{{ old('eventTitle') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('eventTitle') border-red-500 @enderror"
                           placeholder="Event title">
                    @error('eventTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                        <input type="date" name="eventDate" value="{{ old('eventDate', Carbon::now('Asia/Kathmandu')->format('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('eventDate') border-red-500 @enderror">
                        @error('eventDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                        <input type="time" name="eventTime" value="{{ old('eventTime') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select name="eventType" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                            <option value="Meeting">Meeting</option>
                            <option value="Leave">Leave</option>
                            <option value="Notice">Notice</option>
                            @if(Auth::user()->hasRole('HR Manager'))
                            <option value="Holiday">Holiday</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                        <select name="eventPriority" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="eventDescription" rows="3"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
                              placeholder="Optional description">{{ old('eventDescription') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    Add Event
                </button>
            </form>
        </div>
    </div>
@endsection
