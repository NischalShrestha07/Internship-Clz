<?php

namespace App\Livewire\Attendance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceHistory extends Component
{
    use WithPagination;

    public $selectedMonth = null;
    public $selectedYear = null;
    public $months = [];
    public $years = [];
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


    public $id = null;
    public $check_in = '';
    public $attendanceId = '';
    public $attendanceName = '';
    public $check_out = '';
    public $break = '';
    public $overtime = '';


    protected $listeners = [
        'refresh-attendance-global' => '$refresh'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedMonth' => ['except' => ''],
        'selectedYear' => ['except' => ''],
        'selectedStatus' => ['except' => '']
    ];

    public function downloadCSV(): StreamedResponse
    {
        $fileName = 'attendance_records_' . now()->format('Y_m_d_H_i_s') . '.csv';

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

            // Get filtered records like in render()
            $records = Attendance::with('breakRecords')
                ->join('users', 'attendances.employee_id', '=', 'users.id')
                ->where('attendances.employee_id', Auth::id())
                ->when($this->search, fn($query) =>
                $query->where('users.name', 'like', '%' . $this->search . '%'))
                ->when($this->selectedMonth, fn($query) =>
                $query->whereMonth('attendances.check_in', $this->selectedMonth))
                ->when($this->selectedYear, fn($query) =>
                $query->whereYear('attendances.check_in', $this->selectedYear))
                ->when($this->selectedStatus, function ($query) {
                    if ($this->selectedStatus === 'Checked-Out') {
                        $query->whereNotNull('attendances.check_out');
                    } elseif ($this->selectedStatus === 'Working') {
                        $query->whereNull('attendances.check_out')
                            ->whereDate('attendances.check_in', Carbon::today());
                    }
                })
                ->select('attendances.*', 'users.name as employee_name')
                ->orderBy('attendances.created_at', 'desc')
                ->get();

            foreach ($records as $record) {
                $checkIn = Carbon::parse($record->check_in)->setTimezone('Asia/Kathmandu');
                $checkOut = $record->check_out ? Carbon::parse($record->check_out)->setTimezone('Asia/Kathmandu') : null;
                $status = $this->determineStatus($checkIn, $checkOut);

                $totalHours = $this->calculateTotalHours($record);
                $breakMinutes = $this->calculateBreakTime($record);
                $overtime = $this->calculateOvertime($record);

                fputcsv($file, [
                    $record->employee_name,
                    $checkIn->format('M j, Y'),
                    $checkIn->format('D'),
                    $status,
                    $checkIn->format('h:i A'),
                    $checkOut ? $checkOut->format('h:i A') : '--',
                    $totalHours,
                    $breakMinutes,
                    $overtime,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function mount()
    {
        $this->currentTime = Carbon::now('Asia/Kathmandu')->format('h:i A');
        $this->months = collect(range(1, 12))->map(function ($month) {
            return Carbon::create()->month($month)->format('F');
        })->toArray();
        $this->years = range(Carbon::now()->year, Carbon::now()->year - 5);
        $this->initializeDateFilters();
    }


    protected function initializeDateFilters()
    {
        $this->months = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => Carbon::create()->month($month)->format('F')];
        });

        $currentYear = Carbon::now()->year;
        $this->years = collect(range($currentYear, $currentYear - 5))->mapWithKeys(function ($year) {
            return [$year => $year];
        });
    }

    public function applyFilters()
    {
        Log::debug('Applying filters', [
            'search' => $this->search,
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
            'selectedStatus' => $this->selectedStatus
        ]);
        $this->resetPage();
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
            'selectedMonth',
            'selectedYear',
            'selectedStatus'
        ]);
        $this->applyFilters();
    }

    protected function determineStatus($checkIn, $checkOut)
    {
        if ($checkOut) return 'Checked-Out';
        return $checkIn->isToday() ? 'Working' : 'Checked-Out';
    }

    protected function calculateTotalHours($record)
    {
        if (!$record->check_out) return 0;

        $checkIn = Carbon::parse($record->check_in)->setTimezone('Asia/Kathmandu');
        $checkOut = Carbon::parse($record->check_out)->setTimezone('Asia/Kathmandu');

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        $breakMinutes = $this->calculateBreakTime($record);
        $workedMinutes = max(0, $totalMinutes - $breakMinutes);

        return round($workedMinutes / 60, 2);
    }

    protected function calculateBreakTime($record)
    {
        return $record->breakRecords->sum(function ($break) {
            if ($break->end_time) {
                $start = Carbon::parse($break->start_time)->setTimezone('Asia/Kathmandu');
                $end = Carbon::parse($break->end_time)->setTimezone('Asia/Kathmandu');
                return $start->diffInMinutes($end);
            }
            return 0;
        });
    }

    protected function calculateOvertime($record)
    {
        $workedHours = $this->calculateTotalHours($record);
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
            'break' => 'required',
            'overtime' => 'required',
        ]);

        $attendance = Attendance::findOrFail($this->attendanceId);
        $attendance->update([
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'break' => $this->break,
            'overtime' => $this->overtime,
        ]);
        $this->isSubmitted = true;


        $this->resetForm();
        $this->dispatch('close-modal', 'view-attendance');
        $this->redirectRoute('attendance.history', navigate: true);
        $this->dispatch('show-notification', 'Attendance updated successfully!');
    }
    public function render()
    {
        Log::debug('Rendering AttendanceHistory', [
            'search' => $this->search,
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
            'selectedStatus' => $this->selectedStatus
        ]);

        $query = Attendance::with('breakRecords')
            ->join('users', 'attendances.employee_id', '=', 'users.id')
            ->where('attendances.employee_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('employee_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedMonth, function ($query) {
                $query->whereMonth('attendances.check_in', $this->selectedMonth);
            })
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('attendances.check_in', $this->selectedYear);
            })
            ->when($this->selectedStatus, function ($query) {
                if ($this->selectedStatus === 'Checked-Out') {
                    $query->whereNotNull('attendances.check_out');
                } elseif ($this->selectedStatus === 'Working') {
                    $query->whereNull('attendances.check_out')
                        ->whereDate('attendances.check_in', Carbon::today());
                }
            })
            ->orderBy('attendances.created_at', 'desc')
            ->select('attendances.*');

        try {
            $attendanceRecords = $query->paginate(8)->through(function ($record) {
                $checkIn = Carbon::parse($record->check_in)->setTimezone('Asia/Kathmandu');
                $checkOut = $record->check_out ? Carbon::parse($record->check_out)->setTimezone('Asia/Kathmandu') : null;

                return [
                    'id' => $record->id,
                    'employee_name' => $record->employee_name,
                    'date' => $checkIn->format('Y-m-d'),
                    'created_at' => $record->created_at,
                    'status' => $this->determineStatus($checkIn, $checkOut),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'production' => $this->calculateTotalHours($record),
                    'break' => $this->calculateBreakTime($record),
                    'overtime' => $this->calculateOvertime($record),
                ];
            });
        } catch (\Exception $e) {
            Log::error('AttendanceHistory query error: ' . $e->getMessage());
            $attendanceRecords = collect([])->paginate(8);
        }

        return view('livewire.attendance.attendance-history', [
            'isHrManager' => Auth::user()->hasRole('HR Manager') ?? false,
            'recentAttendance' => $this->recentAttendance,
            'announcements' => $this->announcements,
            'totalEmployees' => $this->totalEmployees,
            'newEmployeesThisMonth' => $this->newEmployeesThisMonth,
            'attendanceChartData' => $this->attendanceChartData,
            'leaveChartData' => $this->leaveChartData,
            'teamPerformanceChartData' => $this->teamPerformanceChartData,
            'attendanceRecords' => $attendanceRecords,
            'months' => $this->months,
            'years' => $this->years,
            'showFilterAttendance' => $this->showFilterAttendance,
            'selectedStatus' => $this->selectedStatus,
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
            'search' => $this->search ?? '',
            'statuses' => ['Checked-Out', 'Working'],


        ]);
    }
}
