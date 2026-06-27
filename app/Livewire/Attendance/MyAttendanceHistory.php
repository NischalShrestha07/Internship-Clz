<?php

namespace App\Livewire\Attendance;

use App\Helpers\AttendanceHelper;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyAttendanceHistory extends Component
{

    use WithPagination;

    public $dateFrom = null;
    public $dateTo = null;
    public $showFilterAttendance = true;
    public $selectedStatus = '';

    public $attendanceChartData = [];
    public $leaveChartData = [];
    public $teamPerformanceChartData = [];
    public $recentAttendance = [];
    public $announcements = [];
    public $totalEmployees = 0;
    public $newEmployeesThisMonth = 0;
    public $performanceScore;
    public $currentTime;
    public $isSubmitted = false;
    public $viewMode = 'edit';
    public $attendanceSessions = [];

    public $id = null;
    public $check_in = '';
    public $attendanceId = '';
    public $attendanceName = '';
    public $date = '';
    public $check_out = '';
    public $break = '';
    public $overtime = '';
    public string $pageTitle = 'Attendance History ';
    public $quickRange = '';

    public $perPage = null;
    public $userPerPage = null;
    public $selectedOvertime = null;

    protected $listeners = [
        'refresh-attendance-global' => '$refresh',
        'updateDateFrom' => 'updateDateFrom',
        'updateDateTo' => 'updateDateTo',
        'update-check_in' => 'updateCheckIn',
        'update-check_out' => 'updateCheckOut',
        'update-calendar' => 'updateCalendar',
        'resetCalendar' => 'resetCalendar',
    ];

    public function resetCalendar()
    {
        $this->dispatch('resetCalendar');
    }

    public function updateDateFrom()
    {
        $this->dispatch('updateCalendar', [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
    }

    public function updateDateTo()
    {
        $this->dispatch('updateCalendar', [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ]);
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'quickRange' => ['except' => ''],
        'userPerPage' => ['except' => null],
        'selectedOvertime' => ['except' => null],

    ];


    public function downloadCSV(): StreamedResponse
    {
        $fileName = 'my_attendance_records_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Employee',
                'Date',
                'Day',
                'Status',
                'Check In',
                'Check Out',
                'Work Hours',
                'Break (min)',
                'Overtime (hrs)',
            ]);

            // Convert input dates to proper datetime objects with timezone
            $dateFrom = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay()->timezone('Asia/Kathmandu') : null;
            $dateTo = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay()->timezone('Asia/Kathmandu') : null;

            // Get all attendance records grouped by date (same logic as in render())
            $dailyRecords = Attendance::with('breakRecords')
                ->join('users', 'attendances.employee_id', '=', 'users.id')
                ->where('attendances.employee_id', Auth::id())
                ->when($this->selectedStatus, function ($query) {
                    if ($this->selectedStatus === 'Checked-Out') {
                        $query->whereNotNull('attendances.check_out');
                    } elseif ($this->selectedStatus === 'Working') {
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
                ->when($this->selectedOvertime === 'with_overtime', function ($query) {
                    $query->where(function ($q) {
                        $q->where('attendances.overtime', '>', 0)
                            ->orWhere(function ($q2) {
                                $q2->whereNull('attendances.overtime')
                                    ->whereRaw('TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - IFNULL(attendances.break, 0) > 480');
                            });
                    });
                })
                ->when($this->selectedOvertime === 'without_overtime', function ($query) {
                    $query->where(function ($q) {
                        $q->where('attendances.overtime', '<=', 0)
                            ->orWhere(function ($q2) {
                                $q2->whereNull('attendances.overtime')
                                    ->whereRaw('TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - IFNULL(attendances.break, 0) <= 480');
                            });
                    });
                })

                ->orderBy('attendances.check_in', 'desc')
                ->select('attendances.*', 'users.name as employee_name')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->check_in)->format('Y-m-d');
                });

            // Process each day's records (same logic as in render())
            foreach ($dailyRecords as $date => $records) {
                $stats = $this->calculateDailyStats($records);
                $status = $this->determineStatus($records);

                $checkIn = $stats['earliest_check_in'];
                $checkOut = $stats['latest_check_out'];
                $totalHours = $this->formatDecimalToTime($stats['total_hours']);
                $breakMinutes = $stats['break_minutes'];
                $overtime = $stats['overtime'];

                fputcsv($file, [
                    $records->first()->employee_name,
                    Carbon::parse($date)->format('M j, Y'),
                    Carbon::parse($date)->format('D'),
                    $status,
                    $checkIn ? $checkIn->format('h:i A') : '--',
                    $checkOut ? $checkOut->format('h:i A') : '--',
                    $totalHours,
                    $breakMinutes,
                    number_format($overtime, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function mount()
    {
        $this->currentTime = Carbon::now('Asia/Kathmandu')->format('h:i A');
        $this->dateFrom = $this->dateFrom ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = $this->dateTo ?? Carbon::now()->format('Y-m-d');

        $this->perPage = AttendanceHelper::getRecordsPerPage();
        $this->userPerPage = $this->userPerPage ?? $this->perPage;
    }
    protected function filterByDateRange($query)
    {
        if ($this->dateFrom && $this->dateTo) {
            $from = Carbon::parse($this->dateFrom)->startOfDay()->timezone('Asia/Kathmandu');
            $to = Carbon::parse($this->dateTo)->endOfDay()->timezone('Asia/Kathmandu');

            $query->whereBetween('attendances.check_in', [$from, $to]);
        } elseif ($this->dateFrom) {
            $from = Carbon::parse($this->dateFrom)->startOfDay()->timezone('Asia/Kathmandu');
            $query->where('attendances.check_in', '>=', $from);
        } elseif ($this->dateTo) {
            $to = Carbon::parse($this->dateTo)->endOfDay()->timezone('Asia/Kathmandu');
            $query->where('attendances.check_in', '<=', $to);
        }

        return $query;
    }

    public function applyFilters()
    {
        Log::info('applyFilters called', ['dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo]);

        // Validate dates
        if ($this->dateFrom && $this->dateTo) {
            $from = Carbon::parse($this->dateFrom);
            $to = Carbon::parse($this->dateTo);

            if ($from->gt($to)) {
                $this->addError('dateFrom', 'Start date cannot be after end date');
                $this->addError('dateTo', 'End date cannot be before start date');
                Log::warning('Invalid date range', ['dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo]);
                return;
            }
        }

        $this->resetPage();
        $this->dispatch('refresh-attendance-global');
    }

    public function resetForm()
    {
        $this->reset([
            'attendanceId',
            'check_in',
            'check_out',
            'break',
            'overtime',
        ]);
    }

    public function resetFilters()
    {
        $this->reset([
            'dateFrom',
            'dateTo',
            'selectedStatus',
            'quickRange',
            'selectedOvertime'
        ]);
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->applyFilters();
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
    protected function calculateDailyStats($records)
    {
        $totalMinutes = 0;
        $breakMinutes = 0;
        $earliestCheckIn = null;
        $latestCheckOut = null;
        $storedOvertime = 0;

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

            // Calculate break time - use either breakRecords or direct break field, not both
            if ($record->breakRecords && $record->breakRecords->isNotEmpty()) {
                $breakMinutes += $record->breakRecords->sum(function ($break) {
                    $start = Carbon::parse($break->start_time);
                    $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
                    return $start->diffInMinutes($end);
                });
            } else {
                $breakMinutes += $record->break ?? AttendanceHelper::getDefaultBreakMinutes();
            }

            // Use stored overtime if available
            if ($record->overtime) {
                $storedOvertime = $record->overtime;
            }
        }

        // Calculate worked time (subtract breaks)
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);
        $totalHours = $workedMinutes / 60;

        // Use stored overtime if available, otherwise calculate
        $overtime = $storedOvertime ?: max(0, $totalHours - 8);

        return [
            'earliest_check_in' => $earliestCheckIn,
            'latest_check_out' => $latestCheckOut,
            'total_hours' => $totalHours,
            'break_minutes' => $breakMinutes,
            'overtime' => $overtime
        ];
    }

    protected function calculateTotalHours($record)
    {
        if (!$record->check_out) return '00:00';

        $checkIn = Carbon::parse($record->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = Carbon::parse($record->check_out)->setTimezone('Asia/Kathmandu');

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        $breakMinutes = $this->calculateBreakTime($record);
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);

        $hours = floor($workedMinutes / 60);
        $minutes = $workedMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    protected function calculateBreakTime($record)
    {
        if (!$record->breakRecords || $record->breakRecords->isEmpty()) {
            return AttendanceHelper::getDefaultBreakMinutes();
        }

        return $record->breakRecords->sum(function ($break) {
            $start = Carbon::parse($break->start_time);
            $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
            return $start->diffInMinutes($end);
        });
    }

    protected function calculateOvertime($record)
    {
        $workedHours = $this->calculateTotalHours($record);
        // Convert HH:MM back to decimal for calculation if needed
        if (is_string($workedHours)) {
            list($hours, $minutes) = explode(':', $workedHours);
            $workedHours = $hours + ($minutes / 60);
        }
        return max(0, $workedHours - 8);
    }

    public function view($id)
    {
        $attendance = Attendance::findOrFail($id);
        $this->attendanceId = $attendance->id;
        $this->attendanceName = $attendance->employee_name;

        $this->check_in = \Carbon\Carbon::parse($attendance->check_in)->format('Y-m-d\TH:i');
        $this->check_out = \Carbon\Carbon::parse($attendance->check_out)->format('Y-m-d\TH:i');

        $this->break = $attendance->break;
        $this->overtime = $attendance->overtime;
        $this->isSubmitted = false;

        $this->dispatch('open-modal', 'view-attendance');
    }


    public function submit()
    {
        $this->validate([
            'check_in' => 'required',
            'check_out' => 'required',
            'break' => 'nullable|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
        ]);

        $attendance = Attendance::findOrFail($this->attendanceId);

        // Calculate total break time (manual entry + automatic breaks)
        $totalBreak = $this->break;
        $autoBreakMinutes = $attendance->breakRecords->sum(function ($break) {
            $start = Carbon::parse($break->start_time);
            $end = $break->end_time ? Carbon::parse($break->end_time) : Carbon::now();
            return $start->diffInMinutes($end);
        });
        $totalBreak += $autoBreakMinutes;

        // Calculate overtime based on actual worked hours
        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);
        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        $workedMinutes = max(0, $totalMinutes - $totalBreak);
        $workedHours = $workedMinutes / 60;
        $calculatedOvertime = max(0, $workedHours - 8);

        // Use manually entered overtime if provided, otherwise use calculated
        $overtime = $this->overtime !== '' ? $this->overtime : $calculatedOvertime;

        $attendance->update([
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'break' => $totalBreak,
            'overtime' => $overtime, // Make sure this is saved
        ]);

        $this->isSubmitted = true;
        $this->resetForm();
        $this->dispatch('close-modal', 'view-attendance');
        $this->redirectRoute('attendance.my-history', navigate: true);
        $this->dispatch('show-notification', 'Attendance updated successfully!');
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

    protected function formatDecimalToTime($decimalHours)
    {
        if (!is_numeric($decimalHours)) return '00:00';

        $hours = floor($decimalHours);
        $minutes = floor(($decimalHours - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function viewSessions($id, $mode = 'edit')
    {
        $attendance = Attendance::findOrFail($id);
        $this->attendanceId = $attendance->id;
        $this->attendanceName = $attendance->employee_name;
        $this->date = $attendance->date;
        $this->viewMode = $mode;

        if ($mode === 'edit') {
            $this->check_in = \Carbon\Carbon::parse($attendance->check_in)->format('Y-m-d\TH:i');
            $this->check_out = \Carbon\Carbon::parse($attendance->check_out)->format('Y-m-d\TH:i');
            $this->break = $attendance->break;
            $this->overtime = $attendance->overtime;
        } else {
            // Load all sessions for this date
            $date = Carbon::parse($attendance->check_in)->format('Y-m-d');
            $this->attendanceSessions = Attendance::with('breakRecords')
                ->where('employee_id', $attendance->employee_id)
                ->whereDate('check_in', $date)
                ->orderBy('check_in')
                ->get()
                ->map(function ($session) {
                    $checkIn = Carbon::parse($session->check_in);
                    $checkOut = $session->check_out ? Carbon::parse($session->check_out) : null;

                    // Calculate work hours
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
                        'check_in' => $checkIn->format('h:i A'),
                        'check_out' => $checkOut ? $checkOut->format('h:i A') : '--',
                        'work_hours' => $workHours,
                        'break' => $this->decimalToHoursMinutes($this->calculateBreakTime($session) / 60),
                        'status' => $session->check_out ? 'Checked Out' : 'Working'
                    ];
                });
        }

        $this->isSubmitted = false;
        $this->dispatch('open-modal', 'view-attendance-sessions');
    }

    public function toggleViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function applyQuickRange()
    {
        $now = Carbon::now('Asia/Kathmandu');

        switch ($this->quickRange) {
            case 'today':
                $this->dateFrom = $now->format('Y-m-d');
                $this->dateTo = $now->format('Y-m-d');
                break;

            case 'this_week':
                $this->dateFrom = $now->startOfWeek()->format('Y-m-d');
                $this->dateTo = $now->endOfWeek()->format('Y-m-d');
                break;

            case 'last_week':
                $this->dateFrom = $now->subWeek()->startOfWeek()->format('Y-m-d');
                $this->dateTo = $now->endOfWeek()->format('Y-m-d');
                break;

            case 'this_month':
                $this->dateFrom = $now->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->endOfMonth()->format('Y-m-d');
                break;

            case 'last_month':
                $this->dateFrom = $now->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->endOfMonth()->format('Y-m-d');
                break;

            default:
                // For custom range, do nothing - let the user select dates manually
                break;
        }

        $this->applyFilters();
    }

    public function resetToDefaultPerPage()
    {
        $this->userPerPage = $this->perPage;
        $this->applyFilters();
    }

    public function render()
    {
        $dateFrom = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay()->timezone('Asia/Kathmandu') : null;
        $dateTo = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay()->timezone('Asia/Kathmandu') : null;

        // Get all attendance records grouped by date
        $dailyRecords = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->where('attendances.employee_id', Auth::id())
            ->when($this->selectedStatus, function ($query) {
                if ($this->selectedStatus === 'Checked-Out') {
                    $query->whereNotNull('attendances.check_out');
                } elseif ($this->selectedStatus === 'Working') {
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
            ->when($this->selectedOvertime === 'with_overtime', function ($query) {
                $query->where(function ($q) {
                    $q->where('attendances.overtime', '>', 0)
                        ->orWhere(function ($q2) {
                            $q2->whereNull('attendances.overtime')
                                ->whereRaw('TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - IFNULL(attendances.break, 0) > 480');
                        });
                });
            })
            ->when($this->selectedOvertime === 'without_overtime', function ($query) {
                $query->where(function ($q) {
                    $q->where('attendances.overtime', '<=', 0)
                        ->orWhere(function ($q2) {
                            $q2->whereNull('attendances.overtime')
                                ->whereRaw('TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - IFNULL(attendances.break, 0) <= 480');
                        });
                });
            })
            ->orderBy('attendances.check_in', 'desc')
            ->select('attendances.*', 'users.name as employee_name')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format('Y-m-d');
            });

        // Process each day's records
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
                'production' => $this->formatDecimalToTime($stats['total_hours']),
                'break' => $stats['break_minutes'],
                'overtime' => $stats['overtime'],
                'record_count' => $records->count()
            ]);
        }

        $perPage = $this->userPerPage ?? $this->perPage;

        // Paginate the processed records
        $attendanceRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedRecords->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), $perPage),
            $processedRecords->count(),
            $perPage,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.attendance.my-attendance-history', [
            'attendanceRecords' => $attendanceRecords,
            'selectedStatus' => $this->selectedStatus,
            'statuses' => ['Checked-Out', 'Working'],
            'defaultPerPage' => $this->perPage,
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
