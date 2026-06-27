<?php

namespace App\Livewire\Settings;

use App\Models\PayrollSetting;
use Livewire\Component;

class PayrollSettingComponent extends Component
{
    public $overtime_rate;
    public $working_days_per_month;
    public $provident_fund_rate;
    public $tax_deduction_rate;

    public $salary_components = [];
    public $deduction_components = [];

    public $new_salary_component = [
        'name' => '',
        'label' => '',
        'type' => 'fixed',
        'rate' => 0,
        'is_active' => true
    ];

    public $new_deduction_component = [
        'name' => '',
        'label' => '',
        'type' => 'fixed',
        'rate' => 0,
        'is_active' => true
    ];

    public $showSalaryModal = false;
    public $showDeductionModal = false;

    public function mount()
    {
        $settings = PayrollSetting::getSettings();

        $this->fill([
            'overtime_rate' => $settings->overtime_rate,
            'working_days_per_month' => $settings->working_days_per_month,
            'provident_fund_rate' => $settings->provident_fund_rate,
            'tax_deduction_rate' => $settings->tax_deduction_rate,
            'salary_components' => $settings->salary_components ?? [],
            'deduction_components' => $settings->deduction_components ?? [],
        ]);
    }

    public function showAddSalaryModal()
    {
        $this->new_salary_component = [
            'name' => '',
            'label' => '',
            'type' => 'fixed',
            'rate' => 0,
            'is_active' => true
        ];
        $this->showSalaryModal = true;
        $this->dispatch('open-modal', 'add-salary-modal');
    }

    public function showAddDeductionModal()
    {
        $this->new_deduction_component = [
            'name' => '',
            'label' => '',
            'type' => 'fixed',
            'rate' => 0,
            'is_active' => true
        ];
        $this->showDeductionModal = true;
        $this->dispatch('open-modal', 'add-deduction-modal');
    }

    public function addSalaryComponent()
    {
        $this->validate([
            'new_salary_component.name' => 'required|string|max:255|regex:/^[a-z_]+$/',
            'new_salary_component.label' => 'required|string|max:255',
            'new_salary_component.type' => 'required|in:fixed,percentage',
            'new_salary_component.rate' => 'required|numeric|min:0',
        ], [
            'new_salary_component.name.regex' => 'Name must contain only lowercase letters and underscores.',
            'new_salary_component.rate.min' => 'Rate must be a positive number.',
        ]);

        // Check if name already exists
        if (collect($this->salary_components)->contains('name', $this->new_salary_component['name'])) {
            $this->addError('new_salary_component.name', 'This component name already exists.');
            return;
        }

        $this->salary_components[] = $this->new_salary_component;
        $this->showSalaryModal = false;
        $this->dispatch('close-modal', 'add-salary-modal');

        $this->dispatch(
            'show-notification',
            'Salary component added successfully!'
        );
    }

    public function addDeductionComponent()
    {
        $this->validate([
            'new_deduction_component.name' => 'required|string|max:255|regex:/^[a-z_]+$/',
            'new_deduction_component.label' => 'required|string|max:255',
            'new_deduction_component.type' => 'required|in:fixed,percentage',
            'new_deduction_component.rate' => 'required|numeric|min:0',
        ]);

        // Check if name already exists
        if (collect($this->deduction_components)->contains('name', $this->new_deduction_component['name'])) {
            $this->addError('new_deduction_component.name', 'This deduction name already exists.');
            return;
        }

        $this->deduction_components[] = $this->new_deduction_component;
        $this->showDeductionModal = false;
        $this->dispatch('close-modal', 'add-deduction-modal');

        $this->dispatch(
            'show-notification',
            'Deduction component added successfully!'
        );
    }

    public function removeSalaryComponent($index)
    {
        unset($this->salary_components[$index]);
        $this->salary_components = array_values($this->salary_components);
        $this->dispatch(
            'show-notification',
            'Salary component removed!'
        );
    }

    public function removeDeductionComponent($index)
    {
        unset($this->deduction_components[$index]);
        $this->deduction_components = array_values($this->deduction_components);
        $this->dispatch(
            'show-notification',
            'Deduction component removed!'
        );
    }

    public function toggleSalaryComponent($index)
    {
        $this->salary_components[$index]['is_active'] = !$this->salary_components[$index]['is_active'];
    }

    public function toggleDeductionComponent($index)
    {
        $this->deduction_components[$index]['is_active'] = !$this->deduction_components[$index]['is_active'];
    }

    public function submit()
    {
        $this->validate([
            'overtime_rate' => 'required|numeric|min:0',
            'working_days_per_month' => 'required|integer|min:20|max:31',
            'provident_fund_rate' => 'required|numeric|min:0|max:100',
            'tax_deduction_rate' => 'required|numeric|min:0|max:100',
        ]);

        $settings = PayrollSetting::getSettings();
        $settings->update([
            'overtime_rate' => $this->overtime_rate,
            'working_days_per_month' => $this->working_days_per_month,
            'provident_fund_rate' => $this->provident_fund_rate,
            'tax_deduction_rate' => $this->tax_deduction_rate,
            'salary_components' => $this->salary_components,
            'deduction_components' => $this->deduction_components,
        ]);

        $this->dispatch(
            'show-notification',
            'Payroll settings updated successfully!'
        );
    }

    public function render()
    {
        return view('livewire.settings.payroll-setting-component')
            ->layout('components.layouts.app', ['title' => 'Payroll Settings']);
    }
}
