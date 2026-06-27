<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeaveConfigure;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function show(Request $request, $employeeId)
    {
        $employee = User::with('employee')->findOrFail($employeeId);

        $leavesCard = EmployeeLeaveConfigure::with('leaveType')
            ->where('employee_id', $employeeId)
            ->get()
            ->map(function ($config) use ($employeeId) {
                $takenDays = Leave::where('employee_id', $employeeId)
                    ->where('leave_type_id', $config->leave_type_id)
                    ->where('status', 'approved')
                    ->get()
                    ->sum(function ($leave) {
                        return $this->calculateLeaveDays($leave);
                    });
                $config->remaining = $config->days - $takenDays;
                return $config;
            });

        $leaveQuery = Leave::with('leaveType', 'user')
            ->where('employee_id', $employeeId);

        if ($request->filled('leave_type_id')) {
            $leaveQuery->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $leaveQuery->where('status', $request->status);
        }

        $leaves = $leaveQuery->orderByRaw("FIELD(status, 'cancelled') ASC")
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $leaveTypes = LeaveType::all();
        $statuses = ['pending', 'approved', 'rejected', 'declined', 'cancelled'];

        return view('hr.employee-report', compact(
            'employee', 'leavesCard', 'leaves', 'leaveTypes', 'statuses'
        ));
    }

    public function exportCsv(Request $request, $employeeId)
    {
        $employee = User::findOrFail($employeeId);

        $leaveQuery = Leave::with('leaveType', 'user')
            ->where('employee_id', $employeeId);

        if ($request->filled('leave_type_id')) {
            $leaveQuery->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $leaveQuery->where('status', $request->status);
        }

        $leaves = $leaveQuery->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leave-report-' . str_replace(' ', '_', $employee->name) . '.csv"',
        ];

        $callback = function () use ($leaves) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee Name', 'Leave Type', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Leave Reason', 'Status']);

            foreach ($leaves as $leave) {
                fputcsv($file, [
                    $leave->user->name,
                    $leave->leaveType->leave_type,
                    Carbon::parse($leave->start_date_time)->format('Y-m-d'),
                    Carbon::parse($leave->start_date_time)->format('H:i'),
                    Carbon::parse($leave->end_date_time)->format('Y-m-d'),
                    Carbon::parse($leave->end_date_time)->format('H:i'),
                    $leave->leave_reason,
                    $leave->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function back()
    {
        return redirect()->route('leave_manage');
    }

    private function calculateLeaveDays($leave)
    {
        $start = Carbon::parse($leave->start_date_time);
        $end = Carbon::parse($leave->end_date_time);

        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            return 1;
        }

        $days = 0;
        $current = $start->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        while ($current->lte($endDay)) {
            if ($current->format('Y-m-d') === $start->format('Y-m-d')) {
                $days += $start->format('H:i') >= '14:00' ? 0.5 : 1;
            } elseif ($current->format('Y-m-d') === $end->format('Y-m-d')) {
                $days += $end->format('H:i') <= '14:00' ? 0.5 : 1;
            } else {
                $days += 1;
            }
            $current->addDay();
        }

        return $days;
    }
}
