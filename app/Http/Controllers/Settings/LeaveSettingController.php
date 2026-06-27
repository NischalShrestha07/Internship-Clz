<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveConfigure;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveSettingController extends Controller
{
    public function edit()
    {
        $leaveTypes = LeaveType::all();
        $employees = User::role('employee')->get();
        return view('settings.leave-setting', compact('leaveTypes', 'employees'));
    }

    public function update(Request $request)
    {
        if ($request->has('leave_type')) {
            $request->validate([
                'leave_type' => 'required|unique:leave_types,leave_type',
                'default_days' => 'required|numeric',
            ]);
            LeaveType::create([
                'leave_type' => $request->leave_type,
                'default_days' => $request->default_days,
            ]);
            return redirect()->route('setting.leave')->with('status', 'Leave Type added successfully');
        }

        $request->validate([
            'employee' => 'required|exists:users,id',
            'selectedLeaveType' => 'required|exists:leave_types,id',
            'days' => 'required|numeric',
        ]);

        $exists = EmployeeLeaveConfigure::where('employee_id', $request->employee)
            ->where('leave_type_id', $request->selectedLeaveType)
            ->exists();

        if ($exists) {
            return redirect()->route('setting.leave')->with('error', 'This leave type configuration already exists for the employee.');
        }

        EmployeeLeaveConfigure::create([
            'employee_id' => $request->employee,
            'leave_type_id' => $request->selectedLeaveType,
            'days' => $request->days,
        ]);

        return redirect()->route('setting.leave')->with('status', 'Employee Leave Configuration added successfully');
    }
}
