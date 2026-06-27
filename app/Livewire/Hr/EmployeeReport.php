<?php

namespace App\Livewire\Hr;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use Livewire\Component;
use App\Models\LeaveType;
use Livewire\WithPagination;
use App\Models\EmployeeLeaveConfigure;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;

class EmployeeReport extends Component
{
   use WithPagination; 
   public $employeeId;
   public $employee;
   public $leaves_card;
   public  $leaveTypesList;
   public $selectedLeaveType = '';
   public $selectedStatus = '';

    public function mount($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->leaveTypesList = EmployeeLeaveConfigure::where('employee_id', $employeeId)->get();
        $this->employee = User::where('id',$employeeId)->first();
    }
    public function exportCsv(): StreamedResponse
    {
    $fileName = 'leaves_details_' . now()->format('Y_m_d_H_i_s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $query = Leave::where('employee_id', $this->employeeId)
        ->with('leaveType')
        ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
        ->orderBy('created_at', 'desc');

    if ($this->selectedLeaveType) {
        $query->where('leave_type_id', $this->selectedLeaveType);
    }

    if ($this->selectedStatus) {
        $query->where('status', $this->selectedStatus);
    }

    $leaves = $query->get();

    return Response::stream(function () use ($leaves) {
        $handle = fopen('php://output', 'w');

        // CSV Header
        fputcsv($handle, [
            'Employee Name',
            'Leave Type',
            'Start Date',
            'Start Time',
            'End Date',
            'End Time',
            'Leave Reason',
            'Status',
        ]);

        // CSV Data
        foreach ($leaves as $leave) {
            fputcsv($handle, [
                $leave->user->name ?? 'N/A',
                $leave->leaveType->leave_type ?? 'N/A',
                \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y'),
                \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A'),
                \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y'),
                \Carbon\Carbon::parse($leave->end_date_time)->format('g:i A'),
                $leave->leave_reason,
                ucfirst($leave->status),
            ]);
        }

        fclose($handle);
    }, 200, $headers);
    }
    public function calculateLeaveDays(Carbon $start, Carbon $end)
    {
    $startCutoff = Carbon::parse($start->format('Y-m-d') . ' 14:00:00');
    $endCutoff = Carbon::parse($end->format('Y-m-d') . ' 14:00:00');

    if ($start->isSameDay($end)) {
        if ($start->lt($startCutoff) && $end->gte($endCutoff)) {
            return 1.0; 
        } else {
            return 0.5; 
        }
    }

    $days = 0;
    $days += $start->lt($startCutoff) ? 1.0 : 0.5;
    $days += $start->copy()->addDay()->startOfDay()->diffInDays($end->copy()->startOfDay());
    $days += $end->lt($endCutoff) ? 0.0 : 0.5;

    return $days;
    }
    public function applyFilters()
    {
        $this->resetPage();
    }
    public function resetFilters()
    {
        $this->reset([
            'selectedLeaveType',
            'selectedStatus',
        ]);
        $this->resetPage();
    }
    public function back()
    {
       $this->redirectRoute('leave_manage', navigate: true);
    }
    public function render()
    {
       $this->leaves_card = EmployeeLeaveConfigure::with('leaveType')
        ->where('employee_id', $this->employeeId)
        ->get()
        ->map(function ($leave_card) {
            $approvedLeaves = Leave::where('employee_id', $leave_card->employee_id)
                ->where('leave_type_id', $leave_card->leave_type_id)
                ->where('status', 'approved')
                ->get();

            $takenDays = 0;
            foreach ($approvedLeaves as $record) {
                $start = Carbon::parse($record->start_date_time);
                $end = Carbon::parse($record->end_date_time);

                $takenDays += $this->calculateLeaveDays($start, $end);
            }

            $leave_card->taken = $takenDays;
            $leave_card->remaining = round($leave_card->days - $takenDays, 3);

            return $leave_card;
        });
     $leaveTypes = EmployeeLeaveConfigure::with('leaveType')
        ->where('employee_id', $this->employeeId)
        ->get()
        ->map(function ($config) {
            return [
                'id' => $config->leave_type_id,
                'name' => $config->leaveType->leave_type ?? 'Unknown',
            ];
        })
        ->toArray();
    $query = Leave::where('employee_id', $this->employeeId)
        ->with('leaveType')
        ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
        ->orderBy('created_at', 'desc');

    if ($this->selectedLeaveType) {
        $query->where('leave_type_id', $this->selectedLeaveType);
    }

    if ($this->selectedStatus) {
        $query->where('status', $this->selectedStatus);
    }

    // Return view with data
    return view('livewire.hr.employee-report', [
        'leaves' => $query->paginate(5),
        'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
        'leaveTypes' => $leaveTypes,
        'leaves_card' => $this->leaves_card,
    ]);
    }

}