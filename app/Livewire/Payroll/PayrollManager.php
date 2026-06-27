<?php

namespace App\Livewire\Payroll;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\PayrollSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PayrollManager extends Component
{
    use WithPagination;

    public string $pageTitle = 'Payroll Manager';
    public $selectedMonth;
    public $selectedYear;
    public $search = '';
    public $processingPayroll = false;
    public $selectedPayrollId = null;
    public $editingPayrollId = null;
    public $editForm = [];
    public $showSalarySettings = false;
    public $salarySettings = [];

    public $months = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->format('m');
        $this->selectedYear = Carbon::now()->format('Y');
        $this->initializeSalarySettings();
    }

    protected function initializeSalarySettings()
    {
        $settings = PayrollSetting::getSettings();
        $this->salarySettings = [
            'overtime_rate' => $settings->overtime_rate,
            'working_days_per_month' => $settings->working_days_per_month,
            'provident_fund_rate' => $settings->provident_fund_rate,
            'tax_deduction_rate' => $settings->tax_deduction_rate,
        ];
    }

    // Add this method to get payroll settings
    protected function getPayrollSettings()
    {
        return PayrollSetting::getSettings();
    }

    public function generatePayroll($employeeId)
    {
        try {
            $this->processingPayroll = true;

            $employee = User::with('employee')->findOrFail($employeeId);
            $payrollData = $this->calculatePayroll($employee);

            Payroll::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_month' => $this->selectedMonth,
                    'payroll_year' => $this->selectedYear,
                ],
                $payrollData
            );

            $this->dispatch(
                'show-notification',
                'Payroll generated successfully for ' . $employee->name,

            );
        } catch (\Exception $e) {
            $this->dispatch(
                'show-notification',
                'Error generating payroll: ' . $e->getMessage()
            );
        } finally {
            $this->processingPayroll = false;
        }
    }

    public function generateAllPayroll()
    {
        try {
            $this->processingPayroll = true;

            $employees = User::whereHas('roles', function ($query) {
                $query->where('name', 'Employee');
            })->with('employee')->get();


            $generated = 0;
            $errors = [];

            foreach ($employees as $employee) {
                try {
                    $payrollData = $this->calculatePayroll($employee);

                    Payroll::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'payroll_month' => $this->selectedMonth,
                            'payroll_year' => $this->selectedYear,
                        ],
                        $payrollData
                    );
                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = $employee->name . ': ' . $e->getMessage();
                }
            }

            $message = "Payroll generated for {$generated} employees";
            if (!empty($errors)) {
                $message .= '. Errors: ' . implode('; ', $errors);
            }

            $this->dispatch('show-notification',  $message,);
        } catch (\Exception $e) {
            $this->dispatch(
                'show-notification',
                'Error generating payroll: ' . $e->getMessage()
            );
        } finally {
            $this->processingPayroll = false;
        }
    }

    protected function calculatePayroll($employee)
    {
        $settings = $this->getPayrollSettings();
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get attendance data
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Calculate working statistics
        $totalWorkingDays = $settings->working_days_per_month;
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $totalWorkingDays - $presentDays;
        $overtimeHours = $attendances->sum('overtime');

        // Calculate leave days
        $leaveDays = $this->calculateLeaveDaysForPeriod($employee->id, $startDate, $endDate);

        // Basic salary calculation
        $basicSalary = $employee->employee->basic_salary ?? 30000;
        $perDaySalary = $basicSalary / $totalWorkingDays;
        $actualSalary = $perDaySalary * $presentDays;

        // Overtime calculation using dynamic rate
        $overtimePay = $overtimeHours * $settings->overtime_rate;

        // Calculate dynamic salary components
        $salaryBreakdown = [];
        $totalAllowances = 0;

        foreach ($settings->getActiveSalaryComponents() as $component) {
            if ($component['name'] === 'basic_salary') {
                // Basic salary is already calculated separately
                $salaryBreakdown[] = [
                    'label' => $component['label'],
                    'amount' => $basicSalary
                ];
                continue;
            }

            $amount = $component['type'] === 'percentage'
                ? $basicSalary * ($component['rate'] / 100)
                : $component['rate'];

            $salaryBreakdown[] = [
                'label' => $component['label'],
                'amount' => $amount
            ];
            $totalAllowances += $amount;
        }

        // Calculate dynamic deduction components
        $deductionBreakdown = [];
        $totalDeductions = 0;

        foreach ($settings->getActiveDeductionComponents() as $component) {
            $amount = $component['type'] === 'percentage'
                ? $basicSalary * ($component['rate'] / 100)
                : $component['rate'];

            $deductionBreakdown[] = [
                'label' => $component['label'],
                'amount' => $amount
            ];
            $totalDeductions += $amount;
        }

        // Apply specific rates for PF and Tax from settings
        $providentFund = $basicSalary * ($settings->provident_fund_rate / 100);
        $taxDeduction = $basicSalary * ($settings->tax_deduction_rate / 100);

        // Add PF and Tax to deductions breakdown
        $deductionBreakdown[] = ['label' => 'Provident Fund', 'amount' => $providentFund];
        $deductionBreakdown[] = ['label' => 'Tax Deduction', 'amount' => $taxDeduction];
        $totalDeductions += $providentFund + $taxDeduction;

        // Store individual component amounts for backward compatibility
        $componentAmounts = $this->calculateIndividualComponents($salaryBreakdown, $deductionBreakdown);

        // Totals
        $totalEarnings = $actualSalary + $overtimePay + $totalAllowances;
        $netSalary = $totalEarnings - $totalDeductions;

        return array_merge([
            'employee_name' => $employee->name,
            'basic_salary' => $basicSalary,
            'working_days' => $totalWorkingDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'overtime_hours' => $overtimeHours,
            'overtime_rate' => $settings->overtime_rate,
            'overtime_pay' => $overtimePay,
            'bonus' => 0,
            'allowances' => $totalAllowances,
            'deductions' => $totalDeductions - $providentFund - $taxDeduction, // Other deductions
            'tax_deduction' => $taxDeduction,
            'provident_fund' => $providentFund,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'status' => 'generated',
            'payment_date' => $endDate->addDays(5),
            'salary_breakdown' => $salaryBreakdown,
            'deduction_breakdown' => $deductionBreakdown,
        ], $componentAmounts);
    }

    // Helper method to calculate individual component amounts for backward compatibility
    protected function calculateIndividualComponents($salaryBreakdown, $deductionBreakdown)
    {
        $components = [
            'house_rent_allowance' => 0,
            'medical_allowance' => 0,
            'dearness_allowance' => 0,
            'conveyance_allowance' => 0,
            'professional_tax' => 0,
            'income_tax' => 0,
            'loan_deduction' => 0,
        ];

        // Map salary components
        foreach ($salaryBreakdown as $component) {
            $label = strtolower($component['label']);
            if (str_contains($label, 'house rent')) {
                $components['house_rent_allowance'] = $component['amount'];
            } elseif (str_contains($label, 'medical')) {
                $components['medical_allowance'] = $component['amount'];
            } elseif (str_contains($label, 'dearness')) {
                $components['dearness_allowance'] = $component['amount'];
            } elseif (str_contains($label, 'conveyance')) {
                $components['conveyance_allowance'] = $component['amount'];
            }
        }

        // Map deduction components
        foreach ($deductionBreakdown as $component) {
            $label = strtolower($component['label']);
            if (str_contains($label, 'professional tax')) {
                $components['professional_tax'] = $component['amount'];
            } elseif (str_contains($label, 'income tax')) {
                $components['income_tax'] = $component['amount'];
            } elseif (str_contains($label, 'loan')) {
                $components['loan_deduction'] = $component['amount'];
            }
        }

        return $components;
    }

    protected function calculateLeaveDaysForPeriod($employeeId, $startDate, $endDate)
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereBetween('start_date_time', [$startDate, $endDate])
            ->count();
    }

    public function editPayroll($payrollId)
    {
        $this->editingPayrollId = $payrollId;
        $payroll = Payroll::findOrFail($payrollId);

        $this->editForm = [
            'working_days' => $payroll->working_days,
            'present_days' => $payroll->present_days,
            'overtime_hours' => $payroll->overtime_hours,
            'leave_days' => $payroll->leave_days,
            'bonus' => $payroll->bonus,
            'allowances' => $payroll->allowances,
            'deductions' => $payroll->deductions,
            'tax_deduction' => $payroll->tax_deduction,
            'provident_fund' => $payroll->provident_fund,
            'remarks' => $payroll->remarks ?? ''
        ];

        $this->dispatch('open-modal', 'edit-payroll');
    }

    public function updatePayroll()
    {
        try {
            $payroll = Payroll::with('employee')->findOrFail($this->editingPayrollId);
            $settings = $this->getPayrollSettings();

            // Recalculate based on edited values
            $basicSalary = $payroll->basic_salary;
            $perDaySalary = $basicSalary / $settings->working_days_per_month;

            // Calculate actual salary based on present days
            $actualSalary = $perDaySalary * $this->editForm['present_days'];

            // Overtime calculation
            $overtimePay = $this->editForm['overtime_hours'] * $settings->overtime_rate;

            // Total earnings
            $totalEarnings = $actualSalary + $overtimePay + $this->editForm['bonus'] + $this->editForm['allowances'];

            // Total deductions
            $totalDeductions = $this->editForm['deductions'] + $this->editForm['tax_deduction'] + $this->editForm['provident_fund'];

            // Net salary
            $netSalary = $totalEarnings - $totalDeductions;

            $payroll->update([
                'working_days' => $this->editForm['working_days'],
                'present_days' => $this->editForm['present_days'],
                'absent_days' => $this->editForm['working_days'] - $this->editForm['present_days'],
                'overtime_hours' => $this->editForm['overtime_hours'],
                'leave_days' => $this->editForm['leave_days'],
                'overtime_pay' => $overtimePay,
                'bonus' => $this->editForm['bonus'],
                'allowances' => $this->editForm['allowances'],
                'deductions' => $this->editForm['deductions'],
                'tax_deduction' => $this->editForm['tax_deduction'],
                'provident_fund' => $this->editForm['provident_fund'],
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'remarks' => $this->editForm['remarks'],
            ]);

            $this->dispatch(
                'show-notification',
                'Payroll updated successfully',

            );

            $this->cancelEdit();
        } catch (\Exception $e) {
            $this->dispatch(
                'show-notification',
                'Error updating payroll: ' . $e->getMessage()
            );
        }
    }

    public function cancelEdit()
    {
        $this->editingPayrollId = null;
        $this->editForm = [];
        $this->dispatch('close-modal', 'edit-payroll');
    }

    public function markAsPaid($payrollId)
    {
        try {
            $payroll = Payroll::findOrFail($payrollId);
            $payroll->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);

            $this->dispatch(
                'show-notification',
                'Payroll marked as paid for ' . $payroll->employee_name,

            );
        } catch (\Exception $e) {
            $this->dispatch(
                'show-notification',
                'Error updating payroll status: ' . $e->getMessage()
            );
        }
    }

    public function viewPayrollDetails($payrollId)
    {
        $this->selectedPayrollId = $payrollId;
        $this->dispatch('open-modal', 'payroll-details');
    }

    public function getSelectedPayrollProperty()
    {
        if (!$this->selectedPayrollId) {
            return null;
        }
        return Payroll::with(['employee'])->find($this->selectedPayrollId);
    }

    public function showSalarySettings()
    {
        $this->showSalarySettings = true;
        $this->dispatch('open-modal', 'salary-settings');
    }

    public function updateSalarySettings()
    {
        $this->dispatch(
            'show-notification',
            'Salary settings updated successfully',

        );
        $this->showSalarySettings = false;
        $this->dispatch('close-modal', 'salary-settings');
    }

    // Add CSV download method
    public function downloadCSV()
    {
        $payrolls = Payroll::where('payroll_month', $this->selectedMonth)
            ->where('payroll_year', $this->selectedYear)
            ->with('employee')
            ->get();

        $fileName = "payroll-{$this->selectedMonth}-{$this->selectedYear}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            // Headers
            fputcsv($file, [
                'Employee Name',
                'Employee ID',
                'Position',
                'Basic Salary',
                'Present Days',
                'Overtime Hours',
                'Overtime Pay',
                'Bonus',
                'Allowances',
                'Deductions',
                'Provident Fund',
                'Tax Deduction',
                'Total Earnings',
                'Total Deductions',
                'Net Salary',
                'Status'
            ]);

            // Data
            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $payroll->employee_name,
                    $payroll->employee->employee_id ?? 'N/A',
                    $payroll->employee->position ?? 'Employee',
                    $payroll->basic_salary,
                    $payroll->present_days,
                    $payroll->overtime_hours,
                    $payroll->overtime_pay,
                    $payroll->bonus,
                    $payroll->allowances,
                    $payroll->deductions,
                    $payroll->provident_fund,
                    $payroll->tax_deduction,
                    $payroll->total_earnings,
                    $payroll->total_deductions,
                    $payroll->net_salary,
                    $payroll->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employee');
        })
            ->with(['employee', 'payrolls' => function ($query) {
                $query->where('payroll_month', $this->selectedMonth)
                    ->where('payroll_year', $this->selectedYear);
            }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        // Summary statistics
        $totalEmployees = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employee');
        })->count();

        $payrolls = Payroll::where('payroll_month', $this->selectedMonth)
            ->where('payroll_year', $this->selectedYear)
            ->get();

        $generatedPayrolls = $payrolls->count();
        $paidPayrolls = $payrolls->where('status', 'paid')->count();
        $totalNetSalary = $payrolls->sum('net_salary');
        $totalEarnings = $payrolls->sum('total_earnings');
        $totalDeductions = $payrolls->sum('total_deductions');

        return view('livewire.payroll.payroll-manager', [
            'employees' => $employees,
            'totalEmployees' => $totalEmployees,
            'generatedPayrolls' => $generatedPayrolls,
            'paidPayrolls' => $paidPayrolls,
            'totalNetSalary' => $totalNetSalary,
            'totalEarnings' => $totalEarnings,
            'totalDeductions' => $totalDeductions,
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
