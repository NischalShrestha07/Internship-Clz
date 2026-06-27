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

class EmployeeAttendanceHistory extends Component
{
    use WithPagination;

    public $dateFrom = null;
    public $dateTo = null;
    public $showFilterAttendance = true;
    public $search = '';
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

    public $quickRange = '';
    public $selectedOvertime = null;

    public $id = null;
    public $check_in = '';
    public $attendanceId = '';
    public $attendanceName = '';
    public $date = '';
    public $check_out = '';
    public $break = '';
    public $overtime = '';
    public string $pageTitle = 'Employee Attendance History ';

    public $perPage = null;
    public $userPerPage = null;


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

    public function downloadCSV(): StreamedResponse
    {
        $fileName = 'employee_attendance_' . now()->format('Y_m_d_H_i_s') . '.csv';

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
                'Sessions',
                'First Check-In',
                'Last Check-Out',
                'Total Hours',
                'Break (min)',
                'Overtime (hrs)',
                'Details'
            ]);

            $dateFrom = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay()->timezone('Asia/Kathmandu') : null;
            $dateTo = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay()->timezone('Asia/Kathmandu') : null;

            // Replace the existing query with this in both methods
            $groupedRecords = Attendance::with('breakRecords')
                ->join('users', 'attendances.employee_id', '=', 'users.id')
                ->where('attendances.employee_id', '!=', Auth::id())
                ->when($this->search, function ($query) {
                    $query->where('users.name', 'like', '%' . $this->search . '%');
                })
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
                ->orderBy('users.name')
                ->orderBy('attendances.check_in', 'desc')
                ->select('attendances.*', 'users.name as employee_name')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->check_in)->format('Y-m-d') . '|' . $item->employee_id;
                });

            // Then filter the grouped records based on overtime
            if ($this->selectedOvertime === 'with_overtime') {
                $groupedRecords = $groupedRecords->filter(function ($records) {
                    $stats = $this->calculateDailyStats($records);
                    return $stats['overtime'] > 0;
                });
            } elseif ($this->selectedOvertime === 'without_overtime') {
                $groupedRecords = $groupedRecords->filter(function ($records) {
                    $stats = $this->calculateDailyStats($records);
                    return $stats['overtime'] <= 0;
                });
            }

            foreach ($groupedRecords as $groupKey => $records) {
                [$date, $employeeId] = explode('|', $groupKey);
                $stats = $this->calculateDailyStats($records);
                $status = $this->determineStatus($records);

                // Build session details
                $sessionDetails = [];
                foreach ($records as $record) {
                    $checkIn = Carbon::parse($record->check_in)->format('h:i A');
                    $checkOut = $record->check_out ? Carbon::parse($record->check_out)->format('h:i A') : '--';
                    $sessionDetails[] = "{$checkIn} to {$checkOut}";
                }

                fputcsv($file, [
                    $records->first()->employee_name,
                    Carbon::parse($date)->format('M j, Y'),
                    Carbon::parse($date)->format('D'),
                    $status,
                    count($records),
                    $stats['earliest_check_in']->format('h:i A'),
                    $stats['latest_check_out'] ? $stats['latest_check_out']->format('h:i A') : '--',
                    $this->formatDecimalToTime($stats['total_hours']),
                    $stats['break_minutes'],
                    number_format($stats['overtime'], 2),
                    implode("\n", $sessionDetails)
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
            'search',
            'dateFrom',
            'dateTo',
            'selectedStatus',
            'quickRange',
            'selectedOvertime'
        ]);
        // Reset to default date range
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

            // Use either breakRecords or direct break field, not both
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

        if (is_string($workedHours) && strpos($workedHours, ':') !== false) {
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

    public function submit()
    {
        if (auth()->user()->hasRole('HR Manager')) {
            $this->validate([
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'break' => 'required|numeric|min:0',
                'overtime' => 'required|numeric|min:0',
            ]);
        } else {
            $this->validate([
                'break' => 'required|numeric|min:0',
                'overtime' => 'required|numeric|min:0',
            ]);
        }

        $attendance = Attendance::findOrFail($this->attendanceId);

        $updateData = [
            'break' => $this->break,
            'overtime' => $this->overtime,
        ];

        if (auth()->user()->hasRole('HR Manager')) {
            $updateData['check_in'] = $this->check_in;
            $updateData['check_out'] = $this->check_out;

            // Recalculate break time if check-in/check-out changed
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $totalMinutes = $checkIn->diffInMinutes($checkOut);

            // Only update overtime if it wasn't manually set
            if ($this->overtime == $attendance->overtime) {
                $workedMinutes = max(0, $totalMinutes - $this->break);
                $updateData['overtime'] = max(0, ($workedMinutes / 60) - 8);
            }
        }

        $attendance->update($updateData);

        $this->isSubmitted = true;
        $this->resetForm();
        $this->dispatch('close-modal', 'view-attendance');
        $this->dispatch('show-notification', 'Attendance updated successfully!');
    }

    protected function formatBreakTime($minutes)
    {
        if (!is_numeric($minutes)) {
            return '0m';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return sprintf("%dh %02dm", $hours, $remainingMinutes);
        } elseif ($hours > 0) {
            return sprintf("%dh", $hours);
        } else {
            return sprintf("%dm", $remainingMinutes);
        }
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

        $groupedRecords = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->where('attendances.employee_id', '!=', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('users.name', 'like', '%' . $this->search . '%');
            })
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
            ->orderBy('users.name')
            ->orderBy('attendances.check_in', 'desc')
            ->select('attendances.*', 'users.name as employee_name')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->check_in)->format('Y-m-d') . '|' . $item->employee_id;
            });

        // Then filter the grouped records based on overtime
        if ($this->selectedOvertime === 'with_overtime') {
            $groupedRecords = $groupedRecords->filter(function ($records) {
                $stats = $this->calculateDailyStats($records);
                return $stats['overtime'] > 0;
            });
        } elseif ($this->selectedOvertime === 'without_overtime') {
            $groupedRecords = $groupedRecords->filter(function ($records) {
                $stats = $this->calculateDailyStats($records);
                return $stats['overtime'] <= 0;
            });
        }

        // Process each day's records for each employee
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
                'production' => $this->formatDecimalToTime($stats['total_hours']),
                'break' => $stats['break_minutes'],
                'overtime' => $stats['overtime'],
                'record_count' => $records->count(),
                'session_details' => $records->map(function ($record) {
                    return [
                        'check_in' => Carbon::parse($record->check_in)->format('h:i A'),
                        'check_out' => $record->check_out ? Carbon::parse($record->check_out)->format('h:i A') : '--'
                    ];
                })
            ]);
        }

        // Sort by employee name and date
        $processedRecords = $processedRecords->sortBy([
            ['employee_name', 'asc'],
            ['date', 'desc']
        ]);

        $perPage = $this->userPerPage ?? $this->perPage;

        // Paginate the processed records
        $attendanceRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedRecords->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), $perPage),
            $processedRecords->count(),
            $perPage,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.attendance.employee-attendance-history', [
            'attendanceRecords' => $attendanceRecords,
            'selectedStatus' => $this->selectedStatus,
            'search' => $this->search ?? '',
            'statuses' => ['Checked-Out', 'Working'],
            'defaultPerPage' => $this->perPage,
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
