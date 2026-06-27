<div id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-[264px] md:w-[264px] bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-[transform,width] duration-300 -translate-x-full md:translate-x-0">

    <div id="sidebar-content" class="h-full pt-20 px-3 pb-6 overflow-y-auto text-sm space-y-4">
        <div class="flex flex-col h-full">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="dashboard">
                        <span class="material-symbols-outlined text-base">dashboard</span>
                        <span class="truncate">Dashboard</span>
                    </a>
                </li>

                <li>
                    <button data-menu="attendance"
                        class="sidebar-menu-btn flex items-center gap-2 w-full p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-base">schedule</span>
                        <span class="truncate">Attendance</span>
                        <span class="sidebar-chevron material-symbols-outlined ms-auto text-base transition-transform duration-200">keyboard_arrow_down</span>
                    </button>

                    <ul data-submenu="attendance" class="hidden pl-3 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('attendance') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="attendance-tracker">
                                <span class="material-symbols-outlined text-xs">monitoring</span>
                                Tracker
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('attendance.my-history') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="my-attendance-history">
                                <span class="material-symbols-outlined text-xs">history</span>
                                My History
                            </a>
                        </li>
                        @role('HR Manager')
                        <li>
                            <a href="{{ route('attendance.employee-history') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="employee-attendance-history">
                                <span class="material-symbols-outlined text-xs">groups</span>
                                Employee History
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>

                <li>
                    <a href="{{ route('attendance.calendar') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="attendance-calendar">
                        <span class="material-symbols-outlined text-base">calendar_today</span>
                        <span class="truncate">Calendar</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('notices.index') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="notices">
                        <span class="material-symbols-outlined text-base">campaign</span>
                        <span class="truncate">Notice Board</span>
                    </a>
                </li>

                <li>
                    <button data-menu="leaveManagement"
                        class="sidebar-menu-btn flex items-center gap-2 w-full p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-base">location_away</span>
                        <span class="truncate">Leave Management</span>
                        <span class="sidebar-chevron material-symbols-outlined ms-auto text-base transition-transform duration-200">keyboard_arrow_down</span>
                    </button>

                    <ul data-submenu="leaveManagement" class="hidden pl-3 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('leave') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="leave">
                                <span class="material-symbols-outlined text-xs">event_note</span>
                                Leave Details
                            </a>
                        </li>
                        @role('HR Manager')
                        <li>
                            <a href="{{ route('leave_manage') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="leave-manage">
                                <span class="material-symbols-outlined text-xs">playlist_add_check</span>
                                Manage Leave
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>

                @role('HR Manager')
                <li>
                    <a href="{{ route('hr.employees') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="employees">
                        <span class="material-symbols-outlined text-base">group_add</span>
                        <span class="truncate">Manage Employee</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('departments.index') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="departments">
                        <span class="material-symbols-outlined text-base">business</span>
                        <span class="truncate">Departments</span>
                    </a>
                </li>
                @endrole

                <li>
                    <button data-menu="payroll"
                        class="sidebar-menu-btn flex items-center gap-2 w-full p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-base">payments</span>
                        <span class="truncate">Payroll</span>
                        <span class="sidebar-chevron material-symbols-outlined ms-auto text-base transition-transform duration-200">keyboard_arrow_down</span>
                    </button>

                    <ul data-submenu="payroll" class="hidden pl-3 mt-1 space-y-1">
                        @role('HR Manager')
                        <li>
                            <a href="{{ route('payroll.manager') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="payroll">
                                <span class="material-symbols-outlined text-xs">manage_accounts</span>
                                Payroll Manager
                            </a>
                        </li>
                        @endrole
                        <li>
                            <a href="{{ route('payroll.my-payslips') }}"
                                class="sidebar-link flex items-center gap-2 p-2 ps-6 pr-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                data-route="my-payslips">
                                <span class="material-symbols-outlined text-xs">receipt</span>
                                My Payslips
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-600"></li>
            </ul>

            <ul class="space-y-2 mt-auto">
                <li class="border-t border-gray-200 dark:border-gray-600"></li>
                <li>
                    @if (Auth::user()->hasRole('HR Manager'))
                    <a href="{{ route('settings.system') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="system-settings">
                    @else
                    <a href="{{ route('settings.profile') }}"
                        class="sidebar-link flex items-center gap-2 p-3 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        data-route="settings/profile">
                    @endif
                        <span class="material-symbols-outlined text-base">settings</span>
                        <span class="truncate">Setting</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
