<?php

namespace App\Livewire\Leave;

use App\Helpers\AttendanceHelper;
use App\Models\Leave;
use Livewire\Component;
use App\Models\LeaveType;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageLeave extends Component
{
    use WithPagination;
    public string $pageTitle = 'Manage Leave ';
    public $search = '';
    public $selectedLeaveType = '';
    public $selectedStatus = '';
    public $leave;
    public $isSubmitted = false;
    public $leaveId, $leave_reason, $start_date_time, $end_date_time, $leave_type, $status, $remarks, $leaveTypes;
    public $approvedCount, $cancelledCount, $declinedCount, $pendingCount;

    public $perPage = null;
    public $userPerPage = null;
    public $quickRange = '';
    public $startDateFilter = '';
    public $endDateFilter = '';

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'leave_requests_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Use the same query logic as in render()
        $leaves = Leave::with(['user', 'leaveType'])
            ->when(Auth::user()->hasRole('HR Manager'), function ($query) {
                $query->where('employee_id', '!=', auth()->id());
            }, function ($query) {
                $query->where('employee_id', auth()->id());
            })
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedLeaveType, function ($query) {
                $query->where('leave_type_id', $this->selectedLeaveType);
            })
            ->when($this->selectedStatus, function ($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->when($this->startDateFilter && $this->endDateFilter, function ($query) {
                $query->whereDate('start_date_time', '>=', $this->startDateFilter)
                    ->whereDate('end_date_time', '<=', $this->endDateFilter);
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->stream(function () use ($leaves) {
            $handle = fopen('php://output', 'w');

            // Header row (matches table columns)
            fputcsv($handle, [
                'Employee Name',
                'Leave Type',
                'Start Date',
                'Start Time',
                'End Date',
                'End Time',
                'Status',
            ]);

            // Data rows
            foreach ($leaves as $leave) {
                fputcsv($handle, [
                    $leave->user->name,
                    $leave->leaveType->leave_type,
                    \Carbon\Carbon::parse($leave->start_date_time)->format('M d, Y'),
                    \Carbon\Carbon::parse($leave->start_date_time)->format('g:i A'),
                    \Carbon\Carbon::parse($leave->end_date_time)->format('M d, Y'),
                    \Carbon\Carbon::parse($leave->end_date_time)->format('g:i A'),
                    ucfirst($leave->status),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function mount()
    {
        $query = Leave::query();
        if (Auth::user()->hasRole('HR Manager')) {
            $query->where('employee_id', '!=', auth()->id());
        }
        $this->approvedCount = (clone $query)->where('status', 'approved')->count();
        $this->cancelledCount = (clone $query)->where('status', 'cancelled')->count();
        $this->declinedCount = (clone $query)->where('status', 'declined')->count();
        $this->pendingCount = (clone $query)->where('status', 'pending')->count();

        $this->perPage = AttendanceHelper::getRecordsPerPage();
        $this->userPerPage = $this->userPerPage ?? $this->perPage;
    }

    public function resetToDefaultPerPage()
    {
        $this->userPerPage = $this->perPage;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'selectedLeaveType',
            'selectedStatus',
            'quickRange',
            'startDateFilter',
            'endDateFilter',
        ]);
        $this->resetPage();
    }

    public function applyQuickRange()
    {
        $now = \Carbon\Carbon::now();

        switch ($this->quickRange) {
            case 'today':
                $this->startDateFilter = $now->format('Y-m-d');
                $this->endDateFilter = $now->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDateFilter = $now->startOfWeek()->format('Y-m-d');
                $this->endDateFilter = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'last_week':
                $this->startDateFilter = $now->subWeek()->startOfWeek()->format('Y-m-d');
                $this->endDateFilter = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDateFilter = $now->startOfMonth()->format('Y-m-d');
                $this->endDateFilter = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDateFilter = $now->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDateFilter = $now->endOfMonth()->format('Y-m-d');
                break;
            default:
                $this->startDateFilter = '';
                $this->endDateFilter = '';
                break;
        }

        $this->applyFilters();
    }

    public function viewEmployee($employeeId)
    {
        return redirect()->route('employee.show', $employeeId);
    }
    public function view($id)
    {
        $leave = Leave::with(['user', 'leaveType'])->findOrFail($id);

        $this->leaveId = $leave->id;
        $this->leave_type = $leave->leaveType->leave_type;
        $this->start_date_time = \Carbon\Carbon::parse($leave->start_date_time)->format('j/n/Y g:i A');
        $this->end_date_time = \Carbon\Carbon::parse($leave->end_date_time)->format('j/n/Y g:i A');
        $this->leave_reason = $leave->leave_reason;
        $this->status = $leave->status;
        $this->remarks = $leave->remarks;
        $this->isSubmitted = false;
        $this->dispatch('open-modal', 'view-leave');
    }

    public function submit()
    {
        $this->validate([
            'status' => 'required|in:approved,declined',
            'remarks' => 'required|string|max:1000',
        ]);

        $leave = Leave::findOrFail($this->leaveId);
        $leave->update([
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);
        $this->isSubmitted = true;


        $this->resetForm();
        $this->dispatch('close-modal', 'view-leave');
        $this->redirectRoute('leave_manage', navigate: true);
        $this->dispatch('show-notification', 'Status updated successfully!');
    }

    public function resetForm()
    {
        $this->reset([
            'leaveId',
            'status',
            'remarks',
            'leave_type',
            'start_date_time',
            'end_date_time',
            'leave_reason'
        ]);
    }


    public function render()
    {

        $this->leaveTypes = LeaveType::all();


        $query = Leave::with(['user', 'leaveType']);


        if (Auth::user()->hasRole('HR Manager')) {
            $query->where('employee_id', '!=', auth()->id());
        } else {
            $query->where('employee_id', auth()->id());
        }


        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }


        if ($this->selectedLeaveType) {
            $query->where('leave_type_id', $this->selectedLeaveType);
        }


        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        if ($this->startDateFilter && $this->endDateFilter) {
            $query->whereDate('start_date_time', '>=', $this->startDateFilter)
                ->whereDate('end_date_time', '<=', $this->endDateFilter);
        }


        $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');


        return view('livewire.leave.manage-leave', [
            'leaves' => $query->paginate($this->userPerPage),
            'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
            'leaveTypes' => $this->leaveTypes,
            'defaultPerPage' => $this->perPage,
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
