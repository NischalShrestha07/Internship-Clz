<?php

namespace App\Livewire;

use App\Helpers\AttendanceHelper;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\Leave;
use App\Models\EmployeeLeaveConfigure;
use App\Models\User;
use App\Models\CalendarEvent;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardSummary extends Component
{
    public $currentDate;
    public $currentTime;
    public $isClockedIn = false;
    public $isOnBreak = false;
    public $punchInTime;
    public $lastCheckInTime;
    public $totalHoursToday = '0.00';
    public $breakTimeToday = '0 mins';
    public $overtimeToday = '0 hrs';
    public $totalEmployees = 0;
    public $newEmployeesThisMonth = 0;
    public $totalLeaveDays = 0;
    public $usedLeaveDays = 0;
    public $remainingLeaveDays = 0;
    public $pendingLeaveRequests = 0;
    public $approvedLeaveRequests = 0;
    public $upcomingLeaves = [];
    public $leaves_card = [];
    public $weeklyHours = '0.00';
    public $monthlyHours = '0.00';
    public $remainingHoursToday = '8.00';
    public $totalEmployeesClockedIn = 0;
    public $totalPendingLeaveRequests = 0;
    public $totalEmployeesOnLeave = 0;
    public $attendanceChartData = ['labels' => [], 'data' => []];
    public $leaveChartData = ['labels' => [], 'data' => [], 'colors' => []];
    public $teamPerformanceChartData = ['labels' => [], 'data' => []];
    public $recentAttendance = [];
    public $announcements = [];
    public $chartPeriod = 'week';
    public $performanceScore;
    public $leaveTypes = [];
    public $weeklyHoursRaw;
    public $monthlyHoursRaw;

    public $attendanceRate = 0;
    public $lateArrivals = 0;
    public $daysPresent = 0;
    public $daysAbsent = 0;
    public $totalWorkHours = 0;
    public $currentBreakId = null;
    public $breakTime = '0 mins';
    public $totalHours = '0.00';


    public $search = '';
    public $selectedStatus = '';
    public $selectedMonth = '';
    public $selectedYear = '';
    public $months = [];
    public $years = [];
    public $showFilterAttendance = [];

    public string $pageTitle = 'Manage Leave';
    public $selectedLeaveType = '';
    public $leave;
    public $isSubmitted = false;
    public $leaveId, $leave_reason, $start_date_time, $end_date_time, $leave_type, $status, $remarks;
    // Leave statistics
    public $approvedCount = 0;
    public $cancelledCount = 0;
    public $declinedCount = 0;
    public $pendingCount = 0;
    public $isHrManager = false;

    public $todayStats = [
        'hours' => '0.00',
        'week_hours' => '0.00',
        'month_hours' => '0.00',
        'remaining_hours' => '8.00',
        'overtime' => '0.00'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedMonth' => ['except' => ''],
        'selectedYear' => ['except' => ''],
        'selectedStatus' => ['except' => '']
    ];

    // Chart colors
    private $chartColors = [
        'blue' => 'rgba(59, 130, 246, 1)',
        'green' => '#10b981',
        'amber' => '#f59e0b',
        'red' => '#ef4444',
        'purple' => '#8b5cf6',
        'pink' => '#ec4899',
    ];

    public function mount()
    {
        $this->isHrManager = Auth::user()->hasRole('HR Manager');
        $this->loadLeaveStatistics();
        $this->loadLeaveBalances();
        $this->loadUpcomingLeaves();
        $this->currentDate = Carbon::now('Asia/Kathmandu');
        $this->currentTime = $this->currentDate->format('h:i A');
        $this->loadCurrentStatus();
        $this->loadData();
        $this->loadLeaveData();
        $this->loadRecentAttendance();
        $this->loadAnnouncements();
        $this->prepareChartData();
        $this->updateCurrentTime();
    }
    protected function loadLeaveStatistics()
    {
        $query = Leave::query();

        if ($this->isHrManager) {
            $query->where('employee_id', '!=', auth()->id());
        } else {
            $query->where('employee_id', auth()->id());
        }

        $this->approvedCount = (clone $query)->where('status', 'approved')->count();
        $this->cancelledCount = (clone $query)->where('status', 'cancelled')->count();
        $this->declinedCount = (clone $query)->where('status', 'declined')->count();
        $this->pendingCount = (clone $query)->where('status', 'pending')->count();
    }

    protected function loadLeaveBalances()
    {
        $employeeId = $this->isHrManager ? null : auth()->id();

        $leaveConfigs = EmployeeLeaveConfigure::with('leaveType');

        if (!$this->isHrManager) {
            $leaveConfigs->where('employee_id', $employeeId);
        }

        $this->leaves_card = $leaveConfigs->get()->map(function ($leave_card) {
            $query = Leave::where('leave_type_id', $leave_card->leave_type_id)
                ->where('status', 'approved');

            if (!$this->isHrManager) {
                $query->where('employee_id', $leave_card->employee_id);
            }

            $approvedLeaves = $query->get();

            $takenDays = $approvedLeaves->sum(function ($record) {
                $start = Carbon::parse($record->start_date_time);
                $end = Carbon::parse($record->end_date_time);
                return $start->diffInDays($end) + 1;
            });

            $leave_card->remaining = max(0, (int) $leave_card->days - (int) $takenDays);
            return $leave_card;
        });

        $this->totalLeaveDays = $this->leaves_card->sum('days');
        $this->usedLeaveDays = $this->leaves_card->sum(function ($leave) {
            return $leave->days - $leave->remaining;
        });
        $this->remainingLeaveDays = $this->leaves_card->sum('remaining');
        $this->pendingLeaveRequests = Leave::where('status', 'pending')
            ->when(!$this->isHrManager, fn($q) => $q->where('employee_id', auth()->id()))
            ->count();
    }

    protected function loadUpcomingLeaves()
    {
        $query = Leave::with(['leaveType', 'employee'])
            ->where('status', 'approved')
            ->where('start_date_time', '>=', now())
            ->where('start_date_time', '<=', now()->addDays(30))
            ->orderBy('start_date_time');

        if (!$this->isHrManager) {
            $query->where('employee_id', auth()->id());
        }

        $this->upcomingLeaves = $query->get()
            ->map(function ($leave) {
                return [
                    'type' => $leave->leaveType->leave_type,
                    'employee' => $this->isHrManager ? $leave->employee->name : null,
                    'start' => \Carbon\Carbon::parse($leave->start_date_time)->format('M j, Y'),
                    'end' => \Carbon\Carbon::parse($leave->end_date_time)->format('M j, Y'),
                    'days' => \Carbon\Carbon::parse($leave->start_date_time)
                        ->diffInDays(\Carbon\Carbon::parse($leave->end_date_time)) + 1,
                ];
            })->toArray();
    }

    public function updateCurrentTime()
    {
        $this->currentTime = Carbon::now('Asia/Kathmandu')->format('h:i A');
    }
    protected $listeners = [
        'refreshTime' => 'updateCurrentTime',
        'refresh-attendance' => 'refreshAttendance',
        'attendance-updated' => 'updateStatus',
        'refresh-attendance-global' => 'refreshAttendance',
        'end-break-global' => 'endBreak',
    ];
    public function hydrate()
    {
        $this->updateCurrentTime();
    }

    public function setChartPeriod($period)
    {
        $this->chartPeriod = $period;
        $this->prepareChartData();
        $this->dispatch('chartDataUpdated');
    }


    protected function loadLeaveData()
    {
        $user = auth()->user();
        $isHR = $user->hasRole('HR Manager');
        $employeeId = $isHR ? null : $user->id;

        $leaveConfigs = EmployeeLeaveConfigure::with('leaveType');
        if (!$isHR) {
            $leaveConfigs->where('employee_id', $employeeId);
        }

        $this->leaves_card = $leaveConfigs->get()->map(function ($leave_card) use ($isHR) {
            $query = Leave::where('leave_type_id', $leave_card->leave_type_id)
                ->where('status', 'approved');

            if (!$isHR) {
                $query->where('employee_id', $leave_card->employee_id);
            }

            $approvedLeaves = $query->get();

            $takenDays = 0;
            foreach ($approvedLeaves as $record) {
                $start = Carbon::parse($record->start_date_time);
                $end = Carbon::parse($record->end_date_time);
                $takenDays += $start->diffInDays($end) + 1;
            }

            $leave_card->remaining = max(0, (int) $leave_card->days - (int) $takenDays);
            return $leave_card;
        });

        $this->leaveChartData = [
            'labels' => $this->leaves_card->pluck('leaveType.leave_type')->toArray(),
            'data' => $this->leaves_card->map(function ($leave) {
                return $leave->days - $leave->remaining;
            })->toArray(),
            'colors' => $this->leaves_card->map(function ($leave, $index) {
                return $this->chartColors[array_keys($this->chartColors)[$index % count($this->chartColors)]];
            })->toArray()
        ];

        // Fallback for empty leave data
        if (empty($this->leaveChartData['labels'])) {
            $this->leaveChartData = [
                'labels' => ['No Data'],
                'data' => [0],
                'colors' => ['#3b82f6']
            ];
        }

        $upcomingLeavesQuery = Leave::with(['leaveType', 'employee'])
            ->where('status', 'approved')
            ->where('start_date_time', '>=', Carbon::today())
            ->where('start_date_time', '<=', Carbon::today()->addDays(30))
            ->orderBy('start_date_time');

        if (!$isHR) {
            $upcomingLeavesQuery->where('employee_id', $user->id);
        }

        $this->upcomingLeaves = $upcomingLeavesQuery->get()
            ->map(function ($leave) use ($isHR) {
                return [
                    'type' => $leave->leaveType->leave_type,
                    'employee' => $isHR ? $leave->employee->name : null,
                    'start' => Carbon::parse($leave->start_date_time)->format('M j, Y'),
                    'end' => Carbon::parse($leave->end_date_time)->format('M j, Y'),
                    'days' => Carbon::parse($leave->start_date_time)->diffInDays($leave->end_date_time) + 1
                ];
            })->toArray();
    }
    protected function decimalToHoursMinutes($decimalHours)
    {
        // Ensure we're working with a numeric value
        $decimalHours = is_numeric($decimalHours) ? $decimalHours : 0;

        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);

        // Handle cases where minutes might be 60 due to rounding
        if ($minutes >= 60) {
            $hours += 1;
            $minutes = 0;
        }

        // Format with leading zeros and proper labels
        if ($hours > 0 && $minutes > 0) {
            return sprintf("%d hrs %02d mins", $hours, $minutes);
        } elseif ($hours > 0) {
            return sprintf("%d hrs", $hours);
        } else {
            return sprintf("%d mins", $minutes);
        }
    }

    protected function calculatePerformanceScore()
    {
        $baseScore = 85;
        $employeeId = Auth::id();
        $startOfMonth = Carbon::now('Asia/Kathmandu')->startOfMonth();
        $endOfMonth = Carbon::now('Asia/Kathmandu')->endOfMonth();

        // Attendance consistency (max +10 points)
        $attendanceCount = Attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->whereNotNull('check_out')
            ->count();
        $baseScore += min(10, $attendanceCount * 0.5);

        // Late arrivals penalty (max -15 points)
        $lateArrivals = Attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->whereTime('check_in', '>', '09:15:00')
            ->count();
        $baseScore -= min(15, $lateArrivals * 2);

        // Overtime bonus (max +5 points)
        $standardMinutes = 8 * 60; // 8 hours in minutes
        $overtimeMinutes = Attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->select(DB::raw('SUM(
            GREATEST(
                TIMESTAMPDIFF(MINUTE, check_in, COALESCE(check_out, NOW())) - ' . $standardMinutes . ', 0
            )
        ) as total_overtime'))
            ->value('total_overtime') ?? 0;

        $overtimeHours = round($overtimeMinutes / 60, 2);
        $baseScore += min(5, $overtimeHours * 0.5);

        return max(0, min(100, round($baseScore)));
    }

    protected function loadRecentAttendance()
    {
        $isHrManager = Auth::user()->hasRole('HR Manager');
        $employeeId = Auth::id();

        // Get records grouped by date and employee
        $groupedRecords = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->when(!$isHrManager, function ($query) use ($employeeId) {
                $query->where('attendances.employee_id', $employeeId);
            })
            ->orderBy('attendances.check_in', 'desc')
            ->select('attendances.*', 'users.name as employee_name')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format('Y-m-d') . '|' . $item->employee_id;
            });

        // Process each day's records
        $processedRecords = collect();
        foreach ($groupedRecords as $groupKey => $records) {
            [$date, $employeeId] = explode('|', $groupKey);
            $stats = $this->calculateDailyStats($records);
            $status = $this->determineStatus($records);

            $processedRecords->push([
                'id' => $records->first()->id,
                'employee_name' => $records->first()->employee_name,
                'date' => Carbon::parse($date)->format('Y-m-d'),
                'status' => $status,
                'check_in' => $stats['earliest_check_in'],
                'check_out' => $stats['latest_check_out'],
                'production' => $stats['total_hours'],
                'break' => $stats['break_minutes'],
                'overtime' => $stats['overtime'],
                'record_count' => $records->count(),
                'created_at' => $records->first()->created_at
            ]);
        }

        $this->recentAttendance = $processedRecords->take(5)->toArray();
    }


    protected function loadAnnouncements()
    {
        $this->announcements = CalendarEvent::orderBy('date', 'desc')
            ->where('date', '>=', Carbon::today()->subDays(30))
            ->take(3)
            ->get()
            ->map(function ($announcement) {
                return [
                    'title' => $announcement->title,
                    'date' => Carbon::parse($announcement->date),
                    'description' => $announcement->description,
                    'type' => $announcement->type,
                    'priority' => $announcement->priority,
                    'time' => $announcement->time,
                    'created_by' => $announcement->user->name ?? 'N/A',
                    'posted_at' => Carbon::parse($announcement->created_at)->diffForHumans()
                ];
            })->toArray();
    }


    protected function prepareChartData()
    {
        $employeeId = Auth::id();
        $days = $this->chartPeriod === 'week' ? 7 : ($this->chartPeriod === 'month' ? 30 : 365);
        $startDate = Carbon::now('Asia/Kathmandu')->subDays($days);

        // Attendance chart data
        $attendanceData = Attendance::with('breakRecords')
            ->where('employee_id', $employeeId)
            ->where('check_in', '>=', $startDate)
            ->orderBy('check_in')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format($this->chartPeriod === 'week' ? 'D' : ($this->chartPeriod === 'month' ? 'M d' : 'M Y'));
            });

        $this->attendanceChartData = [
            'labels' => [],
            'data' => []
        ];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = Carbon::now('Asia/Kathmandu')->subDays($i);
            $dayKey = $day->format($this->chartPeriod === 'week' ? 'D' : ($this->chartPeriod === 'month' ? 'M d' : 'M Y'));
            $dayDate = $day->format('Y-m-d');

            $this->attendanceChartData['labels'][] = $dayKey;
            $hours = 0;

            if (isset($attendanceData[$dayKey])) {
                foreach ($attendanceData[$dayKey] as $attendance) {
                    $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
                    $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu') : Carbon::now('Asia/Kathmandu');
                    $hours += $checkOut->diffInHours($checkIn);

                    if ($attendance->breakRecords && $attendance->breakRecords->isNotEmpty()) {
                        $breakMinutes = $attendance->breakRecords->sum(function ($break) {
                            $start = Carbon::parse($break->start_time)->setTimezone('Asia/Kathmandu');
                            $end = $break->end_time ? Carbon::parse($break->end_time)->setTimezone('Asia/Kathmandu') : Carbon::now('Asia/Kathmandu');
                            return $start->diffInMinutes($end);
                        });
                        $hours -= ($breakMinutes / 60);
                    }
                }
            }

            $this->attendanceChartData['data'][] = round(max(0, $hours), 2);
        }

        // Ensure non-empty data
        if (empty($this->attendanceChartData['labels'])) {
            $this->attendanceChartData['labels'] = ['No Data'];
            $this->attendanceChartData['data'] = [0];
        }

        // Team performance chart data (for HR Managers)
        if (Auth::user()->hasRole('HR Manager')) {
            $this->teamPerformanceChartData = [
                'labels' => [],
                'data' => []
            ];

            for ($i = $days - 1; $i >= 0; $i--) {
                $day = Carbon::now('Asia/Kathmandu')->subDays($i);
                $dayKey = $day->format($this->chartPeriod === 'week' ? 'D' : ($this->chartPeriod === 'month' ? 'M d' : 'M Y'));
                $dayDate = $day->format('Y-m-d');

                $this->teamPerformanceChartData['labels'][] = $dayKey;
                $this->teamPerformanceChartData['data'][] = Attendance::whereDate('check_in', $dayDate)
                    ->whereNull('check_out')
                    ->distinct('employee_id')
                    ->count();
            }

            // Ensure non-empty data
            if (empty($this->teamPerformanceChartData['labels'])) {
                $this->teamPerformanceChartData['labels'] = ['No Data'];
                $this->teamPerformanceChartData['data'] = [0];
            }
        }

        // Ensure leave chart data is non-empty
        if (empty($this->leaveChartData['labels'])) {
            $this->leaveChartData = [
                'labels' => ['No Data'],
                'data' => [0],
                'colors' => ['#3b82f6']
            ];
        }
    }

    protected function calculateTotalHours($attendance)
    {
        if (!$attendance || !$attendance->check_in) {
            return 0.00;
        }

        $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = $attendance->check_out
            ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
            : Carbon::now('Asia/Kathmandu');

        // If check-out is before check-in (invalid), return 0
        if ($checkOut->lt($checkIn)) {
            return 0.00;
        }

        // Calculate total minutes between check-in and check-out
        $totalMinutes = $checkIn->diffInMinutes($checkOut);

        // Subtract break time (convert to minutes if needed)
        $breakMinutes = $this->calculateBreakTime($attendance, true);
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);

        return round($workedMinutes / 60, 2); // Return hours with 2 decimal places
    }


    protected function calculateTodayHours($attendances)
    {
        $totalMinutes = 0;

        foreach ($attendances as $attendance) {
            $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
            $checkOut = $attendance->check_out
                ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
                : Carbon::now('Asia/Kathmandu');

            if ($checkOut->lt($checkIn)) {
                continue;
            }

            $totalMinutes += $checkIn->diffInMinutes($checkOut);

            $breakMinutes = $this->calculateBreakTime($attendance, true);
            $totalMinutes -= $breakMinutes;
        }

        return round(max(0, $totalMinutes) / 60, 2);
    }

    protected function calculateTotalWorkHours()
    {
        $employeeId = Auth::id();

        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', $employeeId)
            ->get();

        $totalMinutes = 0;

        foreach ($attendances as $attendance) {
            $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
            $checkOut = $attendance->check_out
                ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
                : Carbon::now('Asia/Kathmandu');

            if ($checkOut->lt($checkIn)) {
                continue;
            }

            $totalMinutes += $checkIn->diffInMinutes($checkOut);

            $breakMinutes = $this->calculateBreakTime($attendance, true);
            $totalMinutes -= $breakMinutes;
        }

        return round(max(0, $totalMinutes) / 60, 2);
    }



    protected function calculatePeriodHours($period) // 'week' or 'month'
    {
        $employeeId = Auth::id();
        $startDate = $period === 'week'
            ? Carbon::now('Asia/Kathmandu')->startOfWeek()
            : Carbon::now('Asia/Kathmandu')->startOfMonth();

        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', $employeeId)
            ->where('check_in', '>=', $startDate)
            ->orderBy('check_in')
            ->get()
            ->groupBy(function ($attendance) {
                return Carbon::parse($attendance->check_in)->format('Y-m-d');
            });

        $totalHours = 0;

        // Process each day to avoid double-counting multiple check-ins
        foreach ($attendances as $day => $dayAttendances) {
            $dailyHours = 0;
            foreach ($dayAttendances as $attendance) {
                $dailyHours += $this->calculateTotalHours($attendance);
            }
            $totalHours += min($dailyHours, 24); // Cap daily hours to prevent unrealistic totals
        }

        // Return raw float instead of formatted string
        return $totalHours;
    }
    protected function calculateDailyStats($records)
    {
        $totalMinutes = 0;
        $breakMinutes = 0;
        $earliestCheckIn = null;
        $latestCheckOut = null;

        foreach ($records as $record) {
            // Track earliest check-in
            $checkIn = Carbon::parse($record->check_in);
            if (!$earliestCheckIn || $checkIn->lt($earliestCheckIn)) {
                $earliestCheckIn = $checkIn;
            }

            // Track latest check-out if exists
            if ($record->check_out) {
                $checkOut = Carbon::parse($record->check_out);
                if (!$latestCheckOut || $checkOut->gt($latestCheckOut)) {
                    $latestCheckOut = $checkOut;
                }

                // Add to total worked time
                $totalMinutes += $checkIn->diffInMinutes($checkOut);
            }

            // Sum break time
            $breakMinutes += $record->breakRecords->sum(function ($break) {
                $start = Carbon::parse($break->start_time);
                $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                return $start->diffInMinutes($end);
            });
        }

        // Calculate worked time (subtract breaks)
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);
        $totalHours = $workedMinutes / 60;

        // Overtime calculation (anything over 8 hours)
        $overtime = max(0, $totalHours - 8);

        return [
            'earliest_check_in' => $earliestCheckIn,
            'latest_check_out' => $latestCheckOut,
            'total_hours' => $totalHours,
            'break_minutes' => $breakMinutes,
            'overtime' => $overtime
        ];
    }

    protected function determineStatus($records)
    {
        // If any record for today doesn't have check_out, status is "Working"
        foreach ($records as $record) {
            if (!$record->check_out && Carbon::parse($record->check_in)->isToday()) {
                return 'Working';
            }
        }
        return 'Checked-Out';
    }


    public function loadTodayActivities()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in')
            ->get();

        $activities = [];

        // Group records by check-in/check-out pairs
        foreach ($attendances as $attendance) {
            // Add Check in
            $activities[] = [
                'type' => 'check_in',
                'time' => $attendance->check_in,
                'record' => $attendance,
                'timestamp' => Carbon::parse($attendance->check_in)->timestamp
            ];

            // Process breaks
            foreach ($attendance->breakRecords as $break) {
                // Add Break Start
                $activities[] = [
                    'type' => 'break_start',
                    'time' => $break->start_time,
                    'record' => $break,
                    'timestamp' => Carbon::parse($break->start_time)->timestamp
                ];

                // Add Break End if exists
                if ($break->end_time) {
                    $activities[] = [
                        'type' => 'break_end',
                        'time' => $break->end_time,
                        'record' => $break,
                        'timestamp' => Carbon::parse($break->end_time)->timestamp
                    ];
                }
            }

            // Add check out if exists
            if ($attendance->check_out) {
                $activities[] = [
                    'type' => 'check_out',
                    'time' => $attendance->check_out,
                    'record' => $attendance,
                    'timestamp' => Carbon::parse($attendance->check_out)->timestamp
                ];
            }
        }

        // Sort all activities by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp']; // Newest first
        });

        $this->todayActivities = $activities;
    }

    public function calculateStatistics()
    {
        $today = Carbon::today('Asia/Kathmandu');

        // Today's hours - already correct using calculateDailyTotalHours()
        $todayHours = $this->calculateDailyTotalHours();
        $this->todayStats['hours'] = number_format($todayHours, 2);

        $remainingHours = max(0, 8 - $todayHours);
        $this->todayStats['remaining_hours'] = number_format($remainingHours, 2);

        // Weekly hours - only for current week
        $weekStart = Carbon::now('Asia/Kathmandu')->startOfWeek();
        $weekEnd = Carbon::now('Asia/Kathmandu')->endOfWeek();

        $weekHours = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereBetween('check_in', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(function ($item) {
                return $item->check_in->format('Y-m-d');
            })
            ->sum(function ($dayAttendances) {
                $dayMinutes = 0;

                foreach ($dayAttendances as $attendance) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out) : Carbon::now();

                    if ($checkOut->gt($checkIn)) {
                        $dayMinutes += $checkIn->diffInMinutes($checkOut);

                        // Subtract break time
                        $breakMinutes = $attendance->breakRecords->sum(function ($break) {
                            $start = Carbon::parse($break->start_time);
                            $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                            return $start->diffInMinutes($end);
                        });

                        $dayMinutes -= $breakMinutes;
                    }
                }

                return $dayMinutes / 60;
            });

        $this->todayStats['week_hours'] = number_format($weekHours, 2);

        // Monthly hours - only for current month
        $monthStart = Carbon::now('Asia/Kathmandu')->startOfMonth();
        $monthEnd = Carbon::now('Asia/Kathmandu')->endOfMonth();

        $monthHours = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereBetween('check_in', [$monthStart, $monthEnd])
            ->get()
            ->groupBy(function ($item) {
                return $item->check_in->format('Y-m-d');
            })
            ->sum(function ($dayAttendances) {
                $dayMinutes = 0;

                foreach ($dayAttendances as $attendance) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out) : Carbon::now();

                    if ($checkOut->gt($checkIn)) {
                        $dayMinutes += $checkIn->diffInMinutes($checkOut);

                        $breakMinutes = $attendance->breakRecords->sum(function ($break) {
                            $start = Carbon::parse($break->start_time);
                            $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                            return $start->diffInMinutes($end);
                        });

                        $dayMinutes -= $breakMinutes;
                    }
                }

                return $dayMinutes / 60;
            });

        $this->todayStats['month_hours'] = number_format($monthHours, 2);

        // Overtime calculation - only for current month
        $monthOvertime = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereNotNull('check_out')
            ->whereBetween('check_in', [$monthStart, $monthEnd])
            ->get()
            ->groupBy(function ($item) {
                return $item->check_in->format('Y-m-d');
            })
            ->sum(function ($dayAttendances) {
                $dayMinutes = 0;

                foreach ($dayAttendances as $attendance) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $checkOut = Carbon::parse($attendance->check_out);

                    if ($checkOut->gt($checkIn)) {
                        $dayMinutes += $checkIn->diffInMinutes($checkOut);

                        $breakMinutes = $attendance->breakRecords->sum(function ($break) {
                            $start = Carbon::parse($break->start_time);
                            $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                            return $start->diffInMinutes($end);
                        });

                        $dayMinutes -= $breakMinutes;
                    }
                }

                return max(0, ($dayMinutes / 60) - 8); // Overtime for the day
            });

        $this->todayStats['overtime'] = number_format($monthOvertime, 2);
    }


    protected function loadData()
    {
        $employeeId = Auth::id();
        $today = Carbon::today('Asia/Kathmandu');
        $isHrManager = Auth::user()->hasRole('HR Manager') ?? false;

        // Employee Data
        $this->totalEmployees = User::role('Employee')->count();
        $this->newEmployeesThisMonth = User::role('Employee')
            ->where('created_at', '>=', Carbon::now('Asia/Kathmandu')->startOfMonth())
            ->count();


        $this->pendingLeaveRequests = Leave::where('status', 'pending')
            ->when(!$this->isHrManager, fn($q) => $q->where('employee_id', auth()->id()))
            ->count();

        $this->approvedLeaveRequests = Leave::where('status', 'approved')
            ->when(!$this->isHrManager, fn($q) => $q->where('employee_id', auth()->id()))
            ->count();

        // Today's attendance data
        $todayAttendances = Attendance::with('breakRecords')
            ->where('employee_id', $employeeId)
            ->whereDate('check_in', $today)
            ->get();

        if ($todayAttendances->isNotEmpty()) {
            $this->isClockedIn = $todayAttendances->contains(function ($attendance) {
                return is_null($attendance->check_out);
            });

            $this->punchInTime = $todayAttendances->max(function ($attendance) {
                return Carbon::parse($attendance->check_in);
            });
            $this->lastCheckInTime = $this->punchInTime;

            // Check if any attendance record has an active break
            $this->isOnBreak = $todayAttendances->contains(function ($attendance) {
                return $attendance->breakRecords && $attendance->breakRecords->contains(function ($break) {
                    return is_null($break->end_time);
                });
            });

            $totalBreakMinutes = $todayAttendances->sum(function ($attendance) {
                return $this->calculateBreakTime($attendance, true);
            });
            $this->breakTimeToday = $totalBreakMinutes . ' mins';

            $this->totalHoursToday = $this->calculateTodayHours($todayAttendances);

            $this->totalWorkHours = $this->calculateTotalWorkHours();
        } else {
            $this->isClockedIn = false;
            $this->totalHoursToday = 0.00;
            $this->totalWorkHours = 0.00; // Initialize total work hours
            $this->breakTimeToday = '0 mins';
            $this->overtimeToday = '0 hrs';
            $this->remainingHoursToday = 8.00;
            $this->punchInTime = null;
            $this->lastCheckInTime = null;
        }

        // Weekly and Monthly Hours
        // $this->weeklyHours = number_format($this->calculatePeriodHours('week'), 2);
        // $this->monthlyHours = number_format($this->calculatePeriodHours('month'), 2);

        // Calculate and store raw values
        $this->weeklyHoursRaw = $this->calculatePeriodHours('week');
        $this->monthlyHoursRaw = $this->calculatePeriodHours('month');

        // Format for display
        $this->weeklyHours = $this->decimalToHoursMinutes($this->weeklyHoursRaw);
        $this->monthlyHours = $this->decimalToHoursMinutes($this->monthlyHoursRaw);
        // Attendance rate calculation
        $startOfMonth = Carbon::now('Asia/Kathmandu')->startOfMonth();
        $endOfMonth = Carbon::now('Asia/Kathmandu')->endOfMonth();

        $totalWorkDays = 0;
        $currentDay = $startOfMonth->copy();

        while ($currentDay <= $endOfMonth) {
            if (!$currentDay->isWeekend()) {
                $totalWorkDays++;
            }
            $currentDay->addDay();
        }

        $presentDays = Attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->whereNotNull('check_out')
            ->select(DB::raw('DATE(check_in) as date'))
            ->distinct()
            ->count();

        $this->daysPresent = $presentDays;
        $this->daysAbsent = max(0, $totalWorkDays - $presentDays);
        $this->attendanceRate = $totalWorkDays > 0 ? round(($presentDays / $totalWorkDays) * 100) : 100;

        // Late Arrivals
        $this->lateArrivals = Attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->whereTime('check_in', '>', '09:15:00')
            ->count();

        // Leave Stats
        $leaveAllocations = EmployeeLeaveConfigure::where('employee_id', $employeeId)->get();
        $this->totalLeaveDays = $leaveAllocations->sum('days');

        $approvedLeaves = Leave::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->get();

        $this->usedLeaveDays = $approvedLeaves->sum(function ($leave) {
            $start = Carbon::parse($leave->start_date_time);
            $end = Carbon::parse($leave->end_date_time);
            return $start->diffInDays($end) + 1;
        });

        $this->remainingLeaveDays = max(0, $this->totalLeaveDays - $this->usedLeaveDays);
        $this->pendingLeaveRequests = Leave::where('employee_id', $employeeId)
            ->where('status', 'pending')
            ->count();

        $this->performanceScore = $this->calculatePerformanceScore();

        // HR Manager Stats
        if ($isHrManager) {
            $this->totalEmployeesClockedIn = Attendance::whereDate('check_in', $today)
                ->whereNull('check_out')
                ->distinct('employee_id')
                ->count();

            $this->totalPendingLeaveRequests = Leave::where('status', 'pending')
                ->count();

            $this->totalEmployeesOnLeave = Leave::where('status', 'approved')
                ->whereDate('start_date_time', '<=', $today)
                ->whereDate('end_date_time', '>=', $today)
                ->distinct('employee_id')
                ->count();
        }
    }
    protected function calculateOvertime($totalHours)
    {
        $standardHours = 8; // Standard working hours per day
        return max(0, $totalHours - $standardHours);
    }


    protected function calculateBreakTime($attendance, $inMinutes = false)
    {
        if (!$attendance || !$attendance->breakRecords || $attendance->breakRecords->isEmpty()) {
            return $inMinutes ? 0 : '0 mins';
        }

        $breakMinutes = $attendance->breakRecords->sum(function ($break) {
            $start = Carbon::parse($break->start_time)->setTimezone('Asia/Kathmandu');
            $end = $break->end_time
                ? Carbon::parse($break->end_time)->setTimezone('Asia/Kathmandu')
                : Carbon::now('Asia/Kathmandu');
            return $start->diffInMinutes($end);
        });

        return $inMinutes ? $breakMinutes : ($breakMinutes . ' mins');
    }

    protected function calculateDailyTotalHours()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in')
            ->get();

        $totalMinutes = 0;

        foreach ($attendances as $attendance) {
            $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
            $checkOut = $attendance->check_out
                ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
                : Carbon::now('Asia/Kathmandu');

            if ($checkOut->lt($checkIn)) continue;

            $totalMinutes += $checkIn->diffInMinutes($checkOut);
            $totalMinutes -= $this->calculateBreakTime($attendance, true);
        }

        $this->totalHours = $this->decimalToHoursMinutes(max(0, $totalMinutes) / 60);
    }

    protected function calculateDailyBreakTime($attendances)
    {
        $totalBreakMinutes = 0;

        foreach ($attendances as $attendance) {
            $totalBreakMinutes += $this->calculateBreakTime($attendance, true);
        }

        return $this->decimalToHoursMinutes($totalBreakMinutes / 60);
    }

    protected function calculateDailyOvertime($totalHours)
    {
        $standardHours = 8;
        $overtime = max(0, $totalHours - $standardHours);
        return $this->decimalToHoursMinutes($overtime);
    }

    protected function loadCurrentStatus()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        $this->isClockedIn = $attendances->contains(function ($attendance) {
            return is_null($attendance->check_out);
        });

        $latestAttendance = $attendances->first();
        $this->punchInTime = $latestAttendance ? Carbon::parse($latestAttendance->check_in) : null;

        $this->isOnBreak = false;
        $this->currentBreakId = null;

        if ($this->isClockedIn) {
            $activeBreak = BreakRecord::whereIn('attendance_id', $attendances->pluck('id'))
                ->whereNull('end_time')
                ->first();

            $this->isOnBreak = !is_null($activeBreak);
            $this->currentBreakId = $activeBreak ? $activeBreak->id : null;
        }

        $stats = $this->calculateDailyStats($attendances);
        $this->totalHours = $this->decimalToHoursMinutes($stats['total_hours']);
        $this->breakTime = $this->decimalToHoursMinutes($stats['break_minutes'] / 60);
        $this->overtimeToday = $this->decimalToHoursMinutes($stats['overtime']);
    }


    public function refreshAttendance()
    {
        $this->loadCurrentStatus();
        $this->loadData();
        $this->dispatch('refreshComponent')->self();
    }

    public function updateStatus($isClockedIn)
    {
        $this->isClockedIn = $isClockedIn;
        $this->loadCurrentStatus();
        $this->dispatch('refreshComponent')->self();
    }
    // Add these methods to DashboardSummary class:

    public function punchIn()
    {
        try {
            $now = Carbon::now('Asia/Kathmandu');
            $today = $now->copy()->startOfDay();

            $openAttendance = Attendance::where('employee_id', Auth::id())
                ->whereDate('check_in', $today)
                ->whereNull('check_out')
                ->first();

            if ($openAttendance) {
                $this->dispatch('alert', type: 'error', message: 'You already have an active session today');
                return;
            }

            $previousOpenAttendance = Attendance::where('employee_id', Auth::id())
                ->whereNull('check_out')
                ->whereDate('check_in', '<', $today)
                ->first();

            if ($previousOpenAttendance) {
                $previousOpenAttendance->update([
                    'check_out' => $previousOpenAttendance->check_in->copy()->endOfDay(),
                    'total_hours' => $this->calculateTotalHours($previousOpenAttendance)
                ]);
            }

            $attendance = Attendance::create([
                'employee_id' => Auth::id(),
                'check_in' => $now,
                'status' => 'present',
                'type' => 'regular',
                'employee_name' => Auth::user()->name,
                'date' => $today,
                'remarks' => '',
            ]);

            $this->isClockedIn = true;
            $this->punchInTime = $now;
            $this->isOnBreak = false;
            $this->currentBreakId = null;

            $this->refreshAll();
            $this->dispatch('update-attendance-status', isClockedIn: true);
            $this->dispatch('show-notification', 'Successfully Checked-In at ' . $now->format('h:i A'));
        } catch (\Exception $e) {
            $this->dispatch('show-notification', 'Error CheckIn in: ' . $e->getMessage());
        }
    }

    public function punchOut()
    {
        try {
            $now = Carbon::now('Asia/Kathmandu');
            $today = $now->copy()->startOfDay();

            $attendance = Attendance::where('employee_id', Auth::id())
                ->whereDate('check_in', $today)
                ->whereNull('check_out')
                ->orderBy('check_in', 'desc')
                ->first();

            if (!$attendance) {
                $this->dispatch('show-notification', 'No active attendance record found to check out');
                return;
            }

            if ($this->isOnBreak) {
                BreakRecord::where('attendance_id', $attendance->id)
                    ->whereNull('end_time')
                    ->update(['end_time' => $now]);
            }

            $breakMinutes = $this->calculateBreakTime($attendance, true);
            $totalHours = $this->calculateTotalHours($attendance);

            $attendance->update([
                'check_out' => $now,
                'total_hours' => $totalHours,
                'break' => $breakMinutes,
                'overtime' => max(0, $totalHours - 8)
            ]);

            $this->isClockedIn = false;
            $this->isOnBreak = false;
            $this->currentBreakId = null;

            $this->refreshAll();
            $this->dispatch('update-attendance-status', isClockedIn: false);
            $this->dispatch('show-notification', 'Successfully Checked-Out at ' . $now->format('h:i A'));
        } catch (\Exception $e) {
            $this->dispatch('show-notification', 'Error Checking out: ' . $e->getMessage());
        }
    }
    protected function refreshAll()
    {
        $this->loadCurrentStatus();
        $this->loadTodayActivities();
        $this->calculateStatistics();
        $this->dispatch('refreshComponent')->self();
        $this->dispatch('refresh-attendance-global');
        $this->dispatch('attendance-updated', $this->isClockedIn);
    }


    public function startBreak()
    {
        try {
            if (!$this->isClockedIn) {
                $this->dispatch('show-notification', 'Please check in before starting a break!');
                return;
            }

            if ($this->isOnBreak) {
                $this->dispatch('show-notification', 'You are already on a break');
                return;
            }

            $today = Carbon::today('Asia/Kathmandu');
            $attendance = Attendance::where('employee_id', Auth::id())
                ->whereDate('check_in', $today)
                ->whereNull('check_out')
                ->orderBy('check_in', 'desc')
                ->first();

            if ($attendance) {
                $break = BreakRecord::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => Carbon::now('Asia/Kathmandu'),
                ]);

                $this->isOnBreak = true;
                $this->currentBreakId = $break->id;

                $this->loadCurrentStatus();
                $this->dispatch('refreshComponent')->self();
                $this->dispatch('refresh-attendance-global');
                $this->dispatch('show-notification', 'Break started at ' . Carbon::now('Asia/Kathmandu')->format('h:i A'));
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', 'Error starting break: ' . $e->getMessage());
        }
    }

    public function endBreak()
    {
        try {
            if (!$this->isOnBreak) {
                $this->dispatch('show-notification', 'No active break to end');
                return;
            }

            $today = Carbon::today('Asia/Kathmandu');
            $attendance = Attendance::where('employee_id', Auth::id())
                ->whereDate('check_in', $today)
                ->whereNull('check_out')
                ->first();

            if (!$attendance) {
                $this->dispatch('show-notification', 'No active attendance record found');
                return;
            }

            $break = BreakRecord::where('id', $this->currentBreakId)
                ->where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->first();

            if ($break) {
                $now = Carbon::now('Asia/Kathmandu');
                $break->update(['end_time' => $now]);

                $this->isOnBreak = false;
                $this->currentBreakId = null;

                $this->refreshAll();
                $this->dispatch('refresh-attendance-global');
                $this->dispatch('show-notification', 'Break ended at ' . $now->format('h:i A'));
            } else {
                $this->dispatch('show-notification', 'No active break found to end');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', 'Error ending break: ' . $e->getMessage());
        }
    }

    // Update the render method to use consolidated records
    public function render()
    {
        $isHrManager = Auth::user()->hasRole('HR Manager');
        $employeeId = $isHrManager ? null : Auth::id();
        $perPage = AttendanceHelper::getRecordsPerPage();

        // First get all attendance records grouped by date and employee
        $dailyRecords = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->when(!$isHrManager, function ($query) use ($employeeId) {
                $query->where('attendances.employee_id', $employeeId);
            })
            ->when($this->search, function ($query) {
                $query->where('users.name', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedStatus, function ($query) {
                if ($this->selectedStatus === 'Checked-Out') {
                    $query->whereNotNull('attendances.check_out');
                } elseif ($this->selectedStatus === 'Working') {
                    $query->whereNull('attendances.check_out')
                        ->whereDate('attendances.check_in', Carbon::today());
                }
            })
            ->when($this->selectedMonth && $this->selectedYear, function ($query) {
                $query->whereMonth('attendances.check_in', $this->selectedMonth)
                    ->whereYear('attendances.check_in', $this->selectedYear);
            })
            ->orderBy('attendances.check_in', 'desc')
            ->select('attendances.*', 'users.name as employee_name')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format('Y-m-d') . '|' . $item->employee_id;
            });

        // Now process each day's records for each employee
        $processedRecords = collect();

        foreach ($dailyRecords as $groupKey => $records) {
            [$date, $employeeId] = explode('|', $groupKey);
            $earliestCheckIn = $records->min('check_in');
            $latestCheckOut = $records->max('check_out');
            $status = $latestCheckOut ? 'Checked-Out' : 'Working';

            // Calculate totals for the day
            $totalMinutes = 0;
            $breakMinutes = 0;

            foreach ($records as $record) {
                if ($record->check_out) {
                    $checkIn = Carbon::parse($record->check_in);
                    $checkOut = Carbon::parse($record->check_out);
                    $totalMinutes += $checkIn->diffInMinutes($checkOut);
                }

                // Sum break time
                $breakMinutes += $record->breakRecords->sum(function ($break) {
                    $start = Carbon::parse($break->start_time);
                    $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                    return $start->diffInMinutes($end);
                });
            }

            // Subtract break time from total time
            $workedMinutes = max(0, $totalMinutes - $breakMinutes);
            $totalHours = $workedMinutes / 60;

            // Calculate overtime
            $overtime = max(0, $totalHours - 8);

            $processedRecords->push([
                'id' => $records->first()->id, // Use first record's ID for reference
                'employee_name' => $records->first()->employee_name,
                'date' => Carbon::parse($date)->format('Y-m-d'),
                'created_at' => $records->first()->created_at,
                'status' => $status,
                'check_in' => $earliestCheckIn,
                'check_out' => $latestCheckOut,
                'production' => $totalHours,
                'break' => $breakMinutes,
                'overtime' => $overtime,
                'record_count' => $records->count() // For debugging
            ]);
        }
        // Paginate the processed records
        $attendanceRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedRecords->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 5),
            $processedRecords->count(),
            5,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $this->currentTime = Carbon::now('Asia/Kathmandu')->format('h:i A');
        $this->leaveTypes = LeaveType::all();

        $query = Leave::with(['user', 'leaveType'])
            ->when($this->isHrManager, fn($q) => $q->where('employee_id', '!=', auth()->id()))
            ->when(!$this->isHrManager, fn($q) => $q->where('employee_id', auth()->id()))
            ->when($this->search, fn($q) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%")))
            ->when($this->selectedLeaveType, fn($q) => $q->where('leave_type_id', $this->selectedLeaveType))
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        return view('livewire.dashboard-summary', [
            'isHrManager' => $isHrManager,
            'attendanceChartData' => $this->attendanceChartData,
            'leaveChartData' => $this->leaveChartData,
            'teamPerformanceChartData' => $this->teamPerformanceChartData,
            'recentAttendance' => $this->recentAttendance,
            'announcements' => $this->announcements,
            'totalEmployees' => $this->totalEmployees,
            'newEmployeesThisMonth' => $this->newEmployeesThisMonth,
            'performanceScore' => $this->performanceScore,
            'attendanceRecords' => $attendanceRecords,
            'months' => $this->months,
            'years' => $this->years,
            'showFilterAttendance' => $this->showFilterAttendance,
            'selectedStatus' => $this->selectedStatus,
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
            'search' => $this->search ?? '',
            'leaves' => $query->paginate($perPage),
            'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
            'leaveTypes' => $this->leaveTypes,
            'statuses' => ['Checked-Out', 'Working'],
            'currentNepalTime' => $this->currentTime
        ]);
    }
}
