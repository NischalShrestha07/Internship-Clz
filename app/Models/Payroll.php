<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_name',
        'payroll_month',
        'payroll_year',
        'basic_salary',
        'working_days',
        'present_days',
        'absent_days',
        'leave_days',
        'overtime_hours',
        'overtime_rate',
        'overtime_pay',
        'bonus',
        'allowances',
        'deductions',
        'tax_deduction',
        'provident_fund',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'status',
        'payment_date',
        'remarks',
        'salary_breakdown',
        'deduction_breakdown',
        'house_rent_allowance',
        'medical_allowance',
        'dearness_allowance',
        'conveyance_allowance',
        'professional_tax',
        'income_tax',
        'loan_deduction'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'provident_fund' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
        'overtime_rate' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'salary_breakdown' => 'array', // Add this
        'deduction_breakdown' => 'array', // Add this
    ];
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Remove the user() relationship or fix it
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Accessor for formatted period
    public function getPeriodAttribute()
    {
        return \Carbon\Carbon::createFromDate($this->payroll_year, $this->payroll_month, 1)->format('F Y');
    }

    // Scope for current period
    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('payroll_month', $month)
            ->where('payroll_year', $year);
    }

    // Scope for employee
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
