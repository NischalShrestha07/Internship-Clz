@extends('components.layouts.auth')

@section('content')
<div class="flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 dark:bg-gray-800">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="text-center mb-4">
                <div class="mb-4 flex justify-center">
                    <img src="{{ asset('itsn.png') }}" alt="logo" class="h-16 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Reset Password
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Enter your new password
                </p>
            </div>

            @if (session('status'))
            <div class="text-sm px-4 py-3 rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                {{ session('status') }}
            </div>
            @endif

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required readonly
                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                           dark:text-white" />
                @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                <input type="password" id="password" name="password" required autofocus
                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                           dark:text-white" />
                @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                           dark:text-white" />
            </div>

            <button type="submit"
                class="w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg
                       hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300
                       transition-all duration-200">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection
