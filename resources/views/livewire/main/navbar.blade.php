<header class="sticky top-0 z-50 bg-white shadow-md">
    <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
            <img src="{{ asset('itsn.png') }}" alt="HRMS Logo" class="h-12 w-auto rounded-sm">
        </div>

        <!-- Hamburger -->
        <input type="checkbox" id="menu-toggle" class="hidden peer">
        <label for="menu-toggle" class="flex flex-col justify-center items-center w-10 h-10 cursor-pointer md:hidden">
            <span class="block w-8 h-1 bg-gray-800 rounded mb-2"></span>
            <span class="block w-8 h-1 bg-gray-800 rounded mb-2"></span>
            <span class="block w-8 h-1 bg-gray-800 rounded"></span>
        </label>

        <!-- Menu Items -->
        <div
            class="hidden peer-checked:flex md:flex flex-col md:flex-row items-center gap-6 md:gap-8 absolute md:static top-full left-0 w-full md:w-auto bg-white md:bg-transparent shadow-md md:shadow-none px-6 py-4 md:py-0 transition-all duration-300 ease-in-out">
            <a href="#home" class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 font-medium text-lg"
                aria-label="Go to Home">
                <span class="material-symbols-outlined">home</span> Home
            </a>
            <a href="#about" class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 font-medium text-lg"
                aria-label="Go to About Us">
                <span class="material-symbols-outlined">info</span> About Us
            </a>
            <a href="#services" class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 font-medium text-lg"
                aria-label="Go to Services">
                <span class="material-symbols-outlined">business</span> Services
            </a>
            <a href="#contact" class="flex items-center gap-2 text-gray-700 hover:text-indigo-600 font-medium text-lg"
                aria-label="Go to Contact Us">
                <span class="material-symbols-outlined">contact_mail</span> Contact Us
            </a>
        </div>

        <!-- Login Button -->
        <div class="hidden md:flex">
            <a href="{{ route('login') }}"
                class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition font-semibold text-lg"
                aria-label="Log in to HRMS">
                <span class="material-symbols-outlined">login</span> Log In
            </a>
        </div>
        <div class="md:hidden">
            <a href="{{ route('login') }}"
                class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition font-semibold text-lg"
                aria-label="Log in to HRMS">
                <span class="material-symbols-outlined">login</span> Log In
            </a>
        </div>
    </nav>
</header>