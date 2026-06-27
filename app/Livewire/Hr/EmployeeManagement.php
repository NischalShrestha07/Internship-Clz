<?php

namespace App\Livewire\Hr;

use App\Helpers\AttendanceHelper;
use App\Models\Attendance;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class EmployeeManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $showForm = false;
    public $editMode = false;
    public $currentEmployeeId;
    // Form fields
    public $name;
    public $email;
    public $password;
    public $role = 'Employee';
    public $address;
    public $phone_number;
    public $gender;
    public $date_of_birth;
    public $joined_date;
    public $position;
    public $image;
    public $citizenship_front;
    public $citizenship_back;
    public $CV_file;
    public $basic_salary;
    public $employeeToDelete = null;
    public $search = '';
    public $imagePreview;

    public $perPage = null;
    public $userPerPage = null;

    public $selectedEmployee = null;
    public $attendanceReport = null;
    public $leaveReport = null;
    public $attendanceSummary = [];
    public $leaveSummary = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->currentEmployeeId,
            'password' => $this->editMode ? 'nullable|min:8' : 'required|min:8',
            'role' => 'required|in:Admin,HR Manager,Employee',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'joined_date' => 'required|date',
            'position' => 'required|string',
            'basic_salary' => 'required|numeric|min:0'
        ];
        // Only validate files if they're not strings (existing files) or when they're newly uploaded
        if (!$this->editMode || !is_string($this->image)) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        if (!$this->editMode || !is_string($this->citizenship_front)) {
            $rules['citizenship_front'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }
        if (!$this->editMode || !is_string($this->citizenship_back)) {
            $rules['citizenship_back'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }
        if (!$this->editMode || !is_string($this->CV_file)) {
            $rules['CV_file'] = 'nullable|file|mimes:pdf,doc,docx,png,jpeg|max:5120';
        }

        return $rules;
    }
    public function confirmDelete($id)
    {
        $this->employeeToDelete = $id;
        $this->dispatch('open-modal', 'cancel-leave');
    }

    public function confirmDeleteEmployee()
    {
        if ($this->employeeToDelete) {
            $employee = User::findOrFail($this->employeeToDelete);
            if ($employee->employee) {
                $employee->employee->delete();
            }

            $employee->delete();

            $this->employeeToDelete = null;
            $this->dispatch('close-modal', 'cancel-leave');
            $this->dispatch('show-notification', 'Employee deleted successfully!');
        }
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'userPerPage' => ['except' => null],
    ];

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->perPage = AttendanceHelper::getRecordsPerPage();
        $this->userPerPage = $this->userPerPage ?? $this->perPage;
    }
    public function resetToDefaultPerPage()
    {
        $this->userPerPage = $this->perPage;
        $this->resetPage();
    }

    public function render()
    {

        $roles = Role::whereIn('name', ['HR Manager', 'Employee'])->get();
        $query = User::role(['HR Manager', 'Employee'])
            ->with('employee')
            ->orderBy('name');
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where('name', 'like', $searchTerm);
        }
        $employees = $query->paginate($this->userPerPage ?? $this->perPage);
        $employee = null;
        if ($this->editMode && $this->currentEmployeeId) {
            $employee = User::with('employee')->find($this->currentEmployeeId);
        }
        return view('livewire.hr.employee-management', [
            'employees' => $employees,
            'roles' => $roles,
            'employee' => $employee,
            'defaultPerPage' => $this->perPage,
        ]);
    }
    public function addEmployee()
    {
        $this->resetForm();
        $this->imagePreview = null;
        $this->showForm = true;
        $this->editMode = false;
    }
    public function back()
    {
        $this->resetForm();
        $this->imagePreview = null;
        $this->showForm = false;
        $this->editMode = false;
    }
    public function viewEmployee($id)
    {
        $user = User::with('employee')->findOrFail($id);
        $this->currentEmployeeId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->getRoleNames()->first() ?? 'Employee';
        $this->address = $user->employee->address ?? '';
        $this->phone_number = $user->employee->phone_number ?? '';
        $this->gender = $user->employee->gender ?? '';
        $this->date_of_birth = $user->employee->date_of_birth ?? '';
        $this->joined_date = $user->employee->joined_date ?? '';
        $this->position = $user->employee->position ?? '';
        $this->basic_salary = $user->employee->basic_salary ?? '';
        // Initialize file fields as strings for existing files
        $this->imagePreview = $user->employee->image ?? null;
        $this->citizenship_front = $user->employee->citizenship_front ?? null;
        $this->citizenship_back = $user->employee->citizenship_back ?? null;
        $this->CV_file = $user->employee->CV_file ?? null;
        $this->dispatch('open-modal', 'view-employee');
    }
    public function editEmployee($id)
    {
        $user = User::with('employee')->findOrFail($id);
        $this->currentEmployeeId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->getRoleNames()->first() ?? 'Employee';
        $this->address = $user->employee->address ?? '';
        $this->phone_number = $user->employee->phone_number ?? '';
        $this->gender = $user->employee->gender ?? '';
        $this->date_of_birth = $user->employee->date_of_birth ?? '';
        $this->joined_date = $user->employee->joined_date ?? '';
        $this->position = $user->employee->position ?? '';
        $this->basic_salary = $user->employee->basic_salary ?? '';
        // Initialize file fields as strings for existing files
        $this->imagePreview = $user->employee->image ?? null;
        $this->citizenship_front = $user->employee->citizenship_front ?? null;
        $this->citizenship_back = $user->employee->citizenship_back ?? null;
        $this->CV_file = $user->employee->CV_file  ?? null;
        $this->showForm = true;
        $this->editMode = true;
    }

    public function saveEmployee()
    {
        $this->validate();

        // Handle file uploads - only process new uploads (non-string means new upload)
        $imagePath = (!is_string($this->image) && $this->image) ? $this->image->store('employee_images', 'public') : (is_string($this->image) ? $this->image : null);
        $citizenshipFrontPath = (!is_string($this->citizenship_front) && $this->citizenship_front) ? $this->citizenship_front->store('citizenship', 'public') : (is_string($this->citizenship_front) ? $this->citizenship_front : null);
        $citizenshipBackPath = (!is_string($this->citizenship_back) && $this->citizenship_back) ? $this->citizenship_back->store('citizenship', 'public') : (is_string($this->citizenship_back) ? $this->citizenship_back : null);
        $cvPath = (!is_string($this->CV_file) && $this->CV_file) ? $this->CV_file->store('CVs', 'public') : (is_string($this->CV_file) ? $this->CV_file : null);

        if ($this->editMode) {
            // Update existing employee
            $user = User::find($this->currentEmployeeId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : $user->password
            ]);
            $user->syncRoles([$this->role]);

            $employee = $user->employee;

            $employeeData = [
                'address' => $this->address,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'joined_date' => $this->joined_date,
                'position' => $this->position,
                'basic_salary' => $this->basic_salary,
            ];

            // Handle image field
            if ($imagePath !== null) {
                $employeeData['image'] = $imagePath;
            }

            // Handle citizenship_front
            if ($citizenshipFrontPath !== null) {
                $employeeData['citizenship_front'] = $citizenshipFrontPath;
            } elseif ($this->citizenship_front === null && $employee && $employee->citizenship_front) {
                Storage::disk('public')->delete($employee->citizenship_front);
                $employeeData['citizenship_front'] = null;
            }

            // Handle citizenship_back
            if ($citizenshipBackPath !== null) {
                $employeeData['citizenship_back'] = $citizenshipBackPath;
            } elseif ($this->citizenship_back === null && $employee && $employee->citizenship_back) {
                Storage::disk('public')->delete($employee->citizenship_back);
                $employeeData['citizenship_back'] = null;
            }

            // Handle CV_file
            if ($cvPath !== null) {
                $employeeData['CV_file'] = $cvPath;
            } elseif ($this->CV_file === null && $employee && $employee->CV_file) {
                Storage::disk('public')->delete($employee->CV_file);
                $employeeData['CV_file'] = null;
            }

            $user->employee()->updateOrCreate(
                ['employee_id' => $user->id],
                $employeeData
            );
        } else {
            // Create new employee
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->role);

            Employee::create([
                'employee_id' => $user->id,
                'address' => $this->address,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'joined_date' => $this->joined_date,
                'position' => $this->position,
                'image' => $imagePath,
                'citizenship_front' => $citizenshipFrontPath,
                'citizenship_back' => $citizenshipBackPath,
                'CV_file' => $cvPath,
                'basic_salary' => $this->basic_salary
            ]);
        }

        $this->resetForm();
        $this->dispatch('show-notification', 'Employee ' . ($this->editMode ? 'updated' : 'added') . ' successfully!');
    }

    public function cleanupTempFiles()
    {
        if ($this->citizenship_front && !is_string($this->citizenship_front)) {
            $this->citizenship_front->delete();
        }
        if ($this->citizenship_back && !is_string($this->citizenship_back)) {
            $this->citizenship_back->delete();
        }
        if ($this->CV_file && !is_string($this->CV_file)) {
            $this->CV_file->delete();
        }
    }
    public function resetForm()
    {
        $this->cleanupTempFiles();
        $this->reset([
            'name',
            'email',
            'password',
            'role',
            'address',
            'phone_number',
            'gender',
            'date_of_birth',
            'joined_date',
            'position',
            'image',
            'citizenship_front',
            'citizenship_back',
            'CV_file',
            'basic_salary',
            'imagePreview' // Add this line
        ]);
        $this->resetErrorBag();
        $this->showForm = false;
    }

    public function clearCv()
    {
        $this->CV_file = null;
    }

    public function clearCitizenshipFront()
    {
        $this->citizenship_front = null;
    }

    public function clearCitizenshipBack()
    {
        $this->citizenship_back = null;
    }

    // Add these methods
    public function showAttendanceReport($employeeId)
    {
        $this->selectedEmployee = User::with('employee')->find($employeeId);

        // Get attendance data for the selected employee
        $this->attendanceReport = Attendance::where('employee_id', $employeeId)
            ->whereBetween('check_in', [now()->startOfMonth(), now()->endOfMonth()])
            ->orderBy('check_in', 'desc')
            ->get();

        // Calculate summary
        $this->attendanceSummary = [
            'present' => $this->attendanceReport->whereNotNull('check_out')->count(),
            'absent' => 0, // You'll need your own logic for absences
            'total_hours' => $this->attendanceReport->sum(function ($record) {
                return $record->check_out
                    ? Carbon::parse($record->check_in)->diffInHours(Carbon::parse($record->check_out))
                    : 0;
            })
        ];

        $this->dispatch('open-modal', 'attendance-report');
    }

    public function showLeaveReport($employeeId)
    {
        $this->selectedEmployee = User::with('employee')->find($employeeId);

        $this->leaveReport = Leave::with('leaveType')
            ->where('employee_id', $employeeId)
            ->orderBy('start_date_time', 'desc')
            ->get();

        $this->leaveSummary = [
            'approved' => $this->leaveReport->where('status', 'approved')->count(),
            'rejected' => $this->leaveReport->where('status', 'rejected')->count(),
            'declined' => $this->leaveReport->where('status', 'declined')->count(),
            'pending' => $this->leaveReport->where('status', 'pending')->count(),
            'total_days' => $this->leaveReport->where('status', 'approved')->sum(function ($leave) {
                return $leave->leaveType->default_days ?? 0;
            }),
        ];

        $this->dispatch('open-modal', 'leave-report');
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
