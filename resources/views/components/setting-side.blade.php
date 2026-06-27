<div
    class="w-64 max-h-[400px] bg-white dark:bg-gray-800 border rounded-xl border-gray-200 dark:border-gray-700 shadow-by-force mt-2">
    <div class="py-4 px-3">
        @if ($title)
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1 text-center">{{ $title }}</h2>
        <hr class="mb-4">
        @endif

        <ul class="space-y-3" x-data="{ currentPath: window.location.pathname }">
            @role('HR Manager')
            <li>
                <a wire:navigate href="/system-settings" :class="currentPath === '/system-settings'
                            ?
                            'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex items-center gap-2 p-3 rounded-lg text-sm">
                    <span class="material-symbols-outlined text-base">admin_panel_settings</span>
                    <span class="truncate">System Settings</span>
                </a>
            </li>
            <li>
                <a wire:navigate href="/attendance-settings" :class="currentPath === '/attendance-settings' ?
                            'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex items-center gap-2 p-3 rounded-lg text-sm">
                    <span class="material-symbols-outlined text-base">schedule</span>
                    <span class="truncate">Attendance Settings</span>
                </a>
            </li>
            <li>
                <a wire:navigate href="/leave-setting" :class="currentPath === '/leave-setting' ?
                            'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex items-center gap-2 p-3 rounded-lg text-sm">
                    <span class="material-symbols-outlined text-base">location_away</span>
                    <span class="truncate">Leave Settings</span>
                </a>
            </li>
            @endrole

            @role('HR Manager')

            <li>
                <a wire:navigate href="/payroll-settings" :class="currentPath === '/payroll-settings' ?
                            'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex items-center gap-2 p-3 rounded-lg text-sm">
                    <span class="material-symbols-outlined text-base">payments</span>
                    <span class="truncate">Payroll Settings</span>
                </a>
            </li>
            @endrole

            <li>
                <a wire:navigate href="/settings/profile" :class="currentPath === '/settings/profile' ?
                        'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                        'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex items-center gap-2 p-3 rounded-lg text-sm">
                    <span class="material-symbols-outlined text-base">person</span>
                    <span class="truncate">Profile Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>