<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPayslipController extends Controller
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
        $system_info = SystemSetting::first();

        $payslips = Payroll::where('employee_id', auth()->id())
            ->where('payroll_year', $selectedYear)
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                $query->where('payroll_month', $selectedMonth);
            })
            ->orderBy('payroll_year', 'desc')
            ->orderBy('payroll_month', 'desc')
            ->paginate(9);

        return view('payroll.my-payslips', [
            'title' => 'My Payslips',
            'payslips' => $payslips,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'months' => $this->months,
            'system_info' => $system_info,
        ]);
    }

    public function viewPayslip($payslipId)
    {
        $payslip = Payroll::with('employee.employee')
            ->where('employee_id', auth()->id())
            ->findOrFail($payslipId);

        $emp = $payslip->employee->employee;

        return response()->json([
            'id' => $payslip->id,
            'employee_name' => $payslip->employee_name,
            'employee_id' => $emp->employee_id ?? 'N/A',
            'department' => $emp->department ?? 'N/A',
            'period' => $payslip->period,
            'payroll_month' => $payslip->payroll_month,
            'payroll_year' => $payslip->payroll_year,
            'payment_date' => $payslip->payment_date?->format('d M, Y') ?? 'Pending',
            'status' => $payslip->status,
            'basic_salary' => (float) $payslip->basic_salary,
            'working_days' => $payslip->working_days,
            'present_days' => $payslip->present_days,
            'absent_days' => $payslip->absent_days,
            'overtime_hours' => (float) $payslip->overtime_hours,
            'overtime_pay' => (float) $payslip->overtime_pay,
            'bonus' => (float) $payslip->bonus,
            'allowances' => (float) $payslip->allowances,
            'deductions' => (float) $payslip->deductions,
            'tax_deduction' => (float) $payslip->tax_deduction,
            'provident_fund' => (float) $payslip->provident_fund,
            'total_earnings' => (float) $payslip->total_earnings,
            'total_deductions' => (float) $payslip->total_deductions,
            'net_salary' => (float) $payslip->net_salary,
            'salary_breakdown' => self::getSalaryBreakdown($payslip),
            'deduction_breakdown' => self::getDeductionBreakdown($payslip),
            'remarks' => $payslip->remarks,
            'amount_in_words' => self::convertToWords((float) $payslip->net_salary),
        ]);
    }

    public function downloadPayslip(Request $request, $payslipId)
    {
        $payslip = Payroll::where('employee_id', auth()->id())
            ->where('id', $payslipId)
            ->firstOrFail();

        session(['print_payslip_id' => $payslipId]);

        return redirect()->route('payslip.print');
    }

    public function print(Request $request)
    {
        $payslipId = session('print_payslip_id');

        if (!$payslipId) {
            abort(404);
        }

        $payslip = Payroll::with('employee.employee')
            ->where('id', $payslipId)
            ->where('employee_id', auth()->id())
            ->firstOrFail();

        return view('payslip-print', compact('payslip'));
    }

    public static function getSalaryBreakdown($payslip)
    {
        if ($payslip->salary_breakdown && is_array($payslip->salary_breakdown)) {
            return $payslip->salary_breakdown;
        }

        $breakdown = [['label' => 'Basic Salary', 'amount' => (float) $payslip->basic_salary]];
        if ((float) $payslip->house_rent_allowance > 0) {
            $breakdown[] = ['label' => 'House Rent Allowance', 'amount' => (float) $payslip->house_rent_allowance];
        }
        if ((float) $payslip->medical_allowance > 0) {
            $breakdown[] = ['label' => 'Medical Allowance', 'amount' => (float) $payslip->medical_allowance];
        }
        if ((float) $payslip->dearness_allowance > 0) {
            $breakdown[] = ['label' => 'Dearness Allowance', 'amount' => (float) $payslip->dearness_allowance];
        }
        if ((float) $payslip->conveyance_allowance > 0) {
            $breakdown[] = ['label' => 'Conveyance Allowance', 'amount' => (float) $payslip->conveyance_allowance];
        }
        if ((float) $payslip->allowances > 0) {
            $breakdown[] = ['label' => 'Other Allowances', 'amount' => (float) $payslip->allowances];
        }

        return $breakdown;
    }

    public static function getDeductionBreakdown($payslip)
    {
        if ($payslip->deduction_breakdown && is_array($payslip->deduction_breakdown)) {
            return $payslip->deduction_breakdown;
        }

        $breakdown = [];
        if ((float) $payslip->provident_fund > 0) {
            $breakdown[] = ['label' => 'Provident Fund', 'amount' => (float) $payslip->provident_fund];
        }
        if ((float) $payslip->tax_deduction > 0) {
            $breakdown[] = ['label' => 'Tax Deduction', 'amount' => (float) $payslip->tax_deduction];
        }
        if ((float) $payslip->professional_tax > 0) {
            $breakdown[] = ['label' => 'Professional Tax', 'amount' => (float) $payslip->professional_tax];
        }
        if ((float) $payslip->income_tax > 0) {
            $breakdown[] = ['label' => 'Income Tax', 'amount' => (float) $payslip->income_tax];
        }
        if ((float) $payslip->loan_deduction > 0) {
            $breakdown[] = ['label' => 'Loan Deduction', 'amount' => (float) $payslip->loan_deduction];
        }
        if ((float) $payslip->deductions > 0) {
            $breakdown[] = ['label' => 'Other Deductions', 'amount' => (float) $payslip->deductions];
        }

        return $breakdown;
    }

    public static function convertToWords($number)
    {
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        return ucwords($formatter->format($number)) . ' Rupees Only';
    }
}
