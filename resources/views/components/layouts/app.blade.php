<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        {{ $title ?? 'HRMS' }} || ITSN
    </title>

    @vite('resources/css/app.css')


    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="icon" href="{{ asset('1.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @livewireStyles()
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white" x-data="{ sidebarOpen: true }">

    {{-- Header (fixed at top) --}}
    <div class=" top-0 left-[264px] z-50 w-[calc(100%-264px)]">
        @livewire('app.header')
    </div>

    {{-- Sidebar (fixed on the left) --}}
    <div class="fixed top-20 left-0 z-40 w-[264px] h-[calc(100vh-5rem)] overflow-y-auto">
        @livewire('app.sidebar')
    </div>

    {{-- Main content (with margin offset for header and sidebar) --}}
    <div class="pt-20 p-4 transition-all duration-300" :class="sidebarOpen ? 'ml-[264px]' : 'ml-0'">
        {{ $slot }}
    </div>

    @livewireScripts()
</body>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</html>
