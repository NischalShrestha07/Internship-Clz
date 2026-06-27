<?php

namespace App\Livewire\Attendance;

use App\Helpers\AttendanceHelper;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakRecord;
use Illuminate\Support\Facades\Auth;

class AttendanceTracker extends Component
{
    public string $pageTitle = 'Attendance-Tracker';
    public $isClockedIn = false;
    public $isOnBreak = false;
    public $punchInTime;
    public $totalHours = '0.00';
    public $breakTime = '0 mins';
    public $overtimeToday = '0 hrs';
    public $todayActivities = [];
    public $currentDate;
    public $currentTime;
    public $currentBreakId = null;

    public $standardHours;
    public $startTime;
    public $endTime;


    public $todayStats = [
        'hours' => '0.00',
        'week_hours' => '0.00',
        'month_hours' => '0.00',
        'remaining_hours' => '8.00',
        'overtime' => '0.00'
    ];

    protected $listeners = [
        'refresh-attendance' => 'refreshAttendance',
        'attendance-updated' => 'updateStatus',
        'refresh-attendance-global' => 'refreshAttendance',
        'end-break-global' => 'endBreak',

    ];

    public function updateStatus($isClockedIn)
    {
        $this->isClockedIn = $isClockedIn;
        $this->loadCurrentStatus(); // Reload status to ensure consistency
        $this->dispatch('refreshComponent')->self();
    }

    public function refreshAttendance()
    {
        $this->loadCurrentStatus();
        $this->loadTodayActivities();
        $this->calculateStatistics();
        $this->dispatch('refreshComponent')->self();
    }

