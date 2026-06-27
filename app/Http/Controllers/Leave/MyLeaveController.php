<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveConfigure;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyLeaveController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = Auth::id();

        $selectedLeaveType = $request->leave_type;
        $selectedStatus = $request->status;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $leaveTypesList = EmployeeLeaveConfigure::where('employee_id', $employeeId)->with('leaveType')->get();

        $leaveTypes = $leaveTypesList->map(function ($config) {
            return [
                'id' => $config->leave_type_id,
                'name' => $config->leaveType->leave_type ?? 'Unknown',
            ];
        })->toArray();

        $leaves_card = $leaveTypesList->map(function ($leave_card) {
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

        $query = Leave::where('employee_id', $employeeId)
            ->with('leaveType')
            ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc');

        if ($selectedLeaveType) {
            $query->where('leave_type_id', $selectedLeaveType);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        if ($dateFrom && $dateTo) {
            $query->whereDate('start_date_time', '>=', $dateFrom)
                ->whereDate('end_date_time', '<=', $dateTo);
        }

        $perPage = $request->per_page ?? AttendanceHelper::getRecordsPerPage();
        $leaves = $query->paginate($perPage);

        return view('leave.leaves', [
            'leaves' => $leaves,
            'leaveTypesList' => $leaveTypesList,
            'leaveTypes' => $leaveTypes,
            'leaves_card' => $leaves_card,
            'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
            'defaultPerPage' => AttendanceHelper::getRecordsPerPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|exists:leave_types,id',
            'start_date_time' => 'required|date|after_or_equal:today',
            'end_date_time' => 'required|date|after_or_equal:start_date_time',
            'leave_reason' => 'required|string|max:1000',
        ]);

        $start = Carbon::parse($request->start_date_time);
        $end = Carbon::parse($request->end_date_time);

        if ($start->equalTo($end)) {
            return back()->withErrors(['start_date_time' => 'Start and end date time cannot be exactly the same.'])->withInput();
        }

        if ($start->gt($end)) {
            return back()->withErrors(['start_date_time' => 'Start date cannot be after end date.'])->withInput();
        }

        $overlapExists = Leave::where('employee_id', Auth::id())
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date_time', [$start, $end])
                    ->orWhereBetween('end_date_time', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('start_date_time', '<=', $start)
                            ->where('end_date_time', '>=', $end);
                    });
            })->exists();

        if ($overlapExists) {
            return back()->withErrors(['start_date_time' => 'You already have leave overlapping the selected dates.'])->withInput();
        }

        $config = EmployeeLeaveConfigure::where('employee_id', Auth::id())
            ->where('leave_type_id', $request->leave_type)
            ->first();

        if (!$config || $config->days === 0) {
            return back()->withErrors(['leave_type' => 'You are not allowed to apply for this leave type. Please contact HR.'])->withInput();
        }

        $leaveDurationDays = $this->calculateLeaveDays($start, $end);

        $usedDays = Leave::where('employee_id', Auth::id())
            ->where('leave_type_id', $request->leave_type)
            ->where('status', 'approved')
            ->get()
            ->sum(function ($leave) {
                $s = Carbon::parse($leave->start_date_time);
                $e = Carbon::parse($leave->end_date_time);
                return $this->calculateLeaveDays($s, $e);
            });

        if (($usedDays + $leaveDurationDays) > $config->days) {
            return back()->withErrors(['start_date_time' => 'You have exceeded your leave allowance for this leave type.'])->withInput();
        }

        Leave::create([
            'employee_id' => Auth::id(),
            'leave_type_id' => $request->leave_type,
            'start_date_time' => $request->start_date_time,
            'end_date_time' => $request->end_date_time,
            'leave_reason' => $request->leave_reason,
            'status' => 'pending',
        ]);

        return redirect()->route('leave')->with('status', 'Leave request applied successfully!');
    }

    public function show($id)
    {
        $leave = Leave::with(['user', 'leaveType'])->findOrFail($id);

        if ($leave->employee_id !== Auth::id()) {
            abort(403);
        }

        return view('leave.leave-detail', compact('leave'));
    }

    public function cancel($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->employee_id !== Auth::id()) {
            abort(403);
        }

        $leave->status = 'cancelled';
        $leave->save();

        return redirect()->route('leave')->with('status', 'Leave cancelled successfully!');
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'leaves_details_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $leaves = Leave::where('employee_id', Auth::id())
            ->with(['user', 'leaveType'])
            ->when($request->leave_type, fn($q) => $q->where('leave_type_id', $request->leave_type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from && $request->date_to, function ($q) use ($request) {
                $q->whereDate('start_date_time', '>=', $request->date_from)
                    ->whereDate('end_date_time', '<=', $request->date_to);
            })
            ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->stream(function () use ($leaves) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Leave Type', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Leave Reason', 'Status']);

            foreach ($leaves as $leave) {
                fputcsv($handle, [
                    $leave->leaveType->leave_type,
                    Carbon::parse($leave->start_date_time)->format('M d, Y'),
                    Carbon::parse($leave->start_date_time)->format('g:i A'),
                    Carbon::parse($leave->end_date_time)->format('M d, Y'),
                    Carbon::parse($leave->end_date_time)->format('g:i A'),
                    $leave->leave_reason,
                    ucfirst($leave->status),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    protected function calculateLeaveDays(Carbon $start, Carbon $end)
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
}
