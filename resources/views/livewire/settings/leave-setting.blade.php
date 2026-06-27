<div class="flex transition-all duration-400">
    <x-setting-side title=" Settings" />

    <div class="flex-1 px-3 py-2">
        <x-headings title="Leave Settings" />
        <x-notifications />
        <x-form submitAction="addLeaveType" submitText="Add Leave Type" enctype="multipart/form-data"
            class="w-full bg-white dark:bg-gray-800 px-4 py-6 rounded-lg shadow-by-force mx-auto">
            <p class="text-gray-700 dark:text-gray-200 col-span-2 text-lg font-semibold">
                Leave Type
            </p>
            <hr class="w-full border-gray-300 dark:border-gray-600 mt-1" />
            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>
                    <label for="leave_type"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Leave
                        Type</label>
                    <input type="text" id="leaveType" placeholder="Leave Type" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="leave_type" />
                    @error('leave_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="default_days" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Default Days</label>
                    <input type="text" id="default_days" placeholder="No. of Days" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                    text-gray-900 dark:text-gray-100
                    bg-white dark:bg-gray-800
                    focus:outline-none focus:ring-2 focus:ring-blue-500" required wire:model="default_days" />
                    @error('default_days')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-form>
        <x-form submitAction="submit" submitText="Apply" enctype="multipart/form-data"
            class="w-full bg-white dark:bg-gray-800 px-4 py-6 rounded-lg shadow-by-force mx-auto mt-5">

            <p class="text-gray-700 dark:text-gray-200 col-span-2 text-lg font-semibold">
                Employee Leave Configuration
            </p>
            <hr class="w-full border-gray-300 dark:border-gray-600 mt-1" />

            <!-- Employee & Leave Type Selection -->
            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Employee Name</label>
                    <select id="employee_name"
                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm select-icon
                text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 h-10" required
                        wire:model="employee">
                        <option value="" selected disabled hidden>Select Employee</option>
                        @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @error('employee')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Leave Type</label>
                    <select
                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm select-icon
                text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 h-10"
                        required wire:model.live.debounce="selectedLeaveType">
                        <option value="" selected disabled hidden>Select Leave Type</option>
                        @foreach ($leaveTypes as $leaveType)
                        <option value="{{ $leaveType->id }}">
                            {{ $leaveType->leave_type }}</option>
                        @endforeach
                    </select>
                    @error('selectedLeaveType')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Leave Days Input -->
            <div class="grid grid-cols-2 gap-4 mt-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Leave Days</label>
                    <input type="text" id="days" placeholder="No. of Days" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100
                bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" wire:model="days"
                        required />
                </div>
                @error('days')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </x-form>

    </div>
</div>
