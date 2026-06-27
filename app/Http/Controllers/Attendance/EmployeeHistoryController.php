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
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeHistoryController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay()->timezone('Asia/Kathmandu') : Carbon::now()->subDays(30)->startOfDay()->timezone('Asia/Kathmandu');
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay()->timezone('Asia/Kathmandu') : Carbon::now()->endOfDay()->timezone('Asia/Kathmandu');
        $selectedStatus = $request->status;
        $search = $request->search;

        $groupedRecords = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->where('attendances.employee_id', '!=', Auth::id())
            ->when($search, function ($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%');
            })
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
            ->orderBy('users.name')
            ->orderBy('attendances.check_in', 'desc')
            ->select('attendances.*', 'users.name as employee_name')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format('Y-m-d') . '|' . $item->employee_id;
            });

        $processedRecords = collect();

        foreach ($groupedRecords as $groupKey => $records) {
            [$date, $employeeId] = explode('|', $groupKey);
            $stats = $this->calculateDailyStats($records);
            $status = $this->determineStatus($records);

            $processedRecords->push([
                'id' => $records->first()->id,
                'employee_id' => $employeeId,
                'employee_name' => $records->first()->employee_name,
                'date' => Carbon::parse($date)->format('Y-m-d'),
                'status' => $status,
                'check_in' => $stats['earliest_check_in'],
                'check_out' => $stats['latest_check_out'],
                'production' => AttendanceHelper::decimalToHoursMinutes($stats['total_hours']),
                'break' => $stats['break_minutes'],
                'overtime' => $stats['overtime'],
                'record_count' => $records->count(),
                'session_details' => $records->map(function ($record) {
                    return [
                        'check_in' => Carbon::parse($record->check_in)->format('h:i:s A'),
                        'check_out' => $record->check_out ? Carbon::parse($record->check_out)->format('h:i:s A') : '--'
                    ];
                })
            ]);
        }

        $processedRecords = $processedRecords->sortBy([
            ['employee_name', 'asc'],
            ['date', 'desc']
        ]);

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

        return view('attendance.employee-attendance-history', [
            'attendanceRecords' => $attendanceRecords,
            'search' => $search ?? '',
            'selectedStatus' => $selectedStatus,
            'dateFrom' => $request->date_from ?? Carbon::now()->subDays(30)->format('Y-m-d'),
            'dateTo' => $request->date_to ?? Carbon::now()->format('Y-m-d'),
        ]);
    }

    public function downloadCSV(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay()->timezone('Asia/Kathmandu') : Carbon::now()->subDays(30)->startOfDay()->timezone('Asia/Kathmandu');
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay()->timezone('Asia/Kathmandu') : Carbon::now()->endOfDay()->timezone('Asia/Kathmandu');
        $selectedStatus = $request->status;
        $search = $request->search;

        $fileName = 'employee_attendance_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($dateFrom, $dateTo, $selectedStatus, $search) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Employee', 'Date', 'Day', 'Status', 'Sessions',
                'First Check-In', 'Last Check-Out', 'Total Hours',
                'Break (min)', 'Overtime (hrs)', 'Details'
            ]);

            $groupedRecords = Attendance::with('breakRecords')
                ->join('users', 'attendances.employee_id', '=', 'users.id')
                ->where('attendances.employee_id', '!=', Auth::id())
                ->when($search, function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%');
                })
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
                ->orderBy('users.name')
                ->orderBy('attendances.check_in', 'desc')
                ->select('attendances.*', 'users.name as employee_name')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->check_in)->format('Y-m-d') . '|' . $item->employee_id;
                });

            foreach ($groupedRecords as $groupKey => $records) {
                [$date, $employeeId] = explode('|', $groupKey);
                $stats = $this->calculateDailyStats($records);
                $status = $this->determineStatus($records);

                $sessionDetails = [];
                foreach ($records as $record) {
                    $checkIn = Carbon::parse($record->check_in)->format('h:i:s A');
                    $checkOut = $record->check_out ? Carbon::parse($record->check_out)->format('h:i:s A') : '--';
                    $sessionDetails[] = "{$checkIn} to {$checkOut}";
                }

                fputcsv($file, [
                    $records->first()->employee_name,
                    Carbon::parse($date)->format('M j, Y'),
                    Carbon::parse($date)->format('D'),
                    $status,
                    count($records),
                    $stats['earliest_check_in']->format('h:i:s A'),
                    $stats['latest_check_out'] ? $stats['latest_check_out']->format('h:i:s A') : '--',
                    AttendanceHelper::decimalToHoursMinutes($stats['total_hours']),
                    AttendanceHelper::minutesToHoursMinutes($stats['break_minutes']),
                    AttendanceHelper::decimalToHoursMinutes($stats['overtime']),
                    implode("\n", $sessionDetails)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
}
