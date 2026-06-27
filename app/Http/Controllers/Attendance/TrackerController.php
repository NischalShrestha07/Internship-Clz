<?php

namespace App\Http\Controllers\Attendance;

use App\Helpers\AttendanceHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Kathmandu');
        $today = $now->copy()->startOfDay();

        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', $user->id)
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        $isClockedIn = $attendances->contains(fn($a) => is_null($a->check_out));

        $isOnBreak = false;
        $currentBreakId = null;
        if ($isClockedIn) {
            $activeBreak = BreakRecord::whereIn('attendance_id', $attendances->pluck('id'))
                ->whereNull('end_time')
                ->first();
            $isOnBreak = !is_null($activeBreak);
            $currentBreakId = $activeBreak?->id;
        }

        $totalHoursDecimal = $this->calculateDailyTotalHours($attendances);
        $totalHours = $this->decimalToHoursMinutes($totalHoursDecimal);
        $breakTime = $this->calculateDailyBreakTime($attendances);

        $overtimeDecimal = max(0, $totalHoursDecimal - 8);
        $overtimeToday = $this->decimalToHoursMinutes($overtimeDecimal);

        $todayActivities = $this->buildTodayActivities($attendances);
        $todayStats = $this->calculateStatistics($user);

        $currentNepalTime = $now->format('h:i:s A');
        $currentDate = $now;

        return view('attendance.tracker', compact(
            'isClockedIn', 'isOnBreak', 'currentBreakId',
            'totalHours', 'breakTime', 'overtimeToday',
            'todayActivities', 'todayStats',
            'currentNepalTime', 'currentDate'
        ));
    }

    public function punchIn(Request $request)
    {
        $now = Carbon::now('Asia/Kathmandu');
        $today = $now->copy()->startOfDay();

        $openAttendance = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->first();

        if ($openAttendance) {
            return redirect()->back()->with('error', 'You already have an active session today');
        }

        $previousOpen = Attendance::where('employee_id', Auth::id())
            ->whereNull('check_out')
            ->whereDate('check_in', '<', $today)
            ->first();

        if ($previousOpen) {
            $previousOpen->update([
                'check_out' => $previousOpen->check_in->copy()->endOfDay(),
            ]);
        }

        Attendance::create([
            'employee_id' => Auth::id(),
            'check_in' => $now,
            'status' => 'present',
            'type' => 'regular',
            'employee_name' => Auth::user()->name,
            'date' => $today,
            'remarks' => '',
        ]);

        return redirect()->back()->with('status', 'Checked in successfully.');
    }

    public function punchOut(Request $request)
    {
        $now = Carbon::now('Asia/Kathmandu');
        $today = $now->copy()->startOfDay();

        $attendance = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active session found.');
        }

        BreakRecord::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->update(['end_time' => $now]);

        $totalHours = $this->calculateAttendanceTotalHours($attendance);
        $attendance->update(['check_out' => $now, 'total_hours' => $totalHours]);

        return redirect()->back()->with('status', 'Checked out successfully.');
    }

    public function startBreak(Request $request)
    {
        $today = Carbon::today('Asia/Kathmandu');

        $attendance = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Please check in first.');
        }

        $activeBreak = BreakRecord::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->first();

        if ($activeBreak) {
            return redirect()->back()->with('error', 'Already on break.');
        }

        BreakRecord::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now('Asia/Kathmandu'),
        ]);

        return redirect()->back()->with('status', 'Break started.');
    }

    public function endBreak(Request $request)
    {
        $today = Carbon::today('Asia/Kathmandu');

        $attendance = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active session.');
        }

        BreakRecord::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->update(['end_time' => Carbon::now('Asia/Kathmandu')]);

        return redirect()->back()->with('status', 'Break ended.');
    }

    // --- Calculation helpers ---

    private function calculateDailyTotalHours($attendances)
    {
        $totalMinutes = 0;
        foreach ($attendances as $attendance) {
            $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
            $checkOut = $attendance->check_out
                ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
                : Carbon::now('Asia/Kathmandu');
            if ($checkOut->lt($checkIn)) continue;
            $totalMinutes += $checkIn->diffInMinutes($checkOut);
            $totalMinutes -= $this->calculateAttendanceBreakMinutes($attendance);
        }
        return max(0, $totalMinutes) / 60;
    }

    private function calculateAttendanceTotalHours($attendance)
    {
        if (!$attendance) return '0.00';
        $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = $attendance->check_out
            ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
            : Carbon::now('Asia/Kathmandu');
        if ($checkOut->lt($checkIn)) return '0.00';
        $workedMinutes = max(0, $checkIn->diffInMinutes($checkOut) - $this->calculateAttendanceBreakMinutes($attendance));
        return number_format($workedMinutes / 60, 2);
    }

    private function calculateAttendanceBreakMinutes($attendance)
    {
        if (!$attendance->relationLoaded('breakRecords')) {
            $breaks = BreakRecord::where('attendance_id', $attendance->id)->get();
        } else {
            $breaks = $attendance->breakRecords;
        }
        return $breaks->sum(function ($break) {
            $start = Carbon::parse($break->start_time)->setTimezone('Asia/Kathmandu');
            $end = $break->end_time
                ? Carbon::parse($break->end_time)->setTimezone('Asia/Kathmandu')
                : Carbon::now('Asia/Kathmandu');
            return $end->gt($start) ? $start->diffInMinutes($end) : 0;
        });
    }

    private function calculateDailyBreakTime($attendances)
    {
        $totalBreakMinutes = 0;
        foreach ($attendances as $attendance) {
            $totalBreakMinutes += $this->calculateAttendanceBreakMinutes($attendance);
        }
        return $this->decimalToHoursMinutes($totalBreakMinutes / 60);
    }

    private function buildTodayActivities($attendances)
    {
        $activities = [];
        foreach ($attendances as $attendance) {
            $activities[] = [
                'action' => 'Check In',
                'time' => $attendance->check_in,
                'duration' => null,
                'timestamp' => Carbon::parse($attendance->check_in)->timestamp,
            ];
            $breaks = BreakRecord::where('attendance_id', $attendance->id)
                ->orderBy('start_time', 'desc')->get();
            foreach ($breaks as $break) {
                if ($break->end_time) {
                    $activities[] = [
                        'action' => 'Break End',
                        'time' => $break->end_time,
                        'duration' => null,
                        'timestamp' => Carbon::parse($break->end_time)->timestamp,
                    ];
                }
                $activities[] = [
                    'action' => 'Break Start',
                    'time' => $break->start_time,
                    'duration' => $break->end_time
                        ? Carbon::parse($break->start_time)->diffInMinutes(Carbon::parse($break->end_time))
                        : 'Ongoing',
                    'timestamp' => Carbon::parse($break->start_time)->timestamp,
                ];
            }
            if ($attendance->check_out) {
                $activities[] = [
                    'action' => 'Check Out',
                    'time' => $attendance->check_out,
                    'duration' => null,
                    'timestamp' => Carbon::parse($attendance->check_out)->timestamp,
                ];
            }
        }
        usort($activities, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        return $activities;
    }

    private function calculateStatistics($user)
    {
        $now = Carbon::now('Asia/Kathmandu');
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $todayHours = $this->calculateDailyTotalHours(
            Attendance::with('breakRecords')
                ->where('employee_id', $user->id)
                ->whereDate('check_in', $now->copy()->startOfDay())
                ->get()
        );

        $weekAttendances = Attendance::with('breakRecords')
            ->where('employee_id', $user->id)
            ->whereBetween('check_in', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(fn($item) => $item->check_in->format('Y-m-d'));

        $weekHours = $weekAttendances->sum(function ($dayAttendances) {
            return $this->calculateDailyTotalHours($dayAttendances);
        });

        $monthAttendances = Attendance::with('breakRecords')
            ->where('employee_id', $user->id)
            ->whereBetween('check_in', [$monthStart, $monthEnd])
            ->get()
            ->groupBy(fn($item) => $item->check_in->format('Y-m-d'));

        $monthHours = $monthAttendances->sum(function ($dayAttendances) {
            return $this->calculateDailyTotalHours($dayAttendances);
        });

        $remainingHours = max(0, 8 - $todayHours);

        return [
            'hours' => AttendanceHelper::decimalToHoursMinutes($todayHours),
            'week_hours' => AttendanceHelper::decimalToHoursMinutes($weekHours),
            'month_hours' => AttendanceHelper::decimalToHoursMinutes($monthHours),
            'remaining_hours' => AttendanceHelper::decimalToHoursMinutes($remainingHours),
            'overtime' => AttendanceHelper::decimalToHoursMinutes(max(0, $todayHours - 8)),
            'hours_raw' => round($todayHours, 2),
            'week_hours_raw' => round($weekHours, 2),
            'month_hours_raw' => round($monthHours, 2),
        ];
    }

    private function decimalToHoursMinutes($decimalHours)
    {
        $decimalHours = is_numeric($decimalHours) ? $decimalHours : 0;
        $totalSeconds = round($decimalHours * 3600);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;
        return sprintf("%dh %02dm %02ds", $hours, $minutes, $seconds);
    }
}
