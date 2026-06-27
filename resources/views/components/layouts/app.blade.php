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
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

    {{-- Header (fixed at top) --}}
    <div id="header-wrapper" class="top-0 left-0 md:left-[264px] z-50 w-full md:w-[calc(100%-264px)]">
        @include('partials.header')
    </div>

    {{-- Sidebar (fixed on the left) --}}
    <div class="fixed top-20 left-0 z-40 w-[264px] h-[calc(100vh-5rem)] overflow-y-auto">
        @include('partials.sidebar')
    </div>

    {{-- Main content (with margin offset for header and sidebar) --}}
    <div id="main-content" class="pt-20 p-4 transition-all duration-300 ml-0 md:ml-[264px]">
        @yield('content')
    </div>

    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @stack('scripts')
</body>

</html>
