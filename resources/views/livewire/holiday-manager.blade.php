<div>
    <x-headings title="Manage Holidays" buttonModal="view-holiday" buttonText="+ Add Holiday" />
    <x-notifications />

    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        <!-- Holiday List -->
        <div class="overflow-x-auto">
            <x-table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Title</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date Range</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Duration</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Recurring</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($holidays as $holiday)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $holiday->title }}
                            </div>
                            @if($holiday->description)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $holiday->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $holiday->start_date->format('M d, Y') }} -
                            {{ $holiday->end_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $holiday->start_date->diffInDays($holiday->end_date) + 1 }} days
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($holiday->is_recurring)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                {{ ucfirst($holiday->recurrence_pattern) }}
                            </span>
                            @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                One-time
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($holiday->is_active)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Active
                            </span>
                            @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-3">

                                <button
                                    class="w-6 h-6 flex items-center justify-center text-blue-500 hover:bg-gray-200 rounded"
                                    wire:click="editHolidayModal({{ $holiday['id'] }})">
                                    <span class="material-symbols-outlined text-xs leading-none">edit</span>
                                </button>
                                <button wire:click="deleteHolidayModal('{{ $holiday['id'] }}', 'viewSessions')"
                                    class="w-6 h-6 flex items-center justify-center text-red-500 hover:bg-gray-200 rounded mx-auto">
                                    <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No holidays found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <x-modal name="view-holiday" :show="$showModal" focusable>
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] relative flex flex-col">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $editingId ? 'Edit Holiday' : 'Add New Holiday' }}
                        </h2>
                        <button x-on:click.prevent="$dispatch('close')"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <form wire:submit.prevent="saveHoliday" class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                            <input type="text" wire:model="title" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    bg-white dark:bg-gray-700
                                    border-gray-300 dark:border-gray-600
                                    text-gray-900 dark:text-white
                                    placeholder-gray-400 dark:placeholder-gray-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description
                                (Optional)</label>
                            <textarea wire:model="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    bg-white dark:bg-gray-700
                                    border-gray-300 dark:border-gray-600
                                    text-gray-900 dark:text-white
                                    placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                    Date</label>
                                <input type="date" wire:model="start_date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                    Date</label>
                                <input type="date" wire:model="end_date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Recurring Options -->
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_recurring" id="is_recurring"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                <label for="is_recurring"
                                    class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Recurring
                                    Holiday</label>
                            </div>

                            @if($is_recurring)
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recurrence
                                    Pattern</label>
                                <select wire:model="recurrence_pattern" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500
                                        bg-white dark:bg-gray-700
                                        border-gray-300 dark:border-gray-600
                                        text-gray-900 dark:text-white">
                                    <option value="yearly">Yearly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                            </div>
                            @endif
                        </div>

                        <!-- Status -->
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="is_active" id="is_active"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Active
                                Holiday</label>
                        </div>

                        <!-- Footer -->
                        <div
                            class="sticky bottom-0 bg-white dark:bg-gray-800 pt-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end gap-3">
                            <button type="button" x-on:click.prevent="$dispatch('close')"
                                class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 dark:bg-blue-700 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200">
                                {{ $editingId ? 'Update Holiday' : 'Add Holiday' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>


    <x-modal name="delete-holiday" :show="$errors->isNotEmpty()" focusable>
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 px-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white text-center w-full">
                        Delete Holiday?
                    </h2>
                    <button x-on:click.prevent="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <p class="text-center text-gray-700 dark:text-gray-200 text-lg mb-6">
                    Are you sure you want to delete this holiday?
                </p>

                <!-- Actions -->
                <div class="flex justify-center gap-4">
                    <button type="button" wire:click="confirmDeleteHoliday({{ $holiday->id ?? '' }})"
                        class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 transition-colors">
                        Delete
                    </button>

                    <button type="button" x-on:click.prevent="$dispatch('close')"
                        class="bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 px-5 py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </x-modal>
</div>