@extends('components.layouts.app')

@section('content')
    <x-headings title="Leave Settings" />

    <x-notifications />

    <div class="flex gap-6">
        <div class="w-64 shrink-0">
            <x-setting-side title="Settings" />
        </div>

        <div class="flex-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Add Leave Type</h3>
                <form method="POST" action="{{ route('setting.leave') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave Type</label>
                        <input type="text" name="leave_type" value="{{ old('leave_type') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('leave_type') border-red-500 @enderror"
                               placeholder="e.g. Annual Leave">
                        @error('leave_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Days</label>
                        <input type="number" name="default_days" value="{{ old('default_days') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('default_days') border-red-500 @enderror"
                               placeholder="e.g. 20">
                        @error('default_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add Leave Type
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Employee Leave Configuration</h3>
                <form method="POST" action="{{ route('setting.leave') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employee Name</label>
                        <select name="employee" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                            <option value="">Select Employee</option>
                            @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave Type</label>
                        <select name="selectedLeaveType" id="selectedLeaveType" required
                                onchange="updateDays(this)"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                            <option value="">Select Leave Type</option>
                            @foreach ($leaveTypes as $lt)
                            <option value="{{ $lt->id }}" data-days="{{ $lt->default_days }}" {{ old('selectedLeaveType') == $lt->id ? 'selected' : '' }}>
                                {{ $lt->leave_type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave Days</label>
                        <input type="number" name="days" id="days" value="{{ old('days') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Apply
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateDays(select) {
            const selected = select.options[select.selectedIndex];
            const defaultDays = selected.getAttribute('data-days');
            if (defaultDays) {
                document.getElementById('days').value = defaultDays;
            }
        }
    </script>
@endsection
