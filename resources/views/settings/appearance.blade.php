@extends('components.layouts.app')

@section('content')
    <x-headings title="Appearance Settings" />

    <x-notifications />

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">
                    Theme preferences can be toggled using the theme switcher in the header.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Your theme preference (light/dark) is saved automatically in your browser.
                    Use the <span class="material-symbols-outlined text-base align-text-bottom">dark_mode</span>
                    /
                    <span class="material-symbols-outlined text-base align-text-bottom">light_mode</span>
                    icon in the header to switch between themes.
                </p>
            </div>

            <form method="POST" action="{{ route('settings.appearance') }}" class="mt-6">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Preferences
                </button>
            </form>
        </div>
    </div>
@endsection
