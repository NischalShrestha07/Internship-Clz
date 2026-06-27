<div>
    <x-headings title="Payroll Manager" />
    <x-notifications />

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Employees -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">groups</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Employees</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalEmployees }}</p>
                </div>
            </div>
        </div>

        <!-- Generated Payrolls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">receipt</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Generated Payrolls</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $generatedPayrolls }}</p>
                </div>
            </div>
        </div>

        <!-- Paid Payrolls -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 mr-4">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">payments</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Paid Payrolls</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $paidPayrolls }}</p>
                </div>
            </div>
        </div>

        <!-- Total Salary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 mr-4">
                    <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">savings</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Net Salary</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rs {{ number_format($totalNetSalary)
                        }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">trending_up</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Earnings</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rs {{ number_format($totalEarnings)
                        }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 mr-4">
                    <span class="material-symbols-outlined text-red-600 dark:text-red-400">trending_down</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Deductions</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rs {{
                        number_format($totalDeductions)
                        }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">calendar_month</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payroll Period</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('M Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-5">
            <!-- Month Selector -->
            <div class="ml-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                <select wire:model.live="selectedMonth"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($months as $key => $month)
                    <option value="{{ $key }}">{{ $month }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                <input type="number" wire:model.live="selectedYear" min="2020" max="2030"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Employee</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email..."
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Generate All Button -->
            <div class="flex items-end">
                <button wire:click="generateAllPayroll" wire:loading.attr="disabled"
                    class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors disabled:opacity-50 flex items-center justify-center">
                    <span class="flex items-center">
                        <span class="material-symbols-outlined text-sm mr-2">playlist_add_check</span>
                        Generate All
                    </span>
                </button>
            </div>
            <div class="flex items-end">
                <button wire:click="downloadCSV" class="w-50 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white
                    px-4
                    py-2 rounded-md
                    transition-colors disabled:opacity-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">download</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Present Days</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Overtime Hours
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Net Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($employees as $employee)
                    @php
                    $payroll = $employee->payrolls->first();
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($employee->employee && $employee->employee->image)
                                    <img src="{{ asset('storage/' . $employee->employee->image) }}" alt="Employee Image"
                                        class="h-10 w-10 rounded-full object-cover border border-gray-300">
                                    @else
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $employee->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $employee->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $employee->employee->position ?? 'Employee' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $payroll->present_days ?? '--' }}/{{ $payroll->working_days ?? '26' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $payroll->overtime_hours ?? '--' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            Rs {{ number_format($payroll->basic_salary ?? 0) }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                            Rs {{ number_format($payroll->net_salary ?? 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payroll)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $payroll->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' :
                                           ($payroll->status === 'generated' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100' :
                                           'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100') }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                            @else
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                                Not Generated
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($payroll)
                                <button wire:click="viewPayrollDetails({{ $payroll->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
                                    title="View Details">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </button>
                                <button wire:click="editPayroll({{ $payroll->id }})"
                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center"
                                    title="Edit">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                @if($payroll->status !== 'paid')
                                <button wire:click="markAsPaid({{ $payroll->id }})"
                                    class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 flex items-center"
                                    title="Mark as Paid">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                </button>
                                @endif
                                @else
                                <button wire:click="generatePayroll({{ $employee->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
                                    title="Generate">
                                    <span class="material-symbols-outlined text-sm">add_circle</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No employees found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- Edit Payroll Modal -->
    <x-modal name="edit-payroll">
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl relative">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Payroll</h2>
                        <button x-on:click.prevent="$dispatch('close')" wire:click="cancelEdit"
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
                <div class="p-6">
                    <form wire:submit="updatePayroll">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Working
                                    Days</label>
                                <input type="number" wire:model="editForm.working_days" min="0" max="31"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Present
                                    Days</label>
                                <input type="number" wire:model="editForm.present_days" min="0" max="31"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overtime
                                    Hours</label>
                                <input type="number" wire:model="editForm.overtime_hours" min="0" step="0.5"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave
                                    Days</label>
                                <input type="number" wire:model="editForm.leave_days" min="0" step="0.5"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bonus
                                    (Rs)</label>
                                <input type="number" wire:model="editForm.bonus" min="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Allowances
                                    (Rs)</label>
                                <input type="number" wire:model="editForm.allowances" min="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other
                                    Deductions (Rs)</label>
                                <input type="number" wire:model="editForm.deductions" min="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax
                                    Deduction (Rs)</label>
                                <input type="number" wire:model="editForm.tax_deduction" min="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provident
                                    Fund (Rs)</label>
                                <input type="number" wire:model="editForm.provident_fund" min="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                            <textarea wire:model="editForm.remarks" rows="3"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2"
                                placeholder="Any additional notes..."></textarea>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="cancelEdit"
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-md transition-colors">
                                Update Payroll
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Salary Settings Modal -->
    <x-modal name="salary-settings">
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md relative">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Salary Settings</h2>
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
                <div class="p-6">
                    <form wire:submit="updateSalarySettings">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overtime
                                    Rate (Rs/hour)</label>
                                <input type="number" wire:model="salarySettings.overtime_rate" min="0" step="0.01"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Working
                                    Days per Month</label>
                                <input type="number" wire:model="salarySettings.working_days_per_month" min="20"
                                    max="31"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provident
                                    Fund Rate (%)</label>
                                <input type="number" wire:model="salarySettings.provident_fund_rate" min="0" max="100"
                                    step="0.01"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax
                                    Deduction Rate (%)</label>
                                <input type="number" wire:model="salarySettings.tax_deduction_rate" min="0" max="100"
                                    step="0.01"
                                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" x-on:click.prevent="$dispatch('close')"
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-md transition-colors">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Payroll Details Modal -->
    <x-modal name="payroll-details">
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] relative flex flex-col">
                <!-- Header -->
                <div
                    class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payroll Details</h2>
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
                    @if($this->selectedPayroll)
                    @php
                    $payroll = $this->selectedPayroll;
                    @endphp
                    <div class="space-y-6">
                        <!-- Employee Info -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee Name</p>
                                    <p class="text-lg text-gray-900 dark:text-white">{{ $payroll->employee_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Position</p>
                                    <p class="text-lg text-gray-900 dark:text-white">{{ $payroll->employee->position ??
                                        'Employee' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</p>
                                    <p class="text-lg text-gray-900 dark:text-white">{{
                                        $payroll->payment_date?->format('M d, Y') ?? 'Not Paid' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</p>
                                    <p class="text-lg text-gray-900 dark:text-white">{{ $payroll->payroll_month }}/{{
                                        $payroll->payroll_year }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Breakdown -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Earnings -->
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">Earnings</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Basic Salary</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100">Rs {{
                                            number_format($payroll->basic_salary) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Overtime Pay</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100">Rs {{
                                            number_format($payroll->overtime_pay) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Bonus</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100">Rs {{
                                            number_format($payroll->bonus) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Allowances</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100">Rs {{
                                            number_format($payroll->allowances) }}</span>
                                    </div>
                                    <div class="border-t border-green-200 dark:border-green-700 pt-2 mt-2">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span class="text-green-900 dark:text-green-100">Total Earnings</span>
                                            <span class="text-green-900 dark:text-green-100">Rs {{
                                                number_format($payroll->total_earnings) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deductions & Summary -->
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-4">Deductions</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300">Provident Fund</span>
                                        <span class="font-semibold text-red-900 dark:text-red-100">Rs {{
                                            number_format($payroll->provident_fund) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300">Tax Deduction</span>
                                        <span class="font-semibold text-red-900 dark:text-red-100">Rs {{
                                            number_format($payroll->tax_deduction) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300">Other Deductions</span>
                                        <span class="font-semibold text-red-900 dark:text-red-100">Rs {{
                                            number_format($payroll->deductions) }}</span>
                                    </div>
                                    <div class="border-t border-red-200 dark:border-red-700 pt-2 mt-2">
                                        <div class="flex justify-between text-lg font-bold">
                                            <span class="text-red-900 dark:text-red-100">Total Deductions</span>
                                            <span class="text-red-900 dark:text-red-100">Rs {{
                                                number_format($payroll->total_deductions) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Attendance Summary
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                <div>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">Working Days</p>
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{
                                        $payroll->working_days }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">Present Days</p>
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{
                                        $payroll->present_days }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">Absent Days</p>
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{
                                        $payroll->absent_days }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">Overtime Hours</p>
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{
                                        $payroll->overtime_hours }} hrs</p>
                                </div>
                            </div>
                        </div>

                        <!-- Net Salary -->
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 text-center">
                            <h3 class="text-2xl font-bold text-green-900 dark:text-green-100">Net Salary</h3>
                            <p class="text-4xl font-bold text-green-900 dark:text-green-100 mt-2">Rs {{
                                number_format($payroll->net_salary) }}</p>
                        </div>

                        @if($payroll->remarks)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">Remarks</h3>
                            <p class="text-yellow-800 dark:text-yellow-200">{{ $payroll->remarks }}</p>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">Payroll details not found.</p>
                    </div>
                    @endif
                </div>

                <!-- Footer -->
                <div
                    class="sticky bottom-0 bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
                    <button x-on:click.prevent="$dispatch('close')"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </x-modal>
</div>