@extends('components.layouts.app')

@section('content')
    <x-headings title="Attendance Settings" />

    <x-notifications />

    <div class="flex gap-6">
        <div class="w-64 shrink-0">
            <x-setting-side title="Settings" />
        </div>

        <div class="flex-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <form method="POST" action="{{ route('attendance.setting') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $settings->start_time ?? '09:00') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('start_time') border-red-500 @enderror">
                        @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $settings->end_time ?? '18:00') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('end_time') border-red-500 @enderror">
                        @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Daily Working Hours</label>
                        <input type="number" name="daily_working_hours" value="{{ old('daily_working_hours', $settings->daily_working_hours ?? 8) }}" step="0.5" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('daily_working_hours') border-red-500 @enderror">
                        @error('daily_working_hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Break (minutes)</label>
                        <input type="number" name="default_break_minutes" value="{{ old('default_break_minutes', $settings->default_break_minutes ?? 60) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('default_break_minutes') border-red-500 @enderror">
                        @error('default_break_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overtime Threshold (hours)</label>
                        <input type="number" name="overtime_threshold" value="{{ old('overtime_threshold', $settings->overtime_threshold ?? 8) }}" step="0.5" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('overtime_threshold') border-red-500 @enderror">
                        @error('overtime_threshold') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Late Grace Period (minutes)</label>
                        <input type="number" name="late_grace_minutes" value="{{ old('late_grace_minutes', $settings->late_grace_minutes ?? 15) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('late_grace_minutes') border-red-500 @enderror">
                        @error('late_grace_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Records Per Page</label>
                        <input type="number" name="records_per_page" value="{{ old('records_per_page', $settings->records_per_page ?? 25) }}" min="5" max="100" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('records_per_page') border-red-500 @enderror">
                        @error('records_per_page') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
