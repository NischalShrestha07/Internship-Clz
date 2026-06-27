 <div class="min-h-screen flex items-center justify-center px-4">
     <div class="w-full max-w-sm bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
         <form class="space-y-6">
             <!-- Logo -->
             <div class="flex justify-center">
                 <img src="{{ asset('itsn.png') }}" alt="logo" class="w-14 sm:w-16">
             </div>
             <div class="text-center">
                 <h1 class="text-lg font-extrabold text-gray-800 dark:text-white">
                     Enter your OTP
                 </h1>
                 <p class="text-sm text-gray-500 dark:text-gray-300">
                     We’ve sent a 6-digit code to your email/phone
                 </p>
             </div>
             <div>
                 <label class="block text-sm font-medium mb-3 text-white dark:text-gray-200 ">
                     Your OTP (6 Digit Code)
                 </label>
                 <div class="flex justify-center gap-2">
                     <input type="text" maxlength="1"
                         class="otp-input w-10 h-10 text-center border rounded-md text-gray-900 dark:text-white
                               bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500" />
                     <input type="text" maxlength="1"
                         class="otp-input w-10 h-10 text-center border rounded-md text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2" />
                     <input type="text" maxlength="1"
                         class="otp-input w-10 h-10 text-center border rounded-md text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2" />
                     <input type="text" maxlength="1"
                         class="otp-input w-10 h-10 text-center border rounded-md text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2" />
                     <input type="text" maxlength="1"
                         class="otp-input w-10 h-10 text-center border rounded-md text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2" />
                     <input type="text" maxlength="1"
                         class="otp-input w-10 h-10 text-center border rounded-md text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2" />
                 </div>
             </div>

             <!-- Submit Button -->
             <button type="submit"
                 class="w-full bg-gray-700 dark:bg-gray-600 text-white font-semibold text-sm py-2 rounded-md
                       hover:bg-gray-800 dark:hover:bg-gray-500 transition duration-200 uppercase">
                 Verify
             </button>
         </form>
     </div>
 </div>
