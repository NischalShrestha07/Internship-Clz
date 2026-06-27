<div class="flex transition-all duration-400">
    <x-setting-side title="Settings" />

    <div class="flex-1 px-3 py-2">
        <x-headings title="Payroll Settings" />
        <x-notifications />

        <x-form submitAction="submit" submitText="Save Settings" enctype="multipart/form-data"
            class="w-full bg-white dark:bg-gray-800 px-4 py-6 rounded-lg shadow-by-force mx-auto">

            <!-- Basic Settings Section -->
            <div class="mb-8">
                <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold mb-4">
                    Basic Payroll Configuration
                </p>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="overtime_rate"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Overtime Rate (Rs/hour)
                        </label>
                        <input type="number" step="0.01" id="overtime_rate" wire:model="overtime_rate" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hourly rate for overtime calculations
                        </p>
                    </div>

                    <div>
                        <label for="working_days_per_month"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Working Days per Month
                        </label>
                        <input type="number" id="working_days_per_month" wire:model="working_days_per_month" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Standard working days in a month</p>
                    </div>

                    <div>
                        <label for="provident_fund_rate"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Provident Fund Rate (%)
                        </label>
                        <input type="number" step="0.01" id="provident_fund_rate" wire:model="provident_fund_rate"
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Percentage of basic salary for PF
                            deduction</p>
                    </div>

                    <div>
                        <label for="tax_deduction_rate"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Tax Deduction Rate (%)
                        </label>
                        <input type="number" step="0.01" id="tax_deduction_rate" wire:model="tax_deduction_rate" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2
                            text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800
                            focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Percentage for tax deductions</p>
                    </div>
                </div>
            </div>

            <!-- Salary Components Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold">
                        Salary Components
                    </p>
                    <button type="button" wire:click="showAddSalaryModal"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">add</span>
                        Add Component
                    </button>
                </div>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Component</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Rate</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($salary_components as $index => $component)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $component['label'] }}
                                </td>
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white capitalize">
                                    {{ $component['type'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $component['rate'] }} {{ $component['type'] === 'percentage' ? '%' : '(Rs)' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button type="button" wire:click="toggleSalaryComponent({{ $index }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                        {{ $component['is_active'] ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                        <span class="sr-only">Toggle component</span>
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                                            {{ $component['is_active'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <button type="button" wire:click="removeSalaryComponent({{ $index }})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No salary components configured
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Deduction Components Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-gray-700 dark:text-gray-200 text-lg font-semibold">
                        Deduction Components
                    </p>
                    <button type="button" wire:click="showAddDeductionModal"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">add</span>
                        Add Deduction
                    </button>
                </div>
                <hr class="w-full border-gray-300 dark:border-gray-600 mb-4" />

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Deduction</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Rate</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($deduction_components as $index => $component)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $component['label'] }}
                                </td>
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white capitalize">
                                    {{ $component['type'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $component['rate'] }} {{ $component['type'] === 'percentage' ? '%' : '(Rs)' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button type="button" wire:click="toggleDeductionComponent({{ $index }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                        {{ $component['is_active'] ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                        <span class="sr-only">Toggle component</span>
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                                            {{ $component['is_active'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <button type="button" wire:click="removeDeductionComponent({{ $index }})"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No deduction components configured
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-form>

        <!-- Add Salary Component Modal -->
        @if($showSalaryModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Add Salary Component</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Name
                                (Technical)</label>
                            <input type="text" wire:model="new_salary_component.name"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white"
                                placeholder="e.g., house_rent_allowance">
                            @error('new_salary_component.name') <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Display
                                Label</label>
                            <input type="text" wire:model="new_salary_component.label"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white"
                                placeholder="e.g., House Rent Allowance">
                            @error('new_salary_component.label') <span class="text-red-500 text-xs">{{ $message
                                }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type</label>
                            <select wire:model="new_salary_component.type"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage of Basic</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Rate ({{ $new_salary_component['type'] === 'percentage' ? '%' : 'Rs' }})
                            </label>
                            <input type="number" step="0.01" wire:model="new_salary_component.rate"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                            @error('new_salary_component.rate') <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showSalaryModal', false)"
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="addSalaryComponent"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-md transition-colors">
                            Add Component
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Add Deduction Component Modal -->
        @if($showDeductionModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Add Deduction Component</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Name
                                (Technical)</label>
                            <input type="text" wire:model="new_deduction_component.name"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white"
                                placeholder="e.g., professional_tax">
                            @error('new_deduction_component.name') <span class="text-red-500 text-xs">{{ $message
                                }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Display
                                Label</label>
                            <input type="text" wire:model="new_deduction_component.label"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white"
                                placeholder="e.g., Professional Tax">
                            @error('new_deduction_component.label') <span class="text-red-500 text-xs">{{ $message
                                }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type</label>
                            <select wire:model="new_deduction_component.type"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage of Basic</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Rate ({{ $new_deduction_component['type'] === 'percentage' ? '%' : 'Rs' }})
                            </label>
                            <input type="number" step="0.01" wire:model="new_deduction_component.rate"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-700 dark:text-white">
                            @error('new_deduction_component.rate') <span class="text-red-500 text-xs">{{ $message
                                }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showDeductionModal', false)"
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="addDeductionComponent"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-md transition-colors">
                            Add Deduction
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>