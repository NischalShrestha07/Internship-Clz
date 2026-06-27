@extends('components.layouts.app')

@section('content')
    <x-headings title="Edit Event" />

    <x-notifications />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Edit Event</h3>
            <a href="{{ route('attendance.calendar', ['month' => $month, 'year' => $year]) }}"
               class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Calendar</a>
        </div>

        <form method="POST" action="{{ route('attendance.calendar.event.update', $event) }}" class="space-y-4">
            @csrf
            @method('POST')
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                <input type="text" name="eventTitle" value="{{ old('eventTitle', $event->title) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('eventTitle') border-red-500 @enderror">
                @error('eventTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" name="eventDate" value="{{ old('eventDate', $event->date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                    @error('eventDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                    <input type="time" name="eventTime" value="{{ old('eventTime', $event->time ? Carbon\Carbon::parse($event->time)->format('H:i') : '') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select name="eventType" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                        <option value="Meeting" {{ $event->type == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="Leave" {{ $event->type == 'Leave' ? 'selected' : '' }}>Leave</option>
                        <option value="Notice" {{ $event->type == 'Notice' ? 'selected' : '' }}>Notice</option>
                        @if(Auth::user()->hasRole('HR Manager'))
                        <option value="Holiday" {{ $event->type == 'Holiday' ? 'selected' : '' }}>Holiday</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                    <select name="eventPriority" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                        <option value="Low" {{ $event->priority == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ $event->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ $event->priority == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="eventDescription" rows="3"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">{{ old('eventDescription', $event->description) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <div></div>
                <div class="flex gap-3">
                    <a href="{{ route('attendance.calendar', ['month' => $month, 'year' => $year]) }}"
                       class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Update Event
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
