<?php

namespace App\Livewire\App;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public $isClockedIn = false;
    public $isOnBreak = false;
    public $punchInTime;
    public $totalHours = '0.00';
    public $breakTime = '0 mins';
    public $overtimeToday = '0 hrs';
    public $todayActivities = [];
    public $attendanceRecords = [];
    public $selectedMonth;
    public $selectedYear;
    public $currentDate;
    public $currentTime;
    public $currentBreakId = null;
    public $system_info;
    public $employee;

    public $todayStats = [
        'hours' => '0.00',
        'week_hours' => '0.00',
        'month_hours' => '0.00',
        'remaining_hours' => '8.00',
        'overtime' => '0.00'
    ];

    protected $listeners = [
        'attendance-updated' => 'updateStatus',
        'refresh-attendance-global' => 'refreshHeader'
    ];

    public function updateStatus($isClockedIn)
    {
        $this->isClockedIn = $isClockedIn;
        $this->loadCurrentStatus(); // Reload to ensure consistency
        $this->dispatch('refreshComponent')->self();
    }

    public function refreshHeader()
    {
        $this->loadCurrentStatus();
        $this->loadTodayActivities();
        $this->loadAttendanceRecords();
        $this->calculateStatistics();
        $this->dispatch('refreshComponent')->self();
    }
    public function mount()
    {
        $this->currentDate = Carbon::now('Asia/Kathmandu');
        $this->currentTime = $this->currentDate->format('h:i A');
        $this->selectedMonth = $this->currentDate->format('m');
        $this->selectedYear = $this->currentDate->format('Y');

        $this->loadCurrentStatus();
        $this->loadTodayActivities();
        $this->loadAttendanceRecords();
        $this->calculateStatistics();
        $this->system_info = SystemSetting::first();
        $this->employee = Employee::where('employee_id', auth()->id())->first();
    }

    public function loadCurrentStatus()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendance = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->orderBy('check_in', 'desc')
            ->first();

        if ($attendance) {
            $this->punchInTime = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
            $this->isClockedIn = true;

            $activeBreak = BreakRecord::where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->first();

            $this->isOnBreak = !is_null($activeBreak);
            $this->currentBreakId = $activeBreak ? $activeBreak->id : null;

            $this->totalHours = $this->calculateTotalHours($attendance);
            $this->breakTime = $this->calculateBreakTime($attendance);
            $this->overtimeToday = $this->calculateOvertime($attendance);
        } else {
            $this->isClockedIn = false;
            $this->isOnBreak = false;
            $this->currentBreakId = null;
            $this->totalHours = '0.00';
            $this->breakTime = '0 mins';
            $this->overtimeToday = '0 hrs';
            $this->punchInTime = null;
        }
    }


    protected function calculateTotalHours($attendance)
    {
        if (!$attendance) return '0.00';

        $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = $attendance->check_out
            ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
            : Carbon::now('Asia/Kathmandu');

        // Ensure check-out is after check-in
        if ($checkOut->lt($checkIn)) {
            return '0.00';
        }

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        $breakMinutes = $this->calculateBreakTime($attendance, true);

        // Prevent negative worked time
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);

        return number_format($workedMinutes / 60, 2);
    }

    protected function calculateBreakTime($attendance, $inMinutes = false)
    {
        if (!$attendance) return $inMinutes ? 0 : '0 mins';

        $breakMinutes = BreakRecord::where('attendance_id', $attendance->id)
            ->get()
            ->sum(function ($break) {
                $start = Carbon::parse($break->start_time)->setTimezone('Asia/Kathmandu');
                $end = $break->end_time
                    ? Carbon::parse($break->end_time)->setTimezone('Asia/Kathmandu')
                    : Carbon::now('Asia/Kathmandu');

                // Ensure end time is after start time
                return $end->gt($start) ? $start->diffInMinutes($end) : 0;
            });

        return $inMinutes ? $breakMinutes : ($breakMinutes . ' mins');
    }

    protected function calculateOvertime($attendance)
    {
        $standardHours = 8;
        $workedHours = (float)$this->totalHours;
        $overtime = max(0, $workedHours - $standardHours);
        return number_format($overtime, 2) . ' hrs';
    }

    public function loadTodayActivities()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendances = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'asc')
            ->get();

        $this->todayActivities = [];

        foreach ($attendances as $attendance) {
            $this->todayActivities[] = [
                'action' => 'Check In',
                'time' => Carbon::parse($attendance->check_in)
                    ->setTimezone('Asia/Kathmandu')
                    ->format('h:i A'),
                'duration' => null
            ];

            $breaks = BreakRecord::where('attendance_id', $attendance->id)->get();
            foreach ($breaks as $break) {
                $this->todayActivities[] = [
                    'action' => 'Break Start',
                    'time' => Carbon::parse($break->start_time)
                        ->setTimezone('Asia/Kathmandu')
                        ->format('h:i A'),
                    'duration' => $break->end_time
                        ? Carbon::parse($break->start_time)
                        ->diffInMinutes(Carbon::parse($break->end_time)) . ' mins'
                        : null
                ];

                if ($break->end_time) {
                    $this->todayActivities[] = [
                        'action' => 'Break End',
                        'time' => Carbon::parse($break->end_time)
                            ->setTimezone('Asia/Kathmandu')
                            ->format('h:i A'),
                        'duration' => null
                    ];
                }
            }

            if ($attendance->check_out) {
                $this->todayActivities[] = [
                    'action' => 'Check Out',
                    'time' => Carbon::parse($attendance->check_out)
                        ->setTimezone('Asia/Kathmandu')
                        ->format('h:i A'),
                    'duration' => null
                ];
            }
        }
    }

    public function loadAttendanceRecords()
    {
        $this->attendanceRecords = Attendance::where('employee_id', Auth::id())
            ->when($this->selectedMonth, function ($query) {
                $query->whereMonth('check_in', $this->selectedMonth);
            })
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('check_in', $this->selectedYear);
            })
            ->orderBy('check_in', 'desc')
            ->get()
            ->map(function ($record) {
                $checkIn = Carbon::parse($record->check_in)->setTimezone('Asia/Kathmandu');
                $checkOut = $record->check_out ? Carbon::parse($record->check_out)->setTimezone('Asia/Kathmandu') : null;

                $workedHours = $this->calculateTotalHours($record);
                $breakTime = $this->calculateBreakTime($record);

                return [
                    'date' => $checkIn->format('Y-m-d'),
                    'status' => $checkOut ? 'Checked-Out' : ($checkIn->isToday() ? 'Working' : 'Checked-Out'),
                    'check_in' => $checkIn->format('h:i A'),
                    'check_out' => $checkOut ? $checkOut->format('h:i A') : null,
                    'production' => $workedHours . ' hrs',
                    'break' => $breakTime,
                    'overtime' => max(0, $workedHours - 8) . ' hrs'
                ];
            })
            ->toArray();
    }

    public function calculateStatistics()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendance = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'desc')
            ->first();

        // Today's hours
        $todayHours = $attendance ? (float)$this->totalHours : 0;
        $this->todayStats['hours'] = number_format($todayHours, 2);
        $this->todayStats['remaining_hours'] = number_format(max(0, 8 - $todayHours), 2);

        // Weekly hours
        $weekStart = Carbon::now('Asia/Kathmandu')->startOfWeek();
        $weekEnd = Carbon::now('Asia/Kathmandu')->endOfWeek();

        $weekHours = Attendance::where('employee_id', Auth::id())
            ->whereBetween('check_in', [$weekStart, $weekEnd])
            ->get()
            ->sum(function ($attendance) {
                return (float)$this->calculateTotalHours($attendance);
            });

        $this->todayStats['week_hours'] = number_format($weekHours, 2);

        // Monthly hours
        $monthStart = Carbon::now('Asia/Kathmandu')->startOfMonth();
        $monthEnd = Carbon::now('Asia/Kathmandu')->endOfMonth();

        $monthHours = Attendance::where('employee_id', Auth::id())
            ->whereBetween('check_in', [$monthStart, $monthEnd])
            ->get()
            ->sum(function ($attendance) {
                return (float)$this->calculateTotalHours($attendance);
            });

        $this->todayStats['month_hours'] = number_format($monthHours, 2);

        // Overtime
        $overtime = Attendance::where('employee_id', Auth::id())
            ->whereNotNull('check_out')
            ->whereMonth('check_in', Carbon::now('Asia/Kathmandu')->month)
            ->get()
            ->sum(function ($attendance) {
                $hours = (float)$this->calculateTotalHours($attendance);
                return max(0, $hours - 8);
            });

        $this->todayStats['overtime'] = number_format($overtime, 2);
    }

    public function punchIn()
    {
        try {
            $now = Carbon::now('Asia/Kathmandu');
            $today = $now->copy()->startOfDay();

            // Check for existing open attendance today
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

            $this->loadCurrentStatus();
            $this->loadTodayActivities();
            $this->loadAttendanceRecords();
            $this->calculateStatistics();

            $this->dispatch('refreshComponent')->self();
            $this->dispatch('attendance-updated', true);
            $this->dispatch('refresh-attendance-global');

            // $this->dispatch('show-notification', 'Successfully Checked-In at ' . $now->format('h:i A'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Error Checking in: ' . $e->getMessage());
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
                $this->dispatch('alert', type: 'error', message: 'No active attendance record found to check out');
                return;
            }

            // End any active break
            if ($this->isOnBreak) {
                BreakRecord::where('attendance_id', $attendance->id)
                    ->whereNull('end_time')
                    ->update(['end_time' => $now]);
            }

            $totalHours = $this->calculateTotalHours($attendance);

            $attendance->update([
                'check_out' => $now,
                'total_hours' => $totalHours
            ]);

            $this->isClockedIn = false;
            $this->isOnBreak = false;
            $this->currentBreakId = null;

            $this->loadCurrentStatus();
            $this->loadTodayActivities();
            $this->loadAttendanceRecords();
            $this->calculateStatistics();

            $this->dispatch('refreshComponent')->self();
            $this->dispatch('attendance-updated', false);
            $this->dispatch('refresh-attendance-global');

            // $this->dispatch('show-notification', 'Successfully Checked-Out at ' . $now->format('h:i A'));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Error checking out: ' . $e->getMessage());
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
                $this->dispatch('alert', type: 'error', message: 'Please check in before starting a break');
                return;
            }

            if ($this->isOnBreak) {
                $this->dispatch('alert', type: 'error', message: 'You are already on a break');
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
                $this->loadTodayActivities();
                $this->dispatch('refreshComponent')->self();
                $this->dispatch('refresh-attendance-global');

                $this->dispatch('alert', type: 'success', message: 'Break started at ' .
                    Carbon::now('Asia/Kathmandu')->format('h:i A'));
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Error starting break: ' . $e->getMessage());
        }
    }

    public function endBreak()
    {
        try {
            if (!$this->isOnBreak) {
                $this->dispatch('alert', type: 'error', message: 'No active break to end');
                return;
            }

            // Dispatch event to main attendance component instead of handling locally
            $this->dispatch('end-break-global');
            $this->dispatch('refresh-attendance-global');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: 'Error ending break: ' . $e->getMessage());
        }
    }

    public function searchAttendance()
    {
        $this->validate([
            'selectedMonth' => 'nullable|digits:2',
            'selectedYear' => 'nullable|digits:4'
        ]);

        $this->loadAttendanceRecords();
    }

    public function render()
    {
        $this->currentTime = Carbon::now('Asia/Kathmandu')->format('h:i A');
        return view('livewire.app.header');
    }
}
