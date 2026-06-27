<?php

namespace App\Livewire\Payroll;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payroll;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class MyPayslips extends Component
{
    use WithPagination;

    public string $pageTitle = 'My Payslips';
    public $selectedMonth;
    public $selectedYear;
    public $system_info;
    public $selectedPayslipId = null; // Add this property

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
        $this->selectedMonth = date('m');
        $this->selectedYear = date('Y');
        $this->system_info = SystemSetting::first();
    }

    // Add this method to view payslip details
    public function viewPayslip($payslipId)
    {
        $this->selectedPayslipId = $payslipId;
        $this->dispatch('open-modal', 'payslip-details');
    }

    // Add this computed property to get the selected payslip
    public function getSelectedPayslipProperty()
    {
        if (!$this->selectedPayslipId) {
            return null;
        }

        return Payroll::where('id', $this->selectedPayslipId)
            ->where('employee_id', Auth::id())
            ->with('employee')
            ->first();
    }

    public function convertToWords($number)
    {
        // Simple number to words conversion - you can enhance this later
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $words = $formatter->format($number);
        return ucwords($words) . ' Rupees Only';
    }

    public function downloadPayslip($payrollId)
    {
        $payroll = Payroll::where('id', $payrollId)
            ->where('employee_id', Auth::id())
            ->with('employee')
            ->firstOrFail();

        session(['print_payslip_id' => $payrollId]);

        return redirect()->route('payslip.print');
    }

    public function getSalaryBreakdown($payslip)
    {
        if ($payslip->salary_breakdown && is_array($payslip->salary_breakdown)) {
            return $payslip->salary_breakdown;
        }

        $breakdown = [];
        $breakdown[] = [
            'label' => 'Basic Salary',
            'amount' => $payslip->basic_salary
        ];

        if ($payslip->house_rent_allowance > 0) {
            $breakdown[] = [
                'label' => 'House Rent Allowance',
                'amount' => $payslip->house_rent_allowance
            ];
        }
        if ($payslip->medical_allowance > 0) {
            $breakdown[] = [
                'label' => 'Medical Allowance',
                'amount' => $payslip->medical_allowance
            ];
        }
        if ($payslip->dearness_allowance > 0) {
            $breakdown[] = [
                'label' => 'Dearness Allowance',
                'amount' => $payslip->dearness_allowance
            ];
        }
        if ($payslip->conveyance_allowance > 0) {
            $breakdown[] = [
                'label' => 'Conveyance Allowance',
                'amount' => $payslip->conveyance_allowance
            ];
        }
        return $breakdown;
    }

    public function getDeductionBreakdown($payslip)
    {
        $breakdown = [];

        // Add deduction components from breakdown
        if ($payslip->deduction_breakdown) {
            foreach ($payslip->deduction_breakdown as $component) {
                $breakdown[] = [
                    'label' => $component['label'],
                    'amount' => $component['amount']
                ];
            }
        } else {
            // Fallback to individual fields
            if ($payslip->provident_fund > 0) {
                $breakdown[] = [
                    'label' => 'Provident Fund',
                    'amount' => $payslip->provident_fund
                ];
            }
            if ($payslip->professional_tax > 0) {
                $breakdown[] = [
                    'label' => 'Professional Tax',
                    'amount' => $payslip->professional_tax
                ];
            }
            if ($payslip->income_tax > 0) {
                $breakdown[] = [
                    'label' => 'Income Tax',
                    'amount' => $payslip->income_tax
                ];
            }
            if ($payslip->loan_deduction > 0) {
                $breakdown[] = [
                    'label' => 'Loan Deduction',
                    'amount' => $payslip->loan_deduction
                ];
            }
        }

        return $breakdown;
    }

    public function render()
    {
        $payslips = Payroll::where('employee_id', Auth::id())
            ->when($this->selectedMonth, function ($query) {
                $query->where('payroll_month', $this->selectedMonth);
            })
            ->when($this->selectedYear, function ($query) {
                $query->where('payroll_year', $this->selectedYear);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('livewire.payroll.my-payslips', [
            'payslips' => $payslips
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
