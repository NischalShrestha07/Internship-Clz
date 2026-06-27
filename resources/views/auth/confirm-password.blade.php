@extends('components.layouts.auth')

@section('content')
<div class="flex items-center justify-center mt-0">
    <div
        class="w-full max-w-xl bg-white rounded-xl shadow-lg p-8 dark:bg-gray-800 transition-all duration-300 hover:shadow-xl">
        <div class="text-center mb-8">
            <div class="mb-4 flex justify-center">
                <img src="{{ asset('itsn.png') }}" alt="ITSN Logo"
                    class="h-16 w-auto transition-transform duration-300 hover:scale-105 shadow-by-force">
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Confirm Password
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                This is a secure area. Please confirm your password before continuing.
            </p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
            @csrf
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                           dark:text-white" />
                @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg
                       hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300
                       transition-all duration-200">
                Confirm
            </button>
        </form>
    </div>
</div>
@endsection
