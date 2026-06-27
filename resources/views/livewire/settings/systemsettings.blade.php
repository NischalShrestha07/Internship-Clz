@php($title = 'Settings')
<div class="flex transition-all duration-400">
    <x-setting-side title=" Settings" />

    <div class="flex-1 px-3 py-2">
        <x-headings title="System Setting" />
        <x-form submitAction="submit" :submitText="$buttonText"
            class="w-full  bg-white dark:bg-gray-800 px-6 py-8 rounded-xl shadow-by-force mx-auto space-y-6">


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="org_name" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                        Name of Organization
                    </label>
                    <input type="text" id="org_name" placeholder="Oraganization Name" wire:model="name" required
                        class="w-full border rounded-lg px-3 py-2 text-black dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                        Title
                    </label>
                    <input type="text" id="title" wire:model="title" required placeholder="Enter system title"
                        class="w-full border rounded-lg px-3 py-2 text-black dark:text-white bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Upload Logo <span class="text-xs text-gray-500 dark:text-gray-400">(PNG only)</span>
                    </label>

                    <div
                        class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 transition duration-200">
                        <input type="file" id="logo" wire:model="logo" accept="image/png"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                        <div class="flex items-center justify-center gap-3 text-gray-500 dark:text-gray-300">
                            <span class="material-symbols-outlined text-3xl">upload</span>
                            <span class="text-sm">Upload</span>
                        </div>
                    </div>

                    @error('logo')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror

                    @if ($logo)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">New Logo Preview:</p>
                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview"
                            class="h-20 w-auto rounded shadow border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 p-1">
                    </div>
                    @elseif ($logoPreview)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Current Logo:</p>
                        <img src="{{ asset('storage/' . $logoPreview) }}" alt="Current Logo"
                            class="h-20 w-auto rounded shadow border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 p-1">
                    </div>
                    @endif
                </div>
            </div>
        </x-form>
    </div>
</div>
</div>
