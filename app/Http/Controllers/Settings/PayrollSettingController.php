<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function edit()
    {
        $settings = PayrollSetting::getSettings();
        return view('settings.payroll-setting-component', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'overtime_rate' => 'required|numeric|min:0',
            'working_days_per_month' => 'required|integer|min:20|max:31',
            'provident_fund_rate' => 'required|numeric|min:0|max:100',
            'tax_deduction_rate' => 'required|numeric|min:0|max:100',
            'salary_components' => 'nullable|json',
            'deduction_components' => 'nullable|json',
        ]);

        $settings = PayrollSetting::getSettings();
        $data = $request->only([
            'overtime_rate', 'working_days_per_month',
            'provident_fund_rate', 'tax_deduction_rate',
        ]);

        if ($request->filled('salary_components')) {
            $data['salary_components'] = json_decode($request->salary_components, true);
        }
        if ($request->filled('deduction_components')) {
            $data['deduction_components'] = json_decode($request->deduction_components, true);
        }

        $settings->update($data);

        return redirect()->route('payroll.setting')->with('status', 'Payroll settings updated successfully!');
    }
}
