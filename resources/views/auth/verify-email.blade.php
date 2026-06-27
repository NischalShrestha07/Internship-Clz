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
                Verify Email
            </h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Please verify your email address by clicking the link we just emailed to you.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
        <div
            class="mb-4 text-center text-sm px-4 py-3 rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
            A new verification link has been sent to the email address you provided.
        </div>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg
                           hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300
                           transition-all duration-200">
                    Resend verification email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-blue-600 hover:underline dark:text-blue-400 cursor-pointer">
                    Log out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
