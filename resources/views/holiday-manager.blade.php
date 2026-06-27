@extends('components.layouts.app')

@section('content')
    <x-headings title="Holiday Manager" />

    <x-notifications />

    <div class="flex gap-6">
        <div class="w-full max-w-md shrink-0">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Add Holiday</h3>
                <form method="POST" action="{{ route('holiday.manager') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('title') border-red-500 @enderror"
                               placeholder="e.g. Dashain">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('description') border-red-500 @enderror"
                                  placeholder="Optional description">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('start_date') border-red-500 @enderror">
                            @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('end_date') border-red-500 @enderror">
                            @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_recurring" value="1" id="is_recurring"
                               {{ old('is_recurring') ? 'checked' : '' }}
                               onchange="document.getElementById('recurrence_pattern_group').classList.toggle('hidden', !this.checked)">
                        <label for="is_recurring" class="text-sm text-gray-700 dark:text-gray-300">Recurring</label>
                    </div>

                    <div id="recurrence_pattern_group" class="{{ old('is_recurring') ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recurrence Pattern</label>
                        <select name="recurrence_pattern"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('recurrence_pattern') border-red-500 @enderror">
                            <option value="">Select pattern</option>
                            <option value="yearly" {{ old('recurrence_pattern') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="monthly" {{ old('recurrence_pattern') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('recurrence_pattern') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        </select>
                        @error('recurrence_pattern') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add Holiday
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Holidays</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Start Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">End Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Recurring</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Active</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($holidays as $holiday)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $holiday->title }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $holiday->description ?? '—' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $holiday->start_date->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200">{{ $holiday->end_date->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 text-xs rounded {{ $holiday->is_recurring ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ $holiday->is_recurring ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 text-xs rounded {{ $holiday->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $holiday->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <form method="POST" action="{{ route('holiday.destroy', $holiday) }}" class="inline"
                                              onsubmit="return confirm('Delete this holiday?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                        No holidays defined.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($holidays->hasPages())
                    <div class="mt-4">
                        {{ $holidays->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cb = document.getElementById('is_recurring');
            const group = document.getElementById('recurrence_pattern_group');
            if (cb && group) {
                cb.addEventListener('change', function () {
                    group.classList.toggle('hidden', !this.checked);
                });
            }
        });
    </script>
@endsection
