<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\User;
use App\Notifications\NoticeIssued;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('HR Manager')) {
            $query = Notice::with('user', 'issuer');

            if ($search = $request->search) {
                $query->where(function ($q) use ($search) {
                    $q->where('notice_type', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('issuer', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            }

            $notices = $query->latest()->paginate(15);
            $employees = User::all();
        } else {
            $notices = Notice::with('issuer')
                ->forEmployee($user->id)
                ->latest()
                ->paginate(15);
            $employees = collect();
        }

        return view('hr.notices', compact('notices', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|string',
            'notice_type' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'message' => 'required|string',
        ]);

        $targetUsers = [];
        $empId = null;

        if ($request->employee_id && $request->employee_id !== 'all') {
            $employee = User::findOrFail($request->employee_id);
            $targetUsers = [$employee];
            $empId = $request->employee_id;
        } else {
            $targetUsers = User::all();
        }

        $notice = Notice::create([
            'employee_id' => $empId ?? null,
            'notice_type' => $request->notice_type,
            'priority' => $request->priority,
            'message' => $request->message,
            'issued_by' => Auth::id(),
        ]);

        foreach ($targetUsers as $employee) {
            $employee->notify(new NoticeIssued($notice));
        }

        $count = count($targetUsers);
        $msg = $empId ? 'Notice created successfully.' : "Notice broadcast to {$count} employees.";

        return redirect()->route('notices.index')->with('status', $msg);
    }

    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $request->validate([
            'employee_id' => 'nullable|string',
            'notice_type' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'message' => 'required|string',
        ]);

        $notice->update([
            'employee_id' => ($request->employee_id && $request->employee_id !== 'all') ? $request->employee_id : null,
            'notice_type' => $request->notice_type,
            'priority' => $request->priority,
            'message' => $request->message,
        ]);

        return redirect()->route('notices.index')->with('status', 'Notice updated successfully.');
    }

    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);
        $notice->delete();

        return redirect()->route('notices.index')->with('status', 'Notice deleted successfully.');
    }

    public function markRead($id)
    {
        $notice = Notice::where('id', $id)
            ->forEmployee(Auth::id())
            ->firstOrFail();

        $notice->markAsRead();

        return redirect()->route('notices.index')->with('status', 'Notice marked as read.');
    }
}
