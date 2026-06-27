<div class="main transition-all duration-300" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
    <x-headings title="Change Password" />
    <x-notifications />
    <x-form submitAction="updatePassword" submitText="Save Changes"
        class="w-full bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg dark:shadow-gray-800">

        <p class="text-gray-600 dark:text-gray-300 text-base font-medium mb-6">
            Update your password below.
        </p>

        <!-- Current Password -->
        <div class="mb-6">
            <label for="current_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                Current Password
            </label>
            <div class="relative">
                <input :type="showCurrent ? 'text' : 'password'" id="current_password"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    wire:model.defer="current_password" required placeholder="Enter current password" />
                <button type="button" @click="showCurrent = !showCurrent"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors duration-200">
                    <span x-text="showCurrent ? 'Hide' : 'Show'" class="text-sm font-medium"></span>
                </button>
            </div>
            @error('current_password')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
            <div class="mt-3 text-sm">
                <a href="{{ route('password.request') }}"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors duration-200">Forgot
                    Password?</a>
            </div>
        </div>

        <!-- New Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                New Password
            </label>
            <div class="relative">
                <input :type="showNew ? 'text' : 'password'" id="password"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    wire:model="password" required placeholder="Enter new password" />
                <button type="button" @click="showNew = !showNew"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors duration-200">
                    <span x-text="showNew ? 'Hide' : 'Show'" class="text-sm font-medium"></span>
                </button>
            </div>
            @error('password')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-8">
            <label for="password_confirmation"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                Confirm New Password
            </label>
            <div class="relative">
                <input :type="showConfirm ? 'text' : 'password'" id="confirm_password"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    wire:model.defer="password_confirmation" required placeholder="Re-enter new password" />
                <button type="button" @click="showConfirm = !showConfirm"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors duration-200">
                    <span x-text="showConfirm ? 'Hide' : 'Show'" class="text-sm font-medium"></span>
                </button>
            </div>
            @error('password_confirmation')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

    </x-form>
</div>