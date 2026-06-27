<div id="app-header"
    class="w-full fixed top-0 left-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between h-16 px-4">

        <div class="flex items-center gap-2">
            <button id="sidebar-toggle"
                class="p-2 rounded dark:bg-gray-800 text-black hover:bg-gray-400 transition dark:hover:bg-gray-700 dark:text-white">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ $system_info?->logo ? asset('storage/' . $system_info->logo) : asset('1.png') }}"
                    alt="{{ $system_info->name ?? 'Default Logo' }}" class="h-10 w-10 rounded-sm" />
                <h1 class="text-2xl font-bold text-black dark:text-white sm:block">
                    {{ $system_info->name ?? 'Organization' }}
                </h1>
            </a>
        </div>

        <div class="flex items-center gap-5">
            <div class="flex items-center gap-2">
                <form method="POST"
                    action="{{ $isClockedIn ? route('attendance.punch-out') : route('attendance.punch-in') }}">
                    @csrf
                    <button type="submit"
                        class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition {{ $isClockedIn ? 'bg-red-800 hover:bg-red-700' : 'bg-green-800 hover:bg-green-700' }}">
                        {{ $isClockedIn ? 'Check-Out' : 'Check-In' }}
                    </button>
                </form>

                <div class="relative w-4 h-4 rounded-full overflow-hidden shadow">
                    <div
                        class="absolute inset-0 rounded-full transition-opacity duration-300 {{ $isClockedIn ? 'bg-[#32d874] shadow-[0_0_10px_#178a45,0_0_20px_#178a45]' : 'bg-[#d01414]' }}">
                    </div>
                </div>
            </div>

            <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">notifications</span>
                @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
                @endif
            </a>

            <div id="user-dropdown" class="relative">
                <button id="dropdown-toggle" type="button"
                    class="flex items-center justify-center w-8 h-8 bg-gray-800 rounded-full border border-white
                       focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300 dark:focus:ring-gray-600
                       hover:ring-2 hover:ring-offset-1 hover:ring-blue-400 hover:scale-105 transition-transform duration-200">
                    <img class="w-8 h-8 rounded-full object-cover dark:border-gray-600"
                        src="{{ $employee && $employee->image ? asset('storage/' . $employee->image) : asset('default.jpg') }}"
                        alt="Profile Picture" />
                </button>

                <div id="dropdown-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-2 z-50"
                    role="menu">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-300 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <div id="theme-switcher" class="px-4 py-2">
                        <div class="flex justify-between gap-3">
                            <button type="button" data-theme="light"
                                class="theme-btn w-10 aspect-square rounded-full flex items-center justify-center transition-colors text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <span class="material-symbols-outlined text-base">light_mode</span>
                            </button>
                            <button type="button" data-theme="dark"
                                class="theme-btn w-10 aspect-square rounded-full flex items-center justify-center transition-colors text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <span class="material-symbols-outlined text-base">dark_mode</span>
                            </button>
                            <button type="button" data-theme="system"
                                class="theme-btn w-10 aspect-square rounded-full flex items-center justify-center transition-colors text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                                <span class="material-symbols-outlined text-base">monitor</span>
                            </button>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-600 my-2" />

                    <a href="{{ route('settings.profile') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 rounded-md transition">
                        <span class="material-symbols-outlined mr-2 text-base">person</span>
                        Profile
                    </a>
                    <a href="{{ route('settings.password') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 rounded-md transition">
                        <span class="material-symbols-outlined mr-2 text-base">lock_reset</span>
                        Change Password
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600 rounded-md transition">
                            <span class="material-symbols-outlined mr-2 text-base">logout</span>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
