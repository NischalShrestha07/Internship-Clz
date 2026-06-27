<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('employee');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->role($role);
        }

        $perPage = (int) $request->get('per_page', 10);
        $allowedPerPage = [5, 10, 15, 20, 25];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $roles = Role::all();

        return view('hr.employee-management', compact('employees', 'roles', 'perPage'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('hr.employee-management', [
            'showForm' => true,
            'editMode' => false,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:Admin,HR Manager,Employee',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'position' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'citizenship_front' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'citizenship_back' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'CV_file' => 'nullable|mimes:pdf,doc,docx,png,jpeg|max:5120',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        $employeeData = [
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'joined_date' => $validated['joined_date'],
            'position' => $validated['position'],
            'basic_salary' => $validated['basic_salary'],
        ];

        if ($request->hasFile('image')) {
            $employeeData['image'] = $request->file('image')->store('employee_images', 'public');
        }
        if ($request->hasFile('citizenship_front')) {
            $employeeData['citizenship_front'] = $request->file('citizenship_front')->store('citizenship', 'public');
        }
        if ($request->hasFile('citizenship_back')) {
            $employeeData['citizenship_back'] = $request->file('citizenship_back')->store('citizenship', 'public');
        }
        if ($request->hasFile('CV_file')) {
            $employeeData['CV_file'] = $request->file('CV_file')->store('CVs', 'public');
        }

        $user->employee()->create($employeeData);

        return redirect()->route('hr.employees')->with('status', 'Employee created successfully.');
    }

    public function edit($id)
    {
        $user = User::with('employee')->findOrFail($id);
        $roles = Role::all();

        return view('hr.employee-management', [
            'showForm' => true,
            'editMode' => true,
            'editUser' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'role' => 'required|in:Admin,HR Manager,Employee',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'position' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'citizenship_front' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'citizenship_back' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
            'CV_file' => 'nullable|mimes:pdf,doc,docx,png,jpeg|max:5120',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        $employeeData = [
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'joined_date' => $validated['joined_date'],
            'position' => $validated['position'],
            'basic_salary' => $validated['basic_salary'],
        ];

        $fileFields = [
            'image' => 'employee_images',
            'citizenship_front' => 'citizenship',
            'citizenship_back' => 'citizenship',
            'CV_file' => 'CVs',
        ];

        foreach ($fileFields as $field => $directory) {
            if ($request->hasFile($field)) {
                if ($user->employee && $user->employee->$field) {
                    Storage::disk('public')->delete($user->employee->$field);
                }
                $employeeData[$field] = $request->file($field)->store($directory, 'public');
            } elseif ($request->get('remove_' . $field) === '1') {
                if ($user->employee && $user->employee->$field) {
                    Storage::disk('public')->delete($user->employee->$field);
                }
                $employeeData[$field] = null;
            }
        }

        Employee::updateOrCreate(
            ['employee_id' => $user->id],
            $employeeData
        );

        return redirect()->route('hr.employees')->with('status', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->employee) {
            foreach (['image', 'citizenship_front', 'citizenship_back', 'CV_file'] as $field) {
                if ($user->employee->$field) {
                    Storage::disk('public')->delete($user->employee->$field);
                }
            }
            $user->employee->delete();
        }

        $user->delete();

        return redirect()->route('hr.employees')->with('status', 'Employee deleted successfully.');
    }

    public function show($id)
    {
        $user = User::with(['employee', 'roles'])->findOrFail($id);
        return view('hr.employee-show', compact('user'));
    }

    public function attendanceReport($id)
    {
        $user = User::findOrFail($id);

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $records = Attendance::where('employee_id', $id)
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->orderBy('check_in', 'desc')
            ->get();

        $presentCount = $records->whereNotNull('check_in')->count();
        $totalHours = $records->sum(function ($record) {
            if ($record->check_in && $record->check_out) {
                return Carbon::parse($record->check_in)->floatDiffInHours(Carbon::parse($record->check_out));
            }
            return 0;
        });

        $summary = [
            'present' => $presentCount,
            'total_hours' => round($totalHours, 1),
        ];

        return view('hr.employee-attendance-report', compact('user', 'records', 'summary'));
    }

    public function leaveReport($id)
    {
        $user = User::findOrFail($id);

        $leaves = Leave::with('leaveType')
            ->where('employee_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'approved' => $leaves->where('status', 'approved')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'declined' => $leaves->where('status', 'declined')->count(),
            'total_days' => $leaves->sum(function ($leave) {
                return $this->calculateLeaveDays($leave);
            }),
        ];

        $records = $leaves;

        return view('hr.employee-leave-report', compact('user', 'records', 'summary'));
    }

    private function calculateLeaveDays($leave)
    {
        $start = Carbon::parse($leave->start_date_time);
        $end = Carbon::parse($leave->end_date_time);

        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            return 1;
        }

        $days = 0;
        $current = $start->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        while ($current->lte($endDay)) {
            if ($current->format('Y-m-d') === $start->format('Y-m-d')) {
                $days += $start->format('H:i') >= '14:00' ? 0.5 : 1;
            } elseif ($current->format('Y-m-d') === $end->format('Y-m-d')) {
                $days += $end->format('H:i') <= '14:00' ? 0.5 : 1;
            } else {
                $days += 1;
            }
            $current->addDay();
        }

        return $days;
    }
}
