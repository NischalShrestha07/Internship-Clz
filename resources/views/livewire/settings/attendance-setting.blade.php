<div class="flex transition-all duration-400">
    <x-setting-side title="Settings" />

    <div class="flex-1 px-3 py-2">
        <x-headings title="Attendance Settings" />
        <x-notifications />

        <x-form submitAction="submit" submitText="Save Settings" enctype="multipart/form-data"
            class="w-full bg-white dark:bg-gray-800 px-4 py-6 rounded-lg shadow-by-force mx-auto">

            <!-- Core Working Hours Section -->
            <div class="mb-8">
                <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-4">
                    Working Hours Configuration
                </p>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Office Start Time
                        </label>
                        <input type="time" id="start_time" wire:model="start_time" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Office End Time
                        </label>
                        <input type="time" id="end_time" wire:model="end_time" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                    </div>

                    <div>
                        <label for="daily_working_hours"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Daily Working Hours
                        </label>
                        <input type="number" step="0.25" id="daily_working_hours" wire:model="daily_working_hours"
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Standard working hours per day</p>
                    </div>
                </div>
            </div>

            <!-- Break Settings Section -->
            <div class="mb-8">
                <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-4">
                    Break Settings
                </p>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_break_minutes"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Default Break Duration (minutes)
                        </label>
                        <input type="number" id="default_break_minutes" wire:model="default_break_minutes" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Default break time deducted from
                            working hours</p>
                    </div>
                </div>
            </div>

            <!-- Overtime Rules Section -->
            <div class="mb-8">
                <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-4">
                    Overtime Rules
                </p>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="overtime_threshold"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Overtime Threshold (hours)
                        </label>
                        <input type="number" step="0.25" id="overtime_threshold" wire:model="overtime_threshold" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hours after which overtime is
                            calculated</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-4">
                    Attendance Policies
                </p>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="late_grace_minutes"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Late Arrival Grace Period (minutes)
                        </label>
                        <input type="number" id="late_grace_minutes" wire:model="late_grace_minutes" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Allowed minutes after start time before
                            marked late</p>
                    </div>
                </div>
            </div>

            <!-- Display Settings Section -->
            <div class="mb-8">
                <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-4">
                    Display Settings
                </p>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="records_per_page"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Records Per Page
                        </label>
                        <input type="number" id="records_per_page" wire:model="records_per_page" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Number of records to show per page in
                            lists</p>
                    </div>
                </div>
            </div>
        </x-form>
    </div>
</div>