    public function mount()
    {
        $this->currentDate = Carbon::now('Asia/Kathmandu');
        $this->currentTime = $this->currentDate->format('h:i A');

        $this->standardHours = AttendanceHelper::getDailyWorkingHours();
        $this->startTime = AttendanceHelper::getStartTime();
        $this->endTime = AttendanceHelper::getEndTime();

        $this->loadCurrentStatus();
        $this->loadTodayActivities();
        $this->calculateStatistics();
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
    public function loadCurrentStatus()
    {
        $today = Carbon::today('Asia/Kathmandu');

        // Get all attendances for today
        $attendances = Attendance::with('breakRecords')
            ->where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        // Check if currently clocked in (has any attendance without checkout)
        $this->isClockedIn = $attendances->contains(function ($attendance) {
            return is_null($attendance->check_out);
        });

        // Get earliest check-in and latest check-out for today
        $earliestCheckIn = $attendances->min('check_in');
        $latestCheckOut = $attendances->max('check_out') ?? null;

        $this->punchInTime = $earliestCheckIn ? Carbon::parse($earliestCheckIn) : null;

        // Check if currently on break
        $this->isOnBreak = false;
        $this->currentBreakId = null;

        if ($this->isClockedIn) {
            $activeBreak = BreakRecord::whereIn('attendance_id', $attendances->pluck('id'))
                ->whereNull('end_time')
                ->first();

            $this->isOnBreak = !is_null($activeBreak);
            $this->currentBreakId = $activeBreak ? $activeBreak->id : null;
        }

        // Calculate totals
        $totalHours = $this->calculateDailyTotalHours();
        $this->totalHours = $this->decimalToHoursMinutes($totalHours);
        $this->breakTime = $this->calculateDailyBreakTime($attendances);
        $this->overtimeToday = $this->calculateDailyOvertime($totalHours);
    }

    protected function calculateTotalHours($attendance)
    {
        if (!$attendance) return 0.00;

        $checkIn = Carbon::parse($attendance->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = $attendance->check_out
            ? Carbon::parse($attendance->check_out)->setTimezone('Asia/Kathmandu')
            : Carbon::now('Asia/Kathmandu');

        if ($checkOut->lt($checkIn)) {
            return 0.00;
        }

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        $breakMinutes = $this->calculateBreakTime($attendance, true);

        $workedMinutes = max(0, $totalMinutes - $breakMinutes);
        return round($workedMinutes / 60, 2); // Return decimal hours
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

            if ($checkOut->lt($checkIn)) {
                continue; // Skip invalid time ranges
            }

            $totalMinutes += $checkIn->diffInMinutes($checkOut);

            // Subtract break time
            $breakMinutes = $this->calculateBreakTime($attendance, true);
            $totalMinutes -= $breakMinutes;
        }

        return max(0, $totalMinutes) / 60; // Return hours
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

        if ($inMinutes) {
            return $breakMinutes;
        }

        return $this->decimalToHoursMinutes($breakMinutes / 60);
    }

    protected function calculateDailyBreakTime($attendances)
    {
        $totalBreakMinutes = 0;

        foreach ($attendances as $attendance) {
            $totalBreakMinutes += $attendance->breakRecords->sum(function ($break) {
                $start = Carbon::parse($break->start_time);
                $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                return $start->diffInMinutes($end);
            });
        }

        return $this->decimalToHoursMinutes($totalBreakMinutes / 60);
    }

    protected function calculateDailyOvertime($totalHours)
    {
        $standardHours = 8;
        $overtime = max(0, $totalHours - $standardHours);
        return $this->decimalToHoursMinutes($overtime);
    }


    protected function calculateOvertime($totalHours)
    {
        $threshold = AttendanceHelper::getOvertimeThreshold();
        return max(0, $totalHours - $threshold);
    }

    public function isLate($checkInTime)
    {
        $graceMinutes = AttendanceHelper::getLateGraceMinutes();
        $startTime = Carbon::parse(AttendanceHelper::getStartTime());
        $graceTime = $startTime->addMinutes($graceMinutes);

        return Carbon::parse($checkInTime)->gt($graceTime);
    }

    public function loadTodayActivities()
    {
        $today = Carbon::today('Asia/Kathmandu');
        $attendances = Attendance::where('employee_id', Auth::id())
            ->whereDate('check_in', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        $activities = [];

        foreach ($attendances as $attendance) {
            // Add Check in
            $activities[] = [
                'action' => 'Check In',
                'time' => $attendance->check_in,
                'duration' => null,
                'timestamp' => Carbon::parse($attendance->check_in)->timestamp
            ];

            // Process breaks
            $breaks = BreakRecord::where('attendance_id', $attendance->id)
                ->orderBy('start_time', 'desc')
                ->get();

            foreach ($breaks as $break) {
                if ($break->end_time) {
                    $activities[] = [
                        'action' => 'Break End',
                        'time' => $break->end_time,
                        'duration' => null,
                        'timestamp' => Carbon::parse($break->end_time)->timestamp
                    ];
                }

                // Then add Break Start
                $activities[] = [
                    'action' => 'Break Start',
                    'time' => $break->start_time,
                    'duration' => $break->end_time
                        ? Carbon::parse($break->start_time)->diffInMinutes(Carbon::parse($break->end_time))
                        : 'Ongoing',
                    'timestamp' => Carbon::parse($break->start_time)->timestamp
                ];
            }

            // Add check out if exists
            if ($attendance->check_out) {
                $activities[] = [
                    'action' => 'Check Out',
                    'time' => $attendance->check_out,
                    'duration' => null,
                    'timestamp' => Carbon::parse($attendance->check_out)->timestamp
                ];
            }
        }

        // Sort all activities by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
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
                $this->dispatch('show-notification', 'You already have an active session today.');
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
                $this->loadTodayActivities();
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

            // Get the current attendance
            $attendance = Attendance::where('employee_id', Auth::id())
                ->whereDate('check_in', $today)
                ->whereNull('check_out')
                ->first();

            if (!$attendance) {
                $this->dispatch('show-notification', 'No active attendance record found');
                return;
            }

            // Find the specific break record to end
            $break = BreakRecord::where('id', $this->currentBreakId)
                ->where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->first();

            if ($break) {
                $now = Carbon::now('Asia/Kathmandu');

                // Update only the end_time
                $break->update([
                    'end_time' => $now
                ]);

                $this->isOnBreak = false;
                $this->currentBreakId = null;

                // Refresh all components
                $this->refreshAll();
                $this->dispatch('refresh-attendance-global');

                $this->dispatch('show-notification', 'Break ended at ' .   $now->format('h:i A'));
            } else {
                $this->dispatch('show-notification', 'No active break found to end');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', 'Error ending break: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->currentTime = Carbon::now('Asia/Kathmandu')->format('h:i A');

        return view('livewire.attendance.attendance-tracker', [
            'currentNepalTime' => $this->currentTime
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);;
    }
}
