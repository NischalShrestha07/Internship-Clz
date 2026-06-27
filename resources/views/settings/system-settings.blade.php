@extends('components.layouts.app')

@section('content')
    <x-headings title="System Settings" />

    <x-notifications />

    <div class="flex gap-6">
        <div class="w-64 shrink-0">
            <x-setting-side title="Settings" />
        </div>

        <div class="flex-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <form method="POST" action="{{ route('settings.system') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">System Name</label>
                        <input type="text" name="name" value="{{ old('name', $system->name ?? '') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('name') border-red-500 @enderror"
                               placeholder="e.g. HRMS">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">System Title</label>
                        <input type="text" name="title" value="{{ old('title', $system->title ?? '') }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('title') border-red-500 @enderror"
                               placeholder="e.g. HR Management System">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                        @if ($system->logo ?? false)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $system->logo) }}" alt="Current logo"
                                     class="h-16 w-auto object-contain border rounded">
                            </div>
                        @endif
                        <input type="file" name="logo" accept=".png"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('logo') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG only, max 1MB</p>
                        @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
