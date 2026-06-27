<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\PayrollSetting;
use App\Models\User;
use App\Notifications\PayslipGenerated;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    protected $months = [
        '01' => 'January', '02' => 'February', '03' => 'March',
        '04' => 'April', '05' => 'May', '06' => 'June',
        '07' => 'July', '08' => 'August', '09' => 'September',
        '10' => 'October', '11' => 'November', '12' => 'December'
    ];

    public function index(Request $request)
    {
        $selectedMonth = $request->query('selectedMonth', now()->format('m'));
        $selectedYear = $request->query('selectedYear', now()->format('Y'));
        $search = $request->query('search', '');

        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employee');
        })
            ->with(['employee', 'payrolls' => function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('payroll_month', $selectedMonth)
                    ->where('payroll_year', $selectedYear);
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10);

        $totalEmployees = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employee');
        })->count();

        $payrolls = Payroll::where('payroll_month', $selectedMonth)
            ->where('payroll_year', $selectedYear)
            ->get();

        $generatedPayrolls = $payrolls->count();
        $paidPayrolls = $payrolls->where('status', 'paid')->count();
        $totalNetSalary = $payrolls->sum('net_salary');
        $totalEarnings = $payrolls->sum('total_earnings');
        $totalDeductions = $payrolls->sum('total_deductions');

        return view('payroll.payroll-manager', [
            'title' => 'Payroll Manager',
            'employees' => $employees,
            'totalEmployees' => $totalEmployees,
            'generatedPayrolls' => $generatedPayrolls,
            'paidPayrolls' => $paidPayrolls,
            'totalNetSalary' => $totalNetSalary,
            'totalEarnings' => $totalEarnings,
            'totalDeductions' => $totalDeductions,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'search' => $search,
            'months' => $this->months,
        ]);
    }

    public function generatePayroll(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            $selectedMonth = $request->input('selectedMonth', now()->format('m'));
            $selectedYear = $request->input('selectedYear', now()->format('Y'));

            $employee = User::with('employee')->findOrFail($employeeId);
            $payrollData = $this->calculatePayroll($employee, $selectedMonth, $selectedYear);

            $payroll = Payroll::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'payroll_month' => $selectedMonth,
                    'payroll_year' => $selectedYear,
                ],
                $payrollData
            );

            $employee->notify(new PayslipGenerated($payroll));

            return redirect()->back()->with('status', 'Payroll generated successfully for ' . $employee->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    public function generateAllPayroll(Request $request)
    {
        try {
            $selectedMonth = $request->input('selectedMonth', now()->format('m'));
            $selectedYear = $request->input('selectedYear', now()->format('Y'));

            $employees = User::whereHas('roles', function ($query) {
                $query->where('name', 'Employee');
            })->with('employee')->get();

            $generated = 0;
            $errors = [];

            foreach ($employees as $employee) {
                try {
                    $payrollData = $this->calculatePayroll($employee, $selectedMonth, $selectedYear);

                    $payroll = Payroll::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'payroll_month' => $selectedMonth,
                            'payroll_year' => $selectedYear,
                        ],
                        $payrollData
                    );

                    $employee->notify(new PayslipGenerated($payroll));
                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = $employee->name . ': ' . $e->getMessage();
                }
            }

            $message = "Payroll generated for {$generated} employees";
            if (!empty($errors)) {
                $message .= '. Errors: ' . implode('; ', $errors);
            }

            return redirect()->back()->with('status', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    protected function calculatePayroll($employee, $selectedMonth, $selectedYear)
    {
        $settings = PayrollSetting::getSettings();
        $startDate = Carbon::create($selectedYear, $selectedMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalWorkingDays = $settings->working_days_per_month;
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $totalWorkingDays - $presentDays;
        $overtimeHours = $attendances->sum('overtime');

        $leaveDays = $this->calculateLeaveDaysForPeriod($employee->id, $startDate, $endDate);

        $basicSalary = $employee->employee->basic_salary ?? 30000;
        $perDaySalary = $basicSalary / $totalWorkingDays;
        $actualSalary = $perDaySalary * $presentDays;

        $overtimePay = $overtimeHours * $settings->overtime_rate;

        $salaryBreakdown = [];
        $totalAllowances = 0;

        foreach ($settings->getActiveSalaryComponents() as $component) {
            if ($component['name'] === 'basic_salary') {
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

        $providentFund = $basicSalary * ($settings->provident_fund_rate / 100);
        $taxDeduction = $basicSalary * ($settings->tax_deduction_rate / 100);

        $deductionBreakdown[] = ['label' => 'Provident Fund', 'amount' => $providentFund];
        $deductionBreakdown[] = ['label' => 'Tax Deduction', 'amount' => $taxDeduction];
        $totalDeductions += $providentFund + $taxDeduction;

        $componentAmounts = $this->calculateIndividualComponents($salaryBreakdown, $deductionBreakdown);

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
            'deductions' => $totalDeductions - $providentFund - $taxDeduction,
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

    public function edit($payrollId)
    {
        $payroll = Payroll::findOrFail($payrollId);

        return response()->json([
            'working_days' => $payroll->working_days,
            'present_days' => $payroll->present_days,
            'overtime_hours' => (float) $payroll->overtime_hours,
            'leave_days' => $payroll->leave_days,
            'bonus' => (float) $payroll->bonus,
            'allowances' => (float) $payroll->allowances,
            'deductions' => (float) $payroll->deductions,
            'tax_deduction' => (float) $payroll->tax_deduction,
            'provident_fund' => (float) $payroll->provident_fund,
            'remarks' => $payroll->remarks ?? '',
        ]);
    }

    public function update(Request $request, $payrollId)
    {
        try {
            $payroll = Payroll::with('employee')->findOrFail($payrollId);
            $settings = PayrollSetting::getSettings();

            $basicSalary = $payroll->basic_salary;
            $perDaySalary = $basicSalary / $settings->working_days_per_month;

            $presentDays = $request->input('present_days', $payroll->present_days);
            $workingDays = $request->input('working_days', $payroll->working_days);
            $overtimeHours = $request->input('overtime_hours', $payroll->overtime_hours);
            $bonus = $request->input('bonus', $payroll->bonus);
            $allowances = $request->input('allowances', $payroll->allowances);
            $deductions = $request->input('deductions', $payroll->deductions);
            $taxDeduction = $request->input('tax_deduction', $payroll->tax_deduction);
            $providentFund = $request->input('provident_fund', $payroll->provident_fund);
            $leaveDays = $request->input('leave_days', $payroll->leave_days);
            $remarks = $request->input('remarks', $payroll->remarks);

            $actualSalary = $perDaySalary * $presentDays;
            $overtimePay = $overtimeHours * $settings->overtime_rate;
            $totalEarnings = $actualSalary + $overtimePay + $bonus + $allowances;
            $totalDeductions = $deductions + $taxDeduction + $providentFund;
            $netSalary = $totalEarnings - $totalDeductions;

            $payroll->update([
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'absent_days' => $workingDays - $presentDays,
                'overtime_hours' => $overtimeHours,
                'leave_days' => $leaveDays,
                'overtime_pay' => $overtimePay,
                'bonus' => $bonus,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'tax_deduction' => $taxDeduction,
                'provident_fund' => $providentFund,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'remarks' => $remarks,
            ]);

            return redirect()->back()->with('status', 'Payroll updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating payroll: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Request $request, $payrollId)
    {
        try {
            $payroll = Payroll::findOrFail($payrollId);
            $payroll->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);

            return redirect()->back()->with('status', 'Payroll marked as paid for ' . $payroll->employee_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating payroll status: ' . $e->getMessage());
        }
    }

    public function viewDetails($payrollId)
    {
        $payroll = Payroll::with('employee.employee')->findOrFail($payrollId);
        $emp = $payroll->employee->employee;

        return response()->json([
            'id' => $payroll->id,
            'employee_name' => $payroll->employee_name,
            'employee_id' => $emp->employee_id ?? 'N/A',
            'position' => $emp->position ?? 'Employee',
            'payroll_month' => $payroll->payroll_month,
            'payroll_year' => $payroll->payroll_year,
            'period' => $payroll->period,
            'basic_salary' => (float) $payroll->basic_salary,
            'working_days' => $payroll->working_days,
            'present_days' => $payroll->present_days,
            'absent_days' => $payroll->absent_days,
            'leave_days' => $payroll->leave_days,
            'overtime_hours' => (float) $payroll->overtime_hours,
            'overtime_pay' => (float) $payroll->overtime_pay,
            'bonus' => (float) $payroll->bonus,
            'allowances' => (float) $payroll->allowances,
            'deductions' => (float) $payroll->deductions,
            'tax_deduction' => (float) $payroll->tax_deduction,
            'provident_fund' => (float) $payroll->provident_fund,
            'total_earnings' => (float) $payroll->total_earnings,
            'total_deductions' => (float) $payroll->total_deductions,
            'net_salary' => (float) $payroll->net_salary,
            'status' => $payroll->status,
            'payment_date' => $payroll->payment_date?->format('M d, Y') ?? 'Not Paid',
            'remarks' => $payroll->remarks,
            'salary_breakdown' => $payroll->salary_breakdown ?? [],
            'deduction_breakdown' => $payroll->deduction_breakdown ?? [],
        ]);
    }

    public function downloadCsv(Request $request)
    {
        $selectedMonth = $request->query('selectedMonth', now()->format('m'));
        $selectedYear = $request->query('selectedYear', now()->format('Y'));

        $payrolls = Payroll::where('payroll_month', $selectedMonth)
            ->where('payroll_year', $selectedYear)
            ->with('employee.employee')
            ->get();

        $fileName = "payroll-{$selectedMonth}-{$selectedYear}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Employee Name', 'Employee ID', 'Position', 'Basic Salary',
                'Present Days', 'Overtime Hours', 'Overtime Pay', 'Bonus',
                'Allowances', 'Deductions', 'Provident Fund', 'Tax Deduction',
                'Total Earnings', 'Total Deductions', 'Net Salary', 'Status'
            ]);

            foreach ($payrolls as $payroll) {
                $emp = $payroll->employee->employee ?? null;
                fputcsv($file, [
                    $payroll->employee_name,
                    $emp->employee_id ?? 'N/A',
                    $emp->position ?? 'Employee',
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
                    $payroll->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function salarySettings(Request $request)
    {
        $settings = PayrollSetting::getSettings();

        return response()->json([
            'overtime_rate' => (float) $settings->overtime_rate,
            'working_days_per_month' => $settings->working_days_per_month,
            'provident_fund_rate' => (float) $settings->provident_fund_rate,
            'tax_deduction_rate' => (float) $settings->tax_deduction_rate,
        ]);
    }

    public function updateSalarySettings(Request $request)
    {
        $settings = PayrollSetting::getSettings();
        $settings->update([
            'overtime_rate' => $request->input('overtime_rate', $settings->overtime_rate),
            'working_days_per_month' => $request->input('working_days_per_month', $settings->working_days_per_month),
            'provident_fund_rate' => $request->input('provident_fund_rate', $settings->provident_fund_rate),
            'tax_deduction_rate' => $request->input('tax_deduction_rate', $settings->tax_deduction_rate),
        ]);

        return redirect()->back()->with('status', 'Salary settings updated successfully');
    }
}
