<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\CalendarEvent;
use App\Models\EmployeeLeaveConfigure;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasAnyRole(['HR Manager', 'Employee'])) {
            $data = $this->getDashboardData($user);
            return view('dashboard', $data);
        }

        abort(403, 'Unauthorized');
    }

    private function getDashboardData($user)
    {
        $now = Carbon::now('Asia/Kathmandu');
        $today = $now->copy()->startOfDay();
        $isHrManager = $user->hasRole('HR Manager');

        $todayAttendances = Attendance::with('breakRecords')
            ->where('employee_id', $user->id)
            ->whereDate('check_in', $today)
            ->orderBy('check_in')
            ->get();

        $isClockedIn = $todayAttendances->contains(function ($a) {
            return is_null($a->check_out);
        });

        $currentSession = $todayAttendances->first(function ($a) {
            return is_null($a->check_out);
        });

        $totalMinutesToday = 0;
        $breakMinutesToday = 0;

        foreach ($todayAttendances as $a) {
            $totalMinutesToday += $this->calculateTotalMinutes($a);
            $breakMinutesToday += $this->calculateBreakMinutes($a);
        }

        $isOnBreak = false;
        if ($currentSession) {
            $isOnBreak = BreakRecord::where('attendance_id', $currentSession->id)
                ->whereNull('end_time')
                ->exists();
        }

        $workedMinutesToday = max(0, $totalMinutesToday - $breakMinutesToday);
        $totalHoursToday = AttendanceHelper::decimalToHoursMinutes($workedMinutesToday / 60);
        $breakTimeToday = AttendanceHelper::minutesToHoursMinutes($breakMinutesToday);
        $overtimeMinutes = max(0, $workedMinutesToday - 8 * 60);
        $overtimeToday = AttendanceHelper::minutesToHoursMinutes($overtimeMinutes);
        $remainingMinutes = max(0, 8 * 60 - $workedMinutesToday);
        $remainingHoursToday = AttendanceHelper::minutesToHoursMinutes($remainingMinutes);

        $leaveConfig = EmployeeLeaveConfigure::where('employee_id', $user->id)->first();
        $totalLeaveDays = $leaveConfig ? $leaveConfig->total_leave_days : 0;
        $usedLeaveDays = Leave::where('employee_id', $user->id)
            ->where('status', 'approved')
            ->get()
            ->sum(function ($leave) {
                return Carbon::parse($leave->start_date_time)->diffInDays(Carbon::parse($leave->end_date_time)) + 1;
            });
        $remainingLeaveDays = max(0, $totalLeaveDays - $usedLeaveDays);

        $pendingLeaveRequests = Leave::where('employee_id', $user->id)->where('status', 'pending')->count();
        $approvedLeaveRequests = Leave::where('employee_id', $user->id)->where('status', 'approved')->count();

        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $monthlyAttendances = Attendance::where('employee_id', $user->id)
            ->whereBetween('check_in', [$monthStart, $monthEnd])
            ->get();

        $daysPresent = $monthlyAttendances->pluck('date')->unique()->count();
        $daysAbsent = max(0, $now->daysInMonth - $daysPresent);
        $lateArrivals = $monthlyAttendances->filter(function ($a) {
            $checkIn = Carbon::parse($a->check_in);
            return $checkIn->format('H:i') > '09:00';
        })->count();

        $totalEmployees = $isHrManager ? User::role('Employee')->count() : 0;
        $totalEmployeesClockedIn = 0;
        $totalEmployeesOnLeave = 0;
        $totalPendingLeaveRequests = 0;

        if ($isHrManager) {
            $totalEmployeesClockedIn = Attendance::whereDate('check_in', $today)
                ->whereNull('check_out')
                ->distinct('employee_id')
                ->count('employee_id');

            $totalEmployeesOnLeave = Leave::whereDate('start_date_time', '<=', $today)
                ->whereDate('end_date_time', '>=', $today)
                ->where('status', 'approved')
                ->distinct('employee_id')
                ->count();

            $totalPendingLeaveRequests = Leave::where('status', 'pending')->count();
        }

        $announcements = CalendarEvent::where('type', 'announcement')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($event) {
                $createdAt = $event->created_at ?? now();
                return [
                    'title' => $event->title,
                    'description' => $event->description ?? '',
                    'type' => $event->event_type ?? 'General',
                    'date' => $createdAt,
                    'time' => $createdAt->format('g:i A'),
                    'created_by' => $event->created_by ?? 'System',
                    'posted_at' => $createdAt->diffForHumans(),
                ];
            });

        $upcomingLeaves = Leave::with('user')
            ->where('status', 'approved')
            ->whereDate('start_date_time', '>=', $today)
            ->orderBy('start_date_time')
            ->take(5)
            ->get()
            ->map(function ($leave) {
                return [
                    'employee' => optional($leave->user)->name ?? 'Unknown',
                    'start' => $leave->start_date_time,
                    'end' => $leave->end_date_time,
                    'type' => optional($leave->leaveType)->name ?? 'Leave',
                    'days' => Carbon::parse($leave->start_date_time)->diffInDays(Carbon::parse($leave->end_date_time)) + 1,
                ];
            });

        return [
            'currentDate' => $now,
            'currentTime' => $now->format('h:i:s A'),
            'isClockedIn' => $isClockedIn,
            'isOnBreak' => $isOnBreak,
            'isHrManager' => $isHrManager,
            'totalLeaveDays' => $totalLeaveDays,
            'remainingLeaveDays' => $remainingLeaveDays,
            'pendingLeaveRequests' => $pendingLeaveRequests,
            'approvedLeaveRequests' => $approvedLeaveRequests,
            'totalEmployees' => $totalEmployees,
            'totalEmployeesClockedIn' => $totalEmployeesClockedIn,
            'totalEmployeesOnLeave' => $totalEmployeesOnLeave,
            'totalPendingLeaveRequests' => $totalPendingLeaveRequests,
            'totalHoursToday' => $totalHoursToday,
            'remainingHoursToday' => $remainingHoursToday,
            'workedMinutesToday' => $workedMinutesToday,
            'breakTimeToday' => $breakTimeToday,
            'overtimeToday' => $overtimeToday,
            'daysPresent' => $daysPresent,
            'daysAbsent' => $daysAbsent,
            'lateArrivals' => $lateArrivals,
            'announcements' => $announcements,
            'upcomingLeaves' => $upcomingLeaves,
        ];
    }

    private function calculateTotalMinutes($attendance)
    {
        if (!$attendance) return 0;
        $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = $attendance->check_out
            ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
            : Carbon::now('Asia/Kathmandu');
        if ($checkOut->lt($checkIn)) return 0;
        return $checkIn->diffInMinutes($checkOut);
    }

    private function calculateBreakMinutes($attendance)
    {
        if (!$attendance) return 0;
        return BreakRecord::where('attendance_id', $attendance->id)
            ->get()
            ->sum(function ($break) {
                $start = Carbon::parse($break->start_time)->setTimezone('Asia/Kathmandu');
                $end = $break->end_time
                    ? Carbon::parse($break->end_time)->setTimezone('Asia/Kathmandu')
                    : Carbon::now('Asia/Kathmandu');
                return $end->gt($start) ? $start->diffInMinutes($end) : 0;
            });
    }
}
