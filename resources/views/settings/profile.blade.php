@extends('components.layouts.app')

@section('content')
    <x-headings title="Profile Settings" />

    <x-notifications />

    <div class="max-w-2xl space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
            <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('email') border-red-500 @enderror">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profile Image</label>
                    @if (auth()->user()->employee?->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . auth()->user()->employee->image) }}" alt="Profile"
                                 class="h-20 w-20 object-cover rounded-full border">
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('image') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max 2MB</p>
                    @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Profile
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Delete Account</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted.
                Please enter your current password to confirm.
            </p>
            <form method="POST" action="{{ route('settings.profile.destroy') }}" class="space-y-4"
                  onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('password') border-red-500 @enderror"
                           placeholder="Enter your password to confirm">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete Account
                </button>
            </form>
        </div>
    </div>
@endsection
