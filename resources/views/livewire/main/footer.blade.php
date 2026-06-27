{{--
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<footer class="bg-white text-sm">
    absbsd
</footer> --}}
<footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Company Info -->
            <div>
                <img src="{{ asset('itsn.png') }}" alt="HRMS Logo" class="h-10 w-auto mb-4">
                <p class="text-gray-300 text-sm">
                    Streamline your HR processes with our cutting-edge HRMS solution. Manage attendance, payroll, and
                    employee data effortlessly.
                </p>
            </div>
            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#home" class="hover:text-indigo-400 transition" aria-label="Go to Home">Home</a></li>
                    <li><a href="#about" class="hover:text-indigo-400 transition" aria-label="Go to About Us">About
                            Us</a></li>
                    <li><a href="#services" class="hover:text-indigo-400 transition"
                            aria-label="Go to Services">Services</a></li>
                    <li><a href="#contact" class="hover:text-indigo-400 transition"
                            aria-label="Go to Contact Us">Contact Us</a></li>
                </ul>
            </div>
            <!-- Contact Info -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined">mail</span>
                        <a href="mailto:support@hrms.com" class="hover:text-indigo-400 transition">hrms@gmail.com</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined">phone</span>
                        <a href="tel:9812345678" class="hover:text-indigo-400 transition">9812345678</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined">location_on</span>
                        Biratnagar,Nepal
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm">
            <p>&copy; {{ date('Y') }} HRMS. All rights reserved.</p>
            <p>&copy; Developed by <a href="https://itservicenepal.com/" target="_blank"><span
                        style="color: rgb(195, 36, 36)">ITSN</span></a></p>
        </div>
    </div>
</footer>