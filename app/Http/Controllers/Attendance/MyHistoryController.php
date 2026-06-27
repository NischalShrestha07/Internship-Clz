<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MyHistoryController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay()->timezone('Asia/Kathmandu') : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay()->timezone('Asia/Kathmandu') : null;
        $selectedStatus = $request->status;

        $dailyRecords = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->where('attendances.employee_id', Auth::id())
            ->when($selectedStatus, function ($query) use ($selectedStatus) {
                if ($selectedStatus === 'Checked-Out') {
                    $query->whereNotNull('attendances.check_out');
                } elseif ($selectedStatus === 'Working') {
                    $query->where(function ($q) {
                        $q->whereNull('attendances.check_out')
                            ->whereDate('attendances.check_in', Carbon::today());
                    });
                }
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('attendances.check_in', [$dateFrom, $dateTo]);
            })
            ->when($dateFrom && !$dateTo, function ($query) use ($dateFrom) {
                $query->where('attendances.check_in', '>=', $dateFrom);
            })
            ->when($dateTo && !$dateFrom, function ($query) use ($dateTo) {
                $query->where('attendances.check_in', '<=', $dateTo);
            })
            ->orderBy('attendances.check_in', 'desc')
            ->select('attendances.*', 'users.name as employee_name')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format('Y-m-d');
            });

        $processedRecords = collect();

        foreach ($dailyRecords as $date => $records) {
            $stats = $this->calculateDailyStats($records);
            $status = $this->determineStatus($records);

            $processedRecords->push([
                'id' => $records->first()->id,
                'employee_name' => $records->first()->employee_name,
                'date' => Carbon::parse($date)->format('Y-m-d'),
                'created_at' => $records->first()->created_at,
                'status' => $status,
                'check_in' => $stats['earliest_check_in'],
                'check_out' => $stats['latest_check_out'],
                'production' => AttendanceHelper::decimalToHoursMinutes($stats['total_hours']),
                'break' => $stats['break_minutes'],
                'overtime' => $stats['overtime'],
                'record_count' => $records->count()
            ]);
        }

        $perPage = $request->per_page ?? AttendanceHelper::getRecordsPerPage();

        $page = Paginator::resolveCurrentPage();
        $processedRecords = $processedRecords->values();

        $attendanceRecords = new LengthAwarePaginator(
            $processedRecords->forPage($page, $perPage),
            $processedRecords->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('attendance.my-attendance-history', [
            'attendanceRecords' => $attendanceRecords,
        ]);
    }

    public function sessions($id)
    {
        $attendance = Attendance::with('breakRecords')->findOrFail($id);

        $date = Carbon::parse($attendance->check_in)->format('Y-m-d');

        $sessions = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereDate('check_in', $date)
            ->orderBy('check_in')
            ->get()
            ->map(function ($session) {
                $checkIn = Carbon::parse($session->check_in);
                $checkOut = $session->check_out ? Carbon::parse($session->check_out) : null;

                $workHours = '--';
                if ($checkOut) {
                    $totalMinutes = $checkIn->diffInMinutes($checkOut);
                    $breakMinutes = $this->calculateBreakTime($session);
                    $workedMinutes = max(0, $totalMinutes - $breakMinutes);
                    $hours = floor($workedMinutes / 60);
                    $minutes = $workedMinutes % 60;
                    $workHours = sprintf('%02d:%02d', $hours, $minutes);
                }

                return [
                    'check_in' => $checkIn->format('h:i:s A'),
                    'check_out' => $checkOut ? $checkOut->format('h:i:s A') : '--',
                    'work_hours' => $workHours,
                    'break' => AttendanceHelper::decimalToHoursMinutes($this->calculateBreakTime($session) / 60),
                    'status' => $session->check_out ? 'Checked Out' : 'Working'
                ];
            });

        return view('attendance.attendance-sessions', [
            'sessions' => $sessions,
            'date' => $date,
            'employeeName' => $attendance->employee_name ?? Auth::user()->name,
        ]);
    }

    protected function determineStatus($records)
    {
        foreach ($records as $record) {
            if (!$record->check_out && Carbon::parse($record->check_in)->isToday()) {
                return 'Working';
            }
        }
        return 'Checked-Out';
    }

    protected function calculateDailyStats($records)
    {
        $totalMinutes = 0;
        $breakMinutes = 0;
        $earliestCheckIn = null;
        $latestCheckOut = null;
        $storedOvertime = 0;

        foreach ($records as $record) {
            $checkIn = Carbon::parse($record->check_in);
            if (!$earliestCheckIn || $checkIn->lt($earliestCheckIn)) {
                $earliestCheckIn = $checkIn;
            }

            if ($record->check_out) {
                $checkOut = Carbon::parse($record->check_out);
                if (!$latestCheckOut || $checkOut->gt($latestCheckOut)) {
                    $latestCheckOut = $checkOut;
                }
                $totalMinutes += $checkIn->diffInMinutes($checkOut);
            }

            if ($record->breakRecords && $record->breakRecords->isNotEmpty()) {
                $breakMinutes += $record->breakRecords->sum(function ($break) {
                    $start = Carbon::parse($break->start_time);
                    $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                    return $start->diffInMinutes($end);
                });
            } else {
                $breakMinutes += $record->break ?? 0;
            }

            if ($record->overtime) {
                $storedOvertime = $record->overtime;
            }
        }

        $workedMinutes = max(0, $totalMinutes - $breakMinutes);
        $totalHours = $workedMinutes / 60;
        $overtime = $storedOvertime ?: max(0, $totalHours - 8);

        return [
            'earliest_check_in' => $earliestCheckIn,
            'latest_check_out' => $latestCheckOut,
            'total_hours' => $totalHours,
            'break_minutes' => $breakMinutes,
            'overtime' => $overtime
        ];
    }

    protected function calculateBreakTime($record)
    {
        if (!$record->breakRecords || $record->breakRecords->isEmpty()) {
            return 0;
        }

        return $record->breakRecords->sum(function ($break) {
            $start = Carbon::parse($break->start_time);
            $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
            return $start->diffInMinutes($end);
        });
    }
}
