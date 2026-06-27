<div class="w-64 max-h-[400px] bg-white dark:bg-gray-800 border rounded-xl border-gray-200 dark:border-gray-700 shadow-by-force mt-2">
    <div class="py-4 px-3">
        @if ($title)
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-1 text-center">{{ $title }}</h2>
        <hr class="mb-4">
        @endif

        <ul class="space-y-3">
            @role('HR Manager')
            <li>
                <a href="/system-settings"
                   class="flex items-center gap-2 p-3 rounded-lg text-sm setting-link {{ request()->is('system-settings') ? 'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="material-symbols-outlined text-base">admin_panel_settings</span>
                    <span class="truncate">System Settings</span>
                </a>
            </li>
            <li>
                <a href="/attendance-settings"
                   class="flex items-center gap-2 p-3 rounded-lg text-sm setting-link {{ request()->is('attendance-settings') ? 'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="material-symbols-outlined text-base">schedule</span>
                    <span class="truncate">Attendance Settings</span>
                </a>
            </li>
            <li>
                <a href="/leave-setting"
                   class="flex items-center gap-2 p-3 rounded-lg text-sm setting-link {{ request()->is('leave-setting') ? 'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="material-symbols-outlined text-base">location_away</span>
                    <span class="truncate">Leave Settings</span>
                </a>
            </li>
            @endrole

            @role('HR Manager')
            <li>
                <a href="/payroll-settings"
                   class="flex items-center gap-2 p-3 rounded-lg text-sm setting-link {{ request()->is('payroll-settings') ? 'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="material-symbols-outlined text-base">payments</span>
                    <span class="truncate">Payroll Settings</span>
                </a>
            </li>
            @endrole

            <li>
                <a href="/settings/profile"
                   class="flex items-center gap-2 p-3 rounded-lg text-sm setting-link {{ request()->is('settings/profile') ? 'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="material-symbols-outlined text-base">person</span>
                    <span class="truncate">Profile Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.setting-link').forEach(function (link) {
            if (link.getAttribute('href') === window.location.pathname) {
                link.classList.add('bg-gray-200', 'dark:bg-gray-700', 'dark:text-white', 'font-semibold');
                link.classList.remove('text-gray-700', 'dark:text-gray-200', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            }
        });
    });
</script>
