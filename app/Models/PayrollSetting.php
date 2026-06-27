<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = [
        'overtime_rate',
        'working_days_per_month',
        'provident_fund_rate',
        'tax_deduction_rate',
        'salary_components',
        'deduction_components',
        'is_active'
    ];

    protected $casts = [
        'overtime_rate' => 'decimal:2',
        'working_days_per_month' => 'integer',
        'provident_fund_rate' => 'decimal:2',
        'tax_deduction_rate' => 'decimal:2',
        'salary_components' => 'array',
        'deduction_components' => 'array',
        'is_active' => 'boolean'
    ];

    public static function getSettings()
    {
        $settings = static::first();
        if (!$settings) {
            $settings = static::create([
                'overtime_rate' => 200,
                'working_days_per_month' => 26,
                'provident_fund_rate' => 12,
                'tax_deduction_rate' => 5,
                'salary_components' => [
                    ['name' => 'basic_salary', 'label' => 'Basic Salary', 'type' => 'fixed', 'rate' => 0, 'is_active' => true],
                    ['name' => 'house_rent_allowance', 'label' => 'House Rent Allowance', 'type' => 'percentage', 'rate' => 40, 'is_active' => true],
                    ['name' => 'medical_allowance', 'label' => 'Medical Allowance', 'type' => 'fixed', 'rate' => 1250, 'is_active' => true],
                    ['name' => 'dearness_allowance', 'label' => 'Dearness Allowance', 'type' => 'percentage', 'rate' => 15, 'is_active' => true],
                    ['name' => 'conveyance_allowance', 'label' => 'Conveyance Allowance', 'type' => 'fixed', 'rate' => 800, 'is_active' => true],
                ],
                'deduction_components' => [
                    ['name' => 'provident_fund', 'label' => 'Provident Fund', 'type' => 'percentage', 'rate' => 12, 'is_active' => true],
                    ['name' => 'professional_tax', 'label' => 'Professional Tax', 'type' => 'fixed', 'rate' => 200, 'is_active' => true],
                    ['name' => 'income_tax', 'label' => 'Income Tax', 'type' => 'percentage', 'rate' => 5, 'is_active' => true],
                    ['name' => 'loan_deduction', 'label' => 'Loan Deduction', 'type' => 'fixed', 'rate' => 0, 'is_active' => false],
                ],
                'is_active' => true
            ]);
        }
        return $settings;
    }

    public function getActiveSalaryComponents()
    {
        return collect($this->salary_components ?? [])->where('is_active', true)->values();
    }

    public function getActiveDeductionComponents()
    {
        return collect($this->deduction_components ?? [])->where('is_active', true)->values();
    }

    public function getSalaryComponent($name)
    {
        return collect($this->salary_components ?? [])->firstWhere('name', $name);
    }

    public function getDeductionComponent($name)
    {
        return collect($this->deduction_components ?? [])->firstWhere('name', $name);
    }
}
