<div class="flex items-start max-md:flex-col bg-gray-100 min-h-screen p-6">

    <!-- Sidebar -->
    <div class="w-56 bg-white shadow-md rounded-lg p-6 flex flex-col">
        <nav class="flex flex-col space-y-4">
            <flux:navlist class="flex flex-col space-y-2">
                <flux:navlist.item :href="route('settings.profile')" wire:navigate
                    class="block px-4 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition"
                    :class="request()->routeIs('settings.profile') ? 'bg-blue-200 text-blue-800 font-semibold' : ''">
                    {{ __('Profile') }}
                </flux:navlist.item>

                <flux:navlist.item :href="route('settings.password')" wire:navigate
                    class="block px-4 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition"
                    :class="request()->routeIs('settings.password') ? 'bg-blue-200 text-blue-800 font-semibold' : ''">
                    {{ __('Password') }}
                </flux:navlist.item>

                {{-- <flux:navlist.item :href="route('settings.appearance')" wire:navigate
                    class="block px-4 py-2 rounded-md text-gray-700 hover:bg-blue-100 hover:text-blue-700 transition"
                    :class="request()->routeIs('settings.appearance') ? 'bg-blue-200 text-blue-800 font-semibold' : ''">
                    {{ __('Appearance') }}
                </flux:navlist.item> --}}
            </flux:navlist>
        </nav>
    </div>

    <!-- Separator (only visible on small screens) -->
    <flux:separator class="md:hidden" />

    <!-- Main content -->
    <div class="flex-1 bg-white rounded-lg shadow-md p-8 ml-6 max-md:ml-0 max-md:mt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>

</div>
