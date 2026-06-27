@extends('components.layouts.auth')

@section('content')
<div class="flex items-center justify-center mt-0">
    <div
        class="w-full max-w-xl bg-white rounded-xl shadow-lg p-8 dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="text-center mb-8">
            <div class="mb-4 flex justify-center">
                <img src="{{ asset('itsn.png') }}" alt="ITSN Logo"
                    class="h-16 w-auto transition-transform duration-300 hover:scale-105 shadow-by-force ">
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Welcome back
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Log in to access your account
            </p>
        </div>

        <x-auth-session-status
            class="mb-4 text-center text-sm px-4 py-3 rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300"
            :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required autofocus
                        class="w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                               dark:text-white dark:placeholder-gray-400 transition-all duration-200" />
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                        class="w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                               dark:text-white dark:placeholder-gray-400 transition-all duration-200" />
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                               dark:bg-gray-700 dark:border-gray-600 dark:ring-offset-gray-800" />
                    <label for="remember" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400 transition-colors duration-200">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit"
                class="w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg
                       hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600
                       dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-all duration-200 transform hover:scale-[1.01]">
                Log in
            </button>
        </form>
    </div>
</div>
@endsection
