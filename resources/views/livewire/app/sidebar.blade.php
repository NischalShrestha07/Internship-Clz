<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-40 w-[264px] bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 -ml-2"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak
    x-show="sidebarOpen || window.innerWidth >= 768" @resize.window="sidebarOpen = window.innerWidth >= 768">

    <div class="h-full pt-20 px-3 pb-6 overflow-y-auto text-sm space-y-4" x-data="{
        activePage: window.location.pathname.split('/').filter(Boolean).pop() || '',
        openMenus: {
            attendance: ['attendance-tracker', 'my-attendance-history', 'employee-attendance-history', 'attendance-history'].includes(window.location.pathname.split('/').filter(Boolean).pop()),
            leaveManagement: ['leave', 'leave-manage', 'employee-report'].includes(window.location.pathname.split('/').filter(Boolean).pop()),
            payroll: ['payroll', 'my-payslips'].includes(window.location.pathname.split('/').filter(Boolean).pop()),
            settings: ['system-settings'].includes(window.location.pathname.split('/').filter(Boolean).pop()),
        },
        toggleMenu(menu) {
            this.openMenus[menu] = !this.openMenus[menu];
        }
    }">
        <div class="flex flex-col h-full">

            <!-- Top Navigation Section -->
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a wire:navigate href="/dashboard" :class="activePage === 'dashboard' ?
                            'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="flex items-center gap-2 p-3 rounded-lg text-sm">
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        <span class="truncate">Dashboard</span>
                    </a>
                </li>

                <!-- Attendance Section -->
                <li>
                    <button @click="toggleMenu('attendance')"
                        class="flex items-center gap-2 w-full p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="openMenus.attendance ? 'bg-gray-200 dark:bg-gray-700 font-semibold dark:text-white' : ''">
                        <span class="material-symbols-outlined text-base">schedule</span>
                        <span class="truncate">Attendance</span>
                        <span class="material-symbols-outlined ms-auto text-base transition-transform duration-200"
                            :class="{ 'rotate-180': openMenus.attendance }">keyboard_arrow_down</span>
                    </button>

                    <ul x-show="openMenus.attendance" x-transition x-cloak class="pl-3 mt-1 space-y-1">
                        <li>
                            <a wire:navigate href="/attendance-tracker" :class="activePage === 'attendance-tracker' ?
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">monitoring</span>
                                Tracker
                            </a>
                        </li>
                        <!-- For all users -->
                        <li>
                            <a wire:navigate href="/my-attendance-history" :class="activePage === 'my-attendance-history' ?
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">history</span>
                                My History
                            </a>
                        </li>
                        <!-- Only for HR -->
                        @role('HR Manager')
                        <li>
                            <a wire:navigate href="/employee-attendance-history" :class="activePage === 'employee-attendance-history' ?
                                        'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                        'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">groups</span>
                                Employee History
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>

                <li>
                    <a wire:navigate href="/attendance-calendar" :class="activePage === 'attendance-calendar' ?
                            'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                            'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="flex items-center gap-2 p-3 rounded-lg text-sm">
                        <span class="material-symbols-outlined text-base">calendar_today</span>
                        <span class="truncate">Calendar</span>
                    </a>
                </li>

                {{-- @role('HR Manager')
                <li>
                    <a wire:navigate href="{{ route('holiday.manager') }}"
                        :class="activePage === 'holidays' ?
                                                    'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="flex items-center gap-2 p-3 rounded-lg text-sm">
                        <span class="material-symbols-outlined text-base">park</span> <span
                            class="truncate">Holiday</span>
                    </a>
                </li>
                @endrole --}}

                <!-- Leave Management -->
                <li>
                    <button @click="toggleMenu('leaveManagement')"
                        class="flex items-center gap-2 w-full p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="openMenus.leaveManagement ? 'bg-gray-200 dark:bg-gray-700 font-semibold dark:text-white' : ''">
                        <span class="material-symbols-outlined text-base">location_away</span>
                        <span class="truncate">Leave Management</span>
                        <span class="material-symbols-outlined ms-auto text-base transition-transform duration-200"
                            :class="{ 'rotate-180': openMenus.leaveManagement }">keyboard_arrow_down</span>
                    </button>

                    <ul x-show="openMenus.leaveManagement" x-transition x-cloak class="pl-3 mt-1 space-y-1">
                        <li>
                            <a wire:navigate href="/leave" :class="activePage === 'leave' ?
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">event_note</span>
                                Leave Details
                            </a>
                        </li>

                        @role('HR Manager')
                        <li>
                            <a wire:navigate href="/leave-manage" :class="activePage === 'leave-manage' ?
                                        'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                        'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">playlist_add_check</span>
                                Manage Leave
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>
                @role('HR Manager')
                <li>
                    <a wire:navigate href="/hr/employees" :class="activePage === 'employees' ?
                                'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                                'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="flex items-center gap-2 p-3 rounded-lg text-sm">
                        <span class="material-symbols-outlined text-base">group_add</span>
                        <span class="truncate">Manage Employee</span>
                    </a>
                </li>
                @endrole

                <!-- Payroll Section -->
                <li>
                    <button @click="toggleMenu('payroll')"
                        class="flex items-center gap-2 w-full p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="openMenus.payroll ? 'bg-gray-200 dark:bg-gray-700 font-semibold dark:text-white' : ''">
                        <span class="material-symbols-outlined text-base">payments</span>
                        <span class="truncate">Payroll</span>
                        <span class="material-symbols-outlined ms-auto text-base transition-transform duration-200"
                            :class="{ 'rotate-180': openMenus.payroll }">keyboard_arrow_down</span>
                    </button>

                    <ul x-show="openMenus.payroll" x-transition x-cloak class="pl-3 mt-1 space-y-1">
                        <!-- For HR Manager -->
                        @role('HR Manager')
                        <li>
                            <a wire:navigate href="/payroll" :class="activePage === 'payroll' ?
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">manage_accounts</span>
                                Payroll Manager
                            </a>
                        </li>
                        @endrole

                        <!-- For all employees -->
                        <li>
                            <a wire:navigate href="/my-payslips" :class="activePage === 'my-payslips' ?
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white' :
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                class="flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm">
                                <span class="material-symbols-outlined text-xs">receipt</span>
                                My Payslips
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Divider -->
                <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-600"></li>
            </ul>


            <ul class="space-y-2 mt-auto">
                <li class="border-t border-gray-200 dark:border-gray-600"></li>
                <li class="relative">
                    @if (Auth::user()->hasRole('HR Manager'))
                    <a wire:navigate href="/system-settings" :class="activePage === 'system-settings' ?
                                'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                                'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="flex items-center gap-2 p-3 rounded-lg text-sm">
                        @else
                        <a wire:navigate href="/settings/profile" :class="activePage === 'settings/profile' ?
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white font-semibold' :
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex items-center gap-2 p-3 rounded-lg text-sm">
                            @endif
                            <span class="material-symbols-outlined text-base">settings</span>
                            <span class="truncate">Setting</span>
                        </a>
                </li>
            </ul>
        </div>
    </div>
</div>