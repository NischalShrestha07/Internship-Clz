<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class AttendanceSettingController extends Controller
{
    public function edit()
    {
        $settings = AttendanceSetting::getSettings();
        return view('settings.attendance-setting', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'daily_working_hours' => 'required|numeric|min:1|max:24',
            'default_break_minutes' => 'required|integer|min:0',
            'overtime_threshold' => 'required|numeric|min:0',
            'late_grace_minutes' => 'required|integer|min:0',
            'records_per_page' => 'required|integer|min:5|max:100',
        ]);

        $settings = AttendanceSetting::getSettings();
        $settings->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'daily_working_hours' => $request->daily_working_hours,
            'default_break_minutes' => $request->default_break_minutes,
            'overtime_threshold' => $request->overtime_threshold,
            'late_grace_minutes' => $request->late_grace_minutes,
            'records_per_page' => $request->records_per_page,
        ]);

        return redirect()->route('attendance.setting')->with('status', 'Attendance settings updated successfully!');
    }
}
