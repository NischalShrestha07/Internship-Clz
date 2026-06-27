<div class="flex items-center justify-center px-4">
    <div class="w-full max-w-sm bg-white rounded-lg shadow-md p-6 dark:bg-gray-800 dark:text-white">
        <form>
            <!-- Logo -->
            <div class="logo mb-4">
                <img src="{{ asset('itsn.png') }}" alt="logo" class="mx-auto w-14 sm:w-16"> 
            </div>

            <!-- Heading -->
            <div class="text-center mb-4">
                <h1 class="text-2xl font-semibold">First, let’s find your account</h1>
                <p class="text-sm text-gray-500 dark:text-gray-300">Enter your registered email address below.</p>
            </div>

            <!-- Email Field -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium mb-2">
                    Your email
                </label>
                <input type="email" id="email" name="email" placeholder="name@example.com" required
                    class="w-full px-2 py-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" />
            </div>

            <button type="submit"
                class="w-full bg-gray-700 dark:bg-gray-600 text-white text-sm py-2 rounded-md
           hover:bg-gray-800 dark:hover:bg-gray-500 transition duration-200">
                FIND ACCOUNT
            </button>
            <div class="flex justify-between mt-4 text-sm text-gray-600 dark:text-gray-400">
                <a href="#" class="hover:underline">Log In</a>
            </div>
        </form>
    </div>
</div>

