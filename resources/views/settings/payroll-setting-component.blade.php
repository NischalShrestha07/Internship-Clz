@extends('components.layouts.app')

@section('content')
    <x-headings title="Payroll Settings" />

    <x-notifications />

    <div class="flex gap-6">
        <div class="w-64 shrink-0">
            <x-setting-side title="Settings" />
        </div>

        <div class="flex-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <form method="POST" action="{{ route('payroll.setting') }}" class="space-y-4"
                      x-data="{
                          salaryComponents: @js($settings->salary_components ?? []),
                          deductionComponents: @js($settings->deduction_components ?? []),
                          showSalaryModal: false,
                          showDeductionModal: false,
                          newComponent: { name: '', label: '', type: 'fixed', rate: 0, is_active: true },

                          addSalaryComponent() {
                              this.newComponent.name = this.newComponent.label.toLowerCase().replace(/\\s+/g, '_');
                              this.salaryComponents.push({ ...this.newComponent });
                              this.newComponent = { name: '', label: '', type: 'fixed', rate: 0, is_active: true };
                              this.showSalaryModal = false;
                          },

                          removeSalaryComponent(index) {
                              this.salaryComponents.splice(index, 1);
                          },

                          toggleSalaryComponent(index) {
                              this.salaryComponents[index].is_active = !this.salaryComponents[index].is_active;
                          },

                          addDeductionComponent() {
                              this.newComponent.name = this.newComponent.label.toLowerCase().replace(/\\s+/g, '_');
                              this.deductionComponents.push({ ...this.newComponent });
                              this.newComponent = { name: '', label: '', type: 'fixed', rate: 0, is_active: true };
                              this.showDeductionModal = false;
                          },

                          removeDeductionComponent(index) {
                              this.deductionComponents.splice(index, 1);
                          },

                          toggleDeductionComponent(index) {
                              this.deductionComponents[index].is_active = !this.deductionComponents[index].is_active;
                          },

                          submitForm() {
                              document.getElementById('salary_components_input').value = JSON.stringify(this.salaryComponents);
                              document.getElementById('deduction_components_input').value = JSON.stringify(this.deductionComponents);
                          }
                      }"
                      @submit.prevent="submitForm(); $el.submit()">
                    @csrf

                    <input type="hidden" id="salary_components_input" name="salary_components" value="">
                    <input type="hidden" id="deduction_components_input" name="deduction_components" value="">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overtime Rate</label>
                        <input type="number" name="overtime_rate" value="{{ old('overtime_rate', $settings->overtime_rate ?? 200) }}" step="0.01" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('overtime_rate') border-red-500 @enderror">
                        @error('overtime_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Working Days Per Month</label>
                        <input type="number" name="working_days_per_month" value="{{ old('working_days_per_month', $settings->working_days_per_month ?? 26) }}" min="20" max="31" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('working_days_per_month') border-red-500 @enderror">
                        @error('working_days_per_month') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provident Fund Rate (%)</label>
                        <input type="number" name="provident_fund_rate" value="{{ old('provident_fund_rate', $settings->provident_fund_rate ?? 12) }}" step="0.01" min="0" max="100" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('provident_fund_rate') border-red-500 @enderror">
                        @error('provident_fund_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Deduction Rate (%)</label>
                        <input type="number" name="tax_deduction_rate" value="{{ old('tax_deduction_rate', $settings->tax_deduction_rate ?? 5) }}" step="0.01" min="0" max="100" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white @error('tax_deduction_rate') border-red-500 @enderror">
                        @error('tax_deduction_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Save Settings
                    </button>
                </form>
            </div>

            {{-- Salary Components --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Salary Components</h3>
                    <button type="button" @click="showSalaryModal = true"
                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        Add Salary Component
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Label</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Active</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(comp, index) in salaryComponents" :key="index">
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.name"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.label"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.type"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.rate"></td>
                                    <td class="px-4 py-2 text-sm">
                                        <button type="button" @click="toggleSalaryComponent(index)"
                                                class="px-2 py-1 text-xs rounded"
                                                :class="comp.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                                x-text="comp.is_active ? 'Active' : 'Inactive'">
                                        </button>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <button type="button" @click="removeSalaryComponent(index)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="salaryComponents.length === 0">
                                <td colspan="6" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No salary components defined.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Deduction Components --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg dark:border-gray-700 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Deduction Components</h3>
                    <button type="button" @click="showDeductionModal = true"
                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        Add Deduction Component
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Label</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rate</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Active</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(comp, index) in deductionComponents" :key="index">
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.name"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.label"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.type"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-200" x-text="comp.rate"></td>
                                    <td class="px-4 py-2 text-sm">
                                        <button type="button" @click="toggleDeductionComponent(index)"
                                                class="px-2 py-1 text-xs rounded"
                                                :class="comp.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                                x-text="comp.is_active ? 'Active' : 'Inactive'">
                                        </button>
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <button type="button" @click="removeDeductionComponent(index)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="deductionComponents.length === 0">
                                <td colspan="6" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No deduction components defined.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Salary Component Modal --}}
    <div x-show="showSalaryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4" @click.away="showSalaryModal = false">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">New Salary Component</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                    <input type="text" x-model="newComponent.label"
                           class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
                           placeholder="e.g. House Rent Allowance">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select x-model="newComponent.type"
                            class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate</label>
                    <input type="number" x-model="newComponent.rate" step="0.01"
                           class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" x-model="newComponent.is_active" id="newSalaryActive">
                    <label for="newSalaryActive" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showSalaryModal = false"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="button" @click="addSalaryComponent"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Deduction Component Modal --}}
    <div x-show="showDeductionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4" @click.away="showDeductionModal = false">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">New Deduction Component</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                    <input type="text" x-model="newComponent.label"
                           class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white"
                           placeholder="e.g. Professional Tax">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select x-model="newComponent.type"
                            class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rate</label>
                    <input type="number" x-model="newComponent.rate" step="0.01"
                           class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" x-model="newComponent.is_active" id="newDeductionActive">
                    <label for="newDeductionActive" class="text-sm text-gray-700 dark:text-gray-300">Active</label>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showDeductionModal = false"
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="button" @click="addDeductionComponent"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
