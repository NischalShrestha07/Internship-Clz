<?php

namespace App\Livewire\Leave;

use App\Helpers\AttendanceHelper;
use Carbon\Carbon;
use App\Models\Leave;
use Livewire\Component;
use App\Models\LeaveType;
use Livewire\WithPagination;
use App\Models\EmployeeLeaveConfigure;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Leaves extends Component
{
    public $leaveId, $leave_reason, $start_date_time, $end_date_time, $leaveTypesList, $leave_type, $leave_type_card, $status, $remarks;
    public $leaveToCancel = null;
    public $leaves_card;
    public string $pageTitle = 'Leave ';

    use WithPagination;

    public $search = '';
    public $selectedLeaveType = '';
    public $selectedStatus = '';
    public $employeeId;

    public $perPage = null;
    public $userPerPage = null;
    public $quickRange = '';
    public $startDateFilter = '';
    public $endDateFilter = '';


    public function exportCsv(): StreamedResponse
    {
        $fileName = 'leaves_details_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $leaves = Leave::where('employee_id', auth()->id())
            ->with(['user', 'leaveType'])
            ->when($this->selectedLeaveType, fn($query) => $query->where('leave_type_id', $this->selectedLeaveType))
            ->when($this->selectedStatus, fn($query) => $query->where('status', $this->selectedStatus))
            ->when($this->startDateFilter && $this->endDateFilter, function ($query) {
                $query->whereDate('start_date_time', '>=', $this->startDateFilter)
                    ->whereDate('end_date_time', '<=', $this->endDateFilter);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->stream(function () use ($leaves) {
            $handle = fopen('php://output', 'w');


            fputcsv($handle, [
                'Leave Type',
                'Start Date',
                'Start Time',
                'End Date',
                'End Time',
                'Leave Reason',
                'Status',
            ]);

            foreach ($leaves as $leave) {
                fputcsv($handle, [
                    $leave->leaveType->leave_type,
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

    public function applyQuickRange()
    {
        $now = Carbon::now();

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

    public function resetToDefaultPerPage()
    {
        $this->userPerPage = $this->perPage;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }
    public function mount()
    {
        $this->employeeId = auth()->id();
        $this->leaveTypesList = EmployeeLeaveConfigure::where('employee_id', $this->employeeId)->get();

        $this->perPage = AttendanceHelper::getRecordsPerPage();
        $this->userPerPage = $this->userPerPage ?? $this->perPage;
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


    public function submit()
    {
        $this->validate([
            'leave_type' => 'required|exists:leave_types,id',
            'start_date_time' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $start = Carbon::parse($value);
                    $end = Carbon::parse($this->end_date_time);

                    if ($start->gt($end)) {
                        $fail('Start date cannot be after end date.');
                        return;
                    }

                    if ($start->equalTo($end)) {
                        $fail('Start and end date time cannot be exactly the same.');
                        return;
                    }

                    $leaveDurationDays = $this->calculateLeaveDays($start, $end);

                    $overlapExists = Leave::where('employee_id', auth()->id())
                        ->where(function ($query) use ($start, $end) {
                            $query->whereBetween('start_date_time', [$start, $end])
                                ->orWhereBetween('end_date_time', [$start, $end])
                                ->orWhere(function ($query) use ($start, $end) {
                                    $query->where('start_date_time', '<=', $start)
                                        ->where('end_date_time', '>=', $end);
                                });
                        })->exists();

                    if ($overlapExists) {
                        $fail('You already have leave overlapping the selected dates.');
                        return;
                    }

                    $config = EmployeeLeaveConfigure::where('employee_id', auth()->id())
                        ->where('leave_type_id', $this->leave_type)
                        ->first();

                    if (!$config || $config->days === 0) {
                        $fail('You are not allowed to apply for this leave type. Please contact HR.');
                        return;
                    }

                    $usedDays = Leave::where('employee_id', auth()->id())
                        ->where('leave_type_id', $this->leave_type)
                        ->where('status', 'approved')
                        ->get()
                        ->sum(function ($leave) {
                            $start = Carbon::parse($leave->start_date_time);
                            $end = Carbon::parse($leave->end_date_time);
                            return $this->calculateLeaveDays($start, $end);
                        });

                    if (($usedDays + $leaveDurationDays) > $config->days) {
                        $fail('You have exceeded your leave allowance for this leave type.');
                    }
                },
            ],
            'end_date_time' => 'required|date|after_or_equal:start_date_time',
            'leave_reason' => 'required|string|max:1000',
        ]);
        Leave::create([
            'employee_id' => auth()->id(),
            'leave_type_id' => $this->leave_type,
            'start_date_time' => $this->start_date_time,
            'end_date_time' => $this->end_date_time,
            'leave_reason' => $this->leave_reason,
            'status' => 'pending',
        ]);

        $this->dispatch('close-modal', 'create-leave');
        $this->redirectRoute('leave', navigate: true);
        $this->dispatch('show-notification', 'Leave Request applied successfully!');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate([

            'start_date_time' => 'required|date',
            'end_date_time' => 'required|date|after_or_equal:start_date_time',
            'leave_reason' => 'required|string|max:1000',

        ]);
        $leave = Leave::findOrFail($this->leaveId);

        $leave->update([
            'leave_type_id' => $this->leave_type,
            'start_date_time' => $this->start_date_time,
            'end_date_time' => $this->end_date_time,
            'leave_reason' => $this->leave_reason,
            'status' => 'pending',
        ]);

        $this->dispatch('close-modal', 'edit-leave');
        $this->redirectRoute('leave', navigate: true);
        $this->dispatch('show-notification', 'Leave Request edited successfully!');
    }

    public function promptCancelLeave($id)
    {
        $this->leaveToCancel = $id;
        $this->dispatch('open-modal', 'cancel-leave');
    }

    public function confirmCancelLeave()
    {
        if ($this->leaveToCancel) {
            $leave = Leave::find($this->leaveToCancel);
            if ($leave) {
                $leave->status = 'cancelled';
                $leave->save();
            }

            $this->leaveToCancel = null;
            $this->dispatch('close-modal', 'cancel-leave');
            $this->dispatch('show-notification', 'Leave cancelled successfully!');
        }
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
        $this->dispatch('open-modal', 'view-leave');
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
    public function resetFilters()
    {
        $this->reset([
            'search',
            'selectedLeaveType',
            'selectedStatus',
            'quickRange',
            'startDateFilter',
            'endDateFilter'

        ]);
        $this->resetPage(); // Optional: resets pagination to page 1
    }
    public function edit($id)
    {
        $leave = Leave::findOrFail($id);

        $this->leaveId = $id;
        $this->leave_type = $leave->leave_type_id;
        $this->start_date_time = Carbon::parse($leave->start_date_time)->format('Y-m-d\TH:i');
        $this->end_date_time = Carbon::parse($leave->end_date_time)->format('Y-m-d\TH:i');
        $this->leave_reason = $leave->leave_reason;

        $this->dispatch('open-modal', 'edit-leave');
    }

    public function render()
    {
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

        if ($this->startDateFilter && $this->endDateFilter) {
            $query->whereDate('start_date_time', '>=', $this->startDateFilter)
                ->whereDate('end_date_time', '<=', $this->endDateFilter);
        }

        // Card summary data (recalculated every render)
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
        $leaves = $query->paginate($this->userPerPage);

        return view('livewire.leave.leaves', [
            'leaves' => $leaves,
            'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
            'leaveTypes' => $leaveTypes,
            'leaves_card' => $this->leaves_card,
            'defaultPerPage' => $this->perPage,
        ])->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
}
