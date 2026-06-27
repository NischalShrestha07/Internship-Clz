<div x-data="headerComponent()" x-init="init"
    class="w-full fixed top-0 left-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between h-16 px-4">

        <!-- Left: Logo + Sidebar Toggle -->
        <div class="flex items-center gap-2">
            <!-- Sidebar Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 rounded dark:bg-gray-800 text-black hover:bg-gray-400 transition dark:hover:bg-gray-700 dark:text-white">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <!-- Logo & Title -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ $system_info && $system_info->logo ? asset('storage/' . $system_info->logo) : asset('1.png') }}"
                    alt="{{ $system_info->name ?? 'Default Logo' }}" class="h-10 w-10 rounded-sm" />

                <h1 class="text-2xl font-bold text-black dark:text-white sm:block">
                    {{ $system_info->name ?? 'Organization' }}
                </h1>
            </a>
        </div>

        <!-- Right: Tools -->
        <div class="flex items-center gap-5">
            <!-- Check In / Out -->
            <div x-data="{
                isClockedIn: @js($isClockedIn ?? false),
                loading: false,
                init() {
                    // Initialize from Livewire state
                    this.isClockedIn = @js($isClockedIn ?? false);

                    window.addEventListener('update-attendance-status', (event) => {
                        this.isClockedIn = event.detail.isClockedIn;
                        localStorage.setItem('clockedIn', this.isClockedIn);
                    });
                },
                async toggleAttendance() {
                    this.loading = true;
                    try {
                        if (this.isClockedIn) {
                            await $wire.punchOut();
                            this.isClockedIn = false; // Update state immediately
                        } else {
                            await $wire.punchIn();
                            this.isClockedIn = true; // Update state immediately
                        }
                        localStorage.setItem('clockedIn', this.isClockedIn);
                    } catch (error) {
                        console.error('Error:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }" class="flex items-center gap-2">
                <button @click="toggleAttendance" :disabled="loading" :class="{
                        'bg-red-800 hover:bg-red-700': isClockedIn,
                        'bg-green-800 hover:bg-green-700': !isClockedIn,
                        'opacity-50 cursor-not-allowed': loading
                    }" class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition">
                    <span
                        x-text="isClockedIn ? (loading ? 'Processing...' : 'Check-Out') : (loading ? 'Processing...' : 'Check-In')">
                    </span>
                </button>

                <!-- Glow Light -->
                <div class="relative w-4 h-4 rounded-full overflow-hidden shadow"
                    :class="isClockedIn ? 'bg-[#32d874]' : 'bg-[#d01414]'">
                    <span x-show="isClockedIn"
                        class="absolute inset-0 rounded-full bg-[#32d874] pointer-events-none shadow-[0_0_10px_#178a45,0_0_20px_#178a45] transition-opacity duration-300">
                    </span>
                    <span x-show="!isClockedIn"
                        class="absolute inset-0 rounded-full bg-[#d01414] pointer-events-none shadow-[0_0_10px_#d01414,0_0_20px_#d01414] transition-opacity duration-300">
                    </span>
                </div>
            </div>


            <!-- Search Bar -->
            {{-- <div class="relative hidden md:block w-64">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <span class="material-symbols-outlined text-gray-400">search</span>
                </div>
                <input type="text"
                    class="block w-full ps-10 py-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Search..." />
            </div> --}}

            <!-- User Dropdown -->
            <div class="relative" x-data="{
                open: JSON.parse(localStorage.getItem('dropdownOpen')) || false,
                toggle() {
                    this.open = !this.open;
                    localStorage.setItem('dropdownOpen', JSON.stringify(this.open));
                },
                close() {
                    this.open = false;
                    localStorage.setItem('dropdownOpen', 'false');
                }
            }">
                <!-- Profile Image Button -->
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" class="flex items-center justify-center w-8 h-8 bg-gray-800 rounded-full border border-white
           focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300 dark:focus:ring-gray-600
           shadow-by-force
           hover:ring-2 hover:ring-offset-1 hover:ring-blue-400 hover:scale-105 transition-transform duration-200"
                        aria-expanded="open" aria-haspopup="true" aria-label="Open user menu">

                        <img class="w-8 h-8 rounded-full object-cover dark:border-gray-600"
                            src="{{ $employee && $employee->image ? asset('storage/' . $employee->image) : asset('default.jpg') }}"
                            alt="Profile Picture" />
                    </button>


                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false" @keydown.escape.window="open = false" x-transition
                        x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-2 z-50"
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">

                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-300 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Theme Switcher -->
                        <div class="px-4 py-2" x-data="{
                            theme: localStorage.getItem('theme') || 'system',
                            init() {
                                this.theme = localStorage.getItem('theme') || 'system';
                                this.setTheme(this.theme);
                                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                                    if (this.theme === 'system') this.setTheme('system');
                                });
                            },
                            setTheme(selectedTheme) {
                                this.theme = selectedTheme;
                                localStorage.setItem('theme', selectedTheme);
                                const isDark = selectedTheme === 'dark' || (selectedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                                document.documentElement.classList.toggle('dark', isDark);
                            }
                        }">
                            <div class="flex justify-between gap-3">
                                <button type="button" @click="setTheme('light'); open = false" :class="[
                                        'w-10 aspect-square rounded-full flex items-center justify-center transition-colors',
                                        theme === 'light' ?
                                        'text-yellow-500 bg-gray-200 dark:bg-gray-600' :
                                        'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    ]" title="Light">
                                    <span class="material-symbols-outlined text-base">light_mode</span>
                                </button>

                                <button type="button" @click="setTheme('dark'); open = false" :class="[
                                        'w-10 aspect-square rounded-full flex items-center justify-center transition-colors',
                                        theme === 'dark' ?
                                        'text-yellow-500 bg-gray-200 dark:bg-gray-600' :
                                        'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    ]" title="Dark">
                                    <span class="material-symbols-outlined text-base">dark_mode</span>
                                </button>

                                <button type="button" @click="setTheme('system'); open = false" :class="[
                                        'w-10 aspect-square rounded-full flex items-center justify-center transition-colors',
                                        theme === 'system' ?
                                        'text-yellow-500 bg-gray-200 dark:bg-gray-600' :
                                        'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                    ]" title="System">
                                    <span class="material-symbols-outlined text-base">monitor</span>
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-600 my-2" />

                        <!-- Links -->
                        <a wire:navigate href="{{ route('settings.profile') }}" role="menuitem" @click="open = false"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 rounded-md transition">
                            <span class="material-symbols-outlined mr-2 text-base">person</span>
                            Profile
                        </a>
                        <a wire:navigate href="{{ route('settings.password') }}" role="menuitem" @click="open = false"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 rounded-md transition">
                            <span class="material-symbols-outlined mr-2 text-base">lock_reset</span>
                            Change Password
                        </a>


                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" @click="open = false"
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
</div>