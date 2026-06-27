@extends('components.layouts.app')

@section('content')
<x-headings title="My Payslips" />
<x-notifications />

<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
    <form method="GET" action="{{ route('payroll.my-payslips') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <select name="selectedMonth" onchange="this.form.submit()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Months</option>
                    @foreach($months as $key => $month)
                    <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <input type="number" name="selectedYear" value="{{ $selectedYear }}" min="2020" max="2030" onchange="this.form.submit()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg transition-colors border border-gray-300 dark:border-gray-600">
                    Filter
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('payroll.my-payslips') }}"
                    class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg transition-colors border border-gray-300 dark:border-gray-600 text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($payslips as $payslip)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-white font-bold text-lg">Salary Slip</h3>
                    <p class="text-blue-100 text-sm">{{ $payslip->period }}</p>
                </div>
                <span class="bg-green bg-opacity-20 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    {{ strtoupper($payslip->status) }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">person</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $payslip->employee_name }}</h4>
                    </div>
                </div>
            </div>
            <div class="space-y-3 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Basic Salary:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">Rs {{ number_format($payslip->basic_salary, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Total Earnings:</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">Rs {{ number_format($payslip->total_earnings, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Total Deductions:</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">Rs {{ number_format($payslip->total_deductions, 2) }}</span>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                <div class="flex justify-between items-center">
                    <span class="text-green-800 dark:text-green-200 font-semibold">Net Salary:</span>
                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">Rs {{ number_format($payslip->net_salary, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <button type="button" onclick="viewPayslip({{ $payslip->id }})"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-semibold flex items-center">
                    <span class="material-symbols-outlined text-sm mr-1">visibility</span>
                    View Details
                </button>
                <a href="{{ route('payroll.payslip.download', $payslip->id) }}"
                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-semibold flex items-center">
                    <span class="material-symbols-outlined text-sm mr-1">download</span>
                    Download
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16">
        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-4xl">receipt_long</span>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No payslips found</h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            No payslips available for the selected period. Please check back later or contact HR if you believe this is an error.
        </p>
    </div>
    @endforelse
</div>

@if($payslips->hasPages())
<div class="mt-8">
    {{ $payslips->links() }}
</div>
@endif

<div x-data="{ open: false, payslip: null }"
    x-on:open-payslip-modal.window="open = true; payslip = $event.detail"
    x-on:close-payslip-modal.window="open = false; payslip = null"
    x-show="open"
    class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 p-4"
    style="display: none;">
    <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] relative flex flex-col">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 px-6 pt-6 pb-4 rounded-t-xl border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Salary Slip</h2>
                    <p class="text-blue-100" x-text="payslip?.period"></p>
                </div>
                <button @click="open = false"
                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-8">
            <template x-if="payslip">
                <div class="space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Company Information</h3>
                            <div class="space-y-2 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">{{ $system_info->name ?? 'Organization' }}</p>
                                <p class="text-gray-600 dark:text-gray-400">Company Address Line 1</p>
                                <p class="text-gray-600 dark:text-gray-400">City, State - PIN Code</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information</h3>
                            <div class="space-y-2 text-sm">
                                <p><span class="text-gray-600 dark:text-gray-400">Name:</span> <span class="font-semibold" x-text="payslip.employee_name"></span></p>
                                <p><span class="text-gray-600 dark:text-gray-400">Employee ID:</span> <span x-text="payslip.employee_id || 'N/A'"></span></p>
                                <p><span class="text-gray-600 dark:text-gray-400">Department:</span> <span x-text="payslip.department || 'N/A'"></span></p>
                                <p><span class="text-gray-600 dark:text-gray-400">Payment Date:</span> <span x-text="payslip.payment_date || 'Pending'"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4 flex items-center">
                                <span class="material-symbols-outlined mr-2">trending_up</span>
                                Earnings
                            </h3>
                            <div class="space-y-3">
                                <template x-for="item in (payslip.salary_breakdown || [])" :key="item.label">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300" x-text="item.label"></span>
                                        <span class="font-semibold text-green-900 dark:text-green-100" x-text="'Rs ' + Number(item.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                </template>
                                <template x-if="payslip.overtime_pay > 0">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Overtime Pay</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100" x-text="'Rs ' + Number(payslip.overtime_pay).toLocaleString()"></span>
                                    </div>
                                </template>
                                <template x-if="payslip.bonus > 0">
                                    <div class="flex justify-between">
                                        <span class="text-green-700 dark:text-green-300">Bonus</span>
                                        <span class="font-semibold text-green-900 dark:text-green-100" x-text="'Rs ' + Number(payslip.bonus).toLocaleString()"></span>
                                    </div>
                                </template>
                                <div class="border-t border-green-200 dark:border-green-700 pt-3 mt-2">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-green-900 dark:text-green-100">Total Earnings</span>
                                        <span class="text-green-900 dark:text-green-100" x-text="'Rs ' + Number(payslip.total_earnings).toLocaleString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-6 border border-red-200 dark:border-red-800">
                            <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-4 flex items-center">
                                <span class="material-symbols-outlined mr-2">trending_down</span>
                                Deductions
                            </h3>
                            <div class="space-y-3">
                                <template x-for="item in (payslip.deduction_breakdown || [])" :key="item.label">
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300" x-text="item.label"></span>
                                        <span class="font-semibold text-red-900 dark:text-red-100" x-text="'Rs ' + Number(item.amount).toLocaleString()"></span>
                                    </div>
                                </template>
                                <template x-if="payslip.deductions > 0">
                                    <div class="flex justify-between">
                                        <span class="text-red-700 dark:text-red-300">Other Deductions</span>
                                        <span class="font-semibold text-red-900 dark:text-red-100" x-text="'Rs ' + Number(payslip.deductions).toLocaleString()"></span>
                                    </div>
                                </template>
                                <div class="border-t border-red-200 dark:border-red-700 pt-3 mt-2">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-red-900 dark:text-red-100">Total Deductions</span>
                                        <span class="text-red-900 dark:text-red-100" x-text="'Rs ' + Number(payslip.total_deductions).toLocaleString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Attendance Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Working Days</p>
                                <p class="text-xl font-bold text-blue-900 dark:text-blue-100" x-text="payslip.working_days"></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Present Days</p>
                                <p class="text-xl font-bold text-blue-900 dark:text-blue-100" x-text="payslip.present_days"></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Absent Days</p>
                                <p class="text-xl font-bold text-blue-900 dark:text-blue-100" x-text="payslip.absent_days"></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Overtime Hours</p>
                                <p class="text-xl font-bold text-blue-900 dark:text-blue-100" x-text="payslip.overtime_hours"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl p-8 text-center">
                        <h3 class="text-2xl font-bold text-white mb-2">Net Salary Payable</h3>
                        <p class="text-5xl font-bold text-white" x-text="'Rs ' + Number(payslip.net_salary).toLocaleString()"></p>
                        <p class="text-green-100 mt-2" x-text="'Amount in words: ' + payslip.amount_in_words"></p>
                    </div>
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                        <p>This is a computer-generated payslip and does not require a signature.</p>
                        <p class="mt-1">For queries, please contact the HR department.</p>
                    </div>
                </div>
            </template>
            <template x-if="!payslip">
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Payslip details not found.</p>
                </div>
            </template>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 px-8 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-end">
            <button @click="open = false"
                class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white px-6 py-2 rounded-lg transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function viewPayslip(payslipId) {
    fetch('/my-payslips/' + payslipId + '/view')
        .then(r => r.json())
        .then(data => {
            window.dispatchEvent(new CustomEvent('open-payslip-modal', { detail: data }));
        })
        .catch(() => {
            showNotification('Error loading payslip details', 'error');
        });
}
</script>
@endsection
