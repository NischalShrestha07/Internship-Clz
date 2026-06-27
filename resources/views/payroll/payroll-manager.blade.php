@extends('components.layouts.app')

@section('content')
<x-headings title="Payroll Manager" />
<x-notifications />

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
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
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 mr-4">
                <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">savings</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Net Salary</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rs {{ number_format($totalNetSalary) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                <span class="material-symbols-outlined text-green-600 dark:text-green-400">trending_up</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Earnings</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rs {{ number_format($totalEarnings) }}</p>
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
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rs {{ number_format($totalDeductions) }}</p>
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
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('M Y') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('payroll.manager') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                <select name="selectedMonth" onchange="this.form.submit()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($months as $key => $month)
                    <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                <input type="number" name="selectedYear" value="{{ $selectedYear }}" min="2020" max="2030" onchange="this.form.submit()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Employee</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..."
                    onkeyup="clearTimeout(window.searchTimeout); window.searchTimeout=setTimeout(()=>this.form.submit(),500)"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white px-4 py-2 rounded-md transition-colors text-sm">
                    Search
                </button>
            </div>
        </div>
    </form>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-4">
        <form method="POST" action="{{ route('payroll.generate-all') }}">
            @csrf
            <input type="hidden" name="selectedMonth" value="{{ $selectedMonth }}">
            <input type="hidden" name="selectedYear" value="{{ $selectedYear }}">
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors text-sm flex items-center justify-center">
                <span class="material-symbols-outlined text-sm mr-2">playlist_add_check</span>
                Generate All
            </button>
        </form>
        <a href="{{ route('payroll.csv', ['selectedMonth' => $selectedMonth, 'selectedYear' => $selectedYear]) }}"
            class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors text-sm flex items-center justify-center">
            <span class="material-symbols-outlined text-sm mr-2">download</span>
            Download CSV
        </a>
        <button type="button" onclick="openSalarySettings()"
            class="w-full bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-md transition-colors text-sm flex items-center justify-center">
            <span class="material-symbols-outlined text-sm mr-2">settings</span>
            Salary Settings
        </button>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-blue-600 dark:bg-blue-900 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Present Days</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Overtime Hours</th>
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
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $employee->employee->position ?? 'Employee' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $payroll->present_days ?? '--' }}/{{ $payroll->working_days ?? '26' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $payroll->overtime_hours ?? '--' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Rs {{ number_format($payroll->basic_salary ?? 0) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">Rs {{ number_format($payroll->net_salary ?? 0) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($payroll)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $payroll->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' :
                               ($payroll->status === 'generated' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100' :
                               'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100') }}">
                            {{ ucfirst($payroll->status) }}
                        </span>
                        @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">Not Generated</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @if($payroll)
                            <button type="button" onclick="viewPayrollDetails({{ $payroll->id }})"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center" title="View Details">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </button>
                            <button type="button" onclick="editPayroll({{ $payroll->id }})"
                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 flex items-center" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            @if($payroll->status !== 'paid')
                            <form method="POST" action="{{ route('payroll.mark-paid', $payroll->id) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 flex items-center" title="Mark as Paid">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                </button>
                            </form>
                            @endif
                            @else
                            <form method="POST" action="{{ route('payroll.generate') }}" class="inline">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <input type="hidden" name="selectedMonth" value="{{ $selectedMonth }}">
                                <input type="hidden" name="selectedYear" value="{{ $selectedYear }}">
                                <button type="submit"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 flex items-center" title="Generate">
                                    <span class="material-symbols-outlined text-sm">add_circle</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
        {{ $employees->links() }}
    </div>
</div>

<div x-data="{ open: false }"
    x-on:open-edit-modal.window="open = true"
    x-on:close-edit-modal.window="open = false"
    x-show="open"
    class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4"
    style="display: none;">
    <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl relative">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Payroll</h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form method="POST" action="" id="editForm">
                @csrf
                <input type="hidden" id="edit_payroll_id" name="payroll_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Working Days</label>
                        <input type="number" id="edit_working_days" name="working_days" min="0" max="31"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Present Days</label>
                        <input type="number" id="edit_present_days" name="present_days" min="0" max="31"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overtime Hours</label>
                        <input type="number" id="edit_overtime_hours" name="overtime_hours" min="0" step="0.5"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave Days</label>
                        <input type="number" id="edit_leave_days" name="leave_days" min="0" step="0.5"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bonus (Rs)</label>
                        <input type="number" id="edit_bonus" name="bonus" min="0"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Allowances (Rs)</label>
                        <input type="number" id="edit_allowances" name="allowances" min="0"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other Deductions (Rs)</label>
                        <input type="number" id="edit_deductions" name="deductions" min="0"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Deduction (Rs)</label>
                        <input type="number" id="edit_tax_deduction" name="tax_deduction" min="0"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provident Fund (Rs)</label>
                        <input type="number" id="edit_provident_fund" name="provident_fund" min="0"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea id="edit_remarks" name="remarks" rows="3"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2"
                        placeholder="Any additional notes..."></textarea>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="open = false"
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

<div x-data="{ open: false, details: null }"
    x-on:open-details-modal.window="open = true; details = $event.detail"
    x-on:close-details-modal.window="open = false; details = null"
    x-show="open"
    class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4"
    style="display: none;">
    <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] relative flex flex-col">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payroll Details</h2>
                <button @click="open = false"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <template x-if="details">
                <div class="space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Employee Name</p>
                                <p class="text-lg text-gray-900 dark:text-white" x-text="details.employee_name"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Position</p>
                                <p class="text-lg text-gray-900 dark:text-white" x-text="details.position"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</p>
                                <p class="text-lg text-gray-900 dark:text-white" x-text="details.payment_date"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</p>
                                <p class="text-lg text-gray-900 dark:text-white" x-text="details.period"></p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">Earnings</h3>
                            <div class="space-y-3">
                                <template x-for="item in (details.salary_breakdown || [])" :key="item.label">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300" x-text="item.label"></span>
                                        <span class="font-semibold text-green-900 dark:text-green-100" x-text="'Rs ' + Number(item.amount).toLocaleString()"></span>
                                    </div>
                                </template>
                                <template x-if="details.overtime_pay > 0">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Overtime Pay</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100" x-text="'Rs ' + Number(details.overtime_pay).toLocaleString()"></span>
                                    </div>
                                </template>
                                <template x-if="details.bonus > 0">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Bonus</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100" x-text="'Rs ' + Number(details.bonus).toLocaleString()"></span>
                                    </div>
                                </template>
                                <div class="border-t border-green-200 dark:border-green-700 pt-2 mt-2">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-green-900 dark:text-green-100">Total Earnings</span>
                                        <span class="text-green-900 dark:text-green-100" x-text="'Rs ' + Number(details.total_earnings).toLocaleString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-4">Deductions</h3>
                            <div class="space-y-3">
                                <template x-for="item in (details.deduction_breakdown || [])" :key="item.label">
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300" x-text="item.label"></span>
                                        <span class="font-semibold text-red-900 dark:text-red-100" x-text="'Rs ' + Number(item.amount).toLocaleString()"></span>
                                    </div>
                                </template>
                                <template x-if="details.deductions > 0">
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300">Other Deductions</span>
                                        <span class="font-semibold text-red-900 dark:text-red-100" x-text="'Rs ' + Number(details.deductions).toLocaleString()"></span>
                                    </div>
                                </template>
                                <div class="border-t border-red-200 dark:border-red-700 pt-2 mt-2">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-red-900 dark:text-red-100">Total Deductions</span>
                                        <span class="text-red-900 dark:text-red-100" x-text="'Rs ' + Number(details.total_deductions).toLocaleString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Attendance Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Working Days</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100" x-text="details.working_days"></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Present Days</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100" x-text="details.present_days"></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Absent Days</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100" x-text="details.absent_days"></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Overtime Hours</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100" x-text="details.overtime_hours + ' hrs'"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 text-center">
                        <h3 class="text-2xl font-bold text-green-900 dark:text-green-100">Net Salary</h3>
                        <p class="text-4xl font-bold text-green-900 dark:text-green-100 mt-2" x-text="'Rs ' + Number(details.net_salary).toLocaleString()"></p>
                    </div>
                    <template x-if="details.remarks">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">Remarks</h3>
                            <p class="text-yellow-800 dark:text-yellow-200" x-text="details.remarks"></p>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="!details">
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Payroll details not found.</p>
                </div>
            </template>
        </div>
        <div class="sticky bottom-0 bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 rounded-b-xl flex justify-end">
            <button @click="open = false"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<div x-data="{ open: false, settings: null }"
    x-on:open-salary-settings.window="open = true"
    x-on:close-salary-settings.window="open = false"
    x-show="open"
    class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4"
    style="display: none;">
    <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md relative">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Salary Settings</h2>
                <button @click="open = false"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('payroll.salary-settings.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Overtime Rate (Rs/hour)</label>
                        <input type="number" id="settings_overtime_rate" name="overtime_rate" min="0" step="0.01"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Working Days per Month</label>
                        <input type="number" id="settings_working_days" name="working_days_per_month" min="20" max="31"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provident Fund Rate (%)</label>
                        <input type="number" id="settings_pf_rate" name="provident_fund_rate" min="0" max="100" step="0.01"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Deduction Rate (%)</label>
                        <input type="number" id="settings_tax_rate" name="tax_deduction_rate" min="0" max="100" step="0.01"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="open = false"
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
@endsection

<script>
function viewPayrollDetails(payrollId) {
    fetch('/payroll/' + payrollId + '/details')
        .then(r => r.json())
        .then(data => {
            window.dispatchEvent(new CustomEvent('open-details-modal', { detail: data }));
        })
        .catch(() => {
            showNotification('Error loading payroll details', 'error');
        });
}

function editPayroll(payrollId) {
    fetch('/payroll/' + payrollId + '/edit')
        .then(r => r.json())
        .then(data => {
            document.getElementById('editForm').action = '/payroll/' + payrollId + '/update';
            document.getElementById('edit_payroll_id').value = payrollId;
            document.getElementById('edit_working_days').value = data.working_days;
            document.getElementById('edit_present_days').value = data.present_days;
            document.getElementById('edit_overtime_hours').value = data.overtime_hours;
            document.getElementById('edit_leave_days').value = data.leave_days;
            document.getElementById('edit_bonus').value = data.bonus;
            document.getElementById('edit_allowances').value = data.allowances;
            document.getElementById('edit_deductions').value = data.deductions;
            document.getElementById('edit_tax_deduction').value = data.tax_deduction;
            document.getElementById('edit_provident_fund').value = data.provident_fund;
            document.getElementById('edit_remarks').value = data.remarks || '';
            window.dispatchEvent(new CustomEvent('open-edit-modal'));
        })
        .catch(() => {
            showNotification('Error loading payroll data', 'error');
        });
}

function openSalarySettings() {
    fetch('/payroll/salary-settings')
        .then(r => r.json())
        .then(data => {
            document.getElementById('settings_overtime_rate').value = data.overtime_rate;
            document.getElementById('settings_working_days').value = data.working_days_per_month;
            document.getElementById('settings_pf_rate').value = data.provident_fund_rate;
            document.getElementById('settings_tax_rate').value = data.tax_deduction_rate;
            window.dispatchEvent(new CustomEvent('open-salary-settings'));
        })
        .catch(() => {
            showNotification('Error loading salary settings', 'error');
        });
}
</script>
