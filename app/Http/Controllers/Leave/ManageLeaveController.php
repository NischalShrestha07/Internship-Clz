<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Helpers\AttendanceHelper;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageLeaveController extends Controller
{
    public function index(Request $request)
    {
        $selectedLeaveType = $request->leave_type;
        $selectedStatus = $request->status;
        $search = $request->search;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $query = Leave::with(['user', 'leaveType']);

        if (Auth::user()->hasRole('HR Manager')) {
            $query->where('employee_id', '!=', auth()->id());
        } else {
            $query->where('employee_id', auth()->id());
        }

        $baseQuery = clone $query;

        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $cancelledCount = (clone $baseQuery)->where('status', 'cancelled')->count();
        $declinedCount = (clone $baseQuery)->where('status', 'declined')->count();

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

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

        $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        $leaveTypes = LeaveType::all();
        $perPage = $request->per_page ?? AttendanceHelper::getRecordsPerPage();
        $leaves = $query->paginate($perPage);

        return view('leave.manage-leave', [
            'leaves' => $leaves,
            'leaveTypes' => $leaveTypes,
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
            'cancelledCount' => $cancelledCount,
            'declinedCount' => $declinedCount,
            'defaultPerPage' => AttendanceHelper::getRecordsPerPage(),
            'statuses' => ['approved', 'pending', 'declined', 'cancelled'],
        ]);
    }

    public function show($id)
    {
        $leave = Leave::with(['user', 'leaveType'])->findOrFail($id);

        return view('leave.manage-leave-detail', compact('leave'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,declined',
            'remarks' => 'required|string|max:1000',
        ]);

        $leave = Leave::with('leaveType')->findOrFail($id);
        $leave->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        if ($leave->status === 'approved') {
            $leave->user->notify(new LeaveApproved($leave));
        } elseif ($leave->status === 'declined') {
            $leave->user->notify(new LeaveRejected($leave));
        }

        return redirect()->route('leave_manage')->with('status', 'Leave status updated successfully!');
    }

    public function exportCsv(Request $request)
    {
        $filename = 'leave_requests_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $leaves = Leave::with(['user', 'leaveType'])
            ->when(Auth::user()->hasRole('HR Manager'), function ($q) {
                $q->where('employee_id', '!=', auth()->id());
            })
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            })
            ->when($request->leave_type, fn($q) => $q->where('leave_type_id', $request->leave_type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from && $request->date_to, function ($q) use ($request) {
                $q->whereDate('start_date_time', '>=', $request->date_from)
                    ->whereDate('end_date_time', '<=', $request->date_to);
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->stream(function () use ($leaves) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee Name', 'Leave Type', 'Start Date', 'Start Time', 'End Date', 'End Time', 'Status']);

            foreach ($leaves as $leave) {
                fputcsv($handle, [
                    $leave->user->name,
                    $leave->leaveType->leave_type,
                    Carbon::parse($leave->start_date_time)->format('M d, Y'),
                    Carbon::parse($leave->start_date_time)->format('g:i A'),
                    Carbon::parse($leave->end_date_time)->format('M d, Y'),
                    Carbon::parse($leave->end_date_time)->format('g:i A'),
                    ucfirst($leave->status),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
