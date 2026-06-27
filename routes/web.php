<?php

use App\Livewire\Leave\Leaves;
use App\Livewire\DashboardSummary;
use App\Livewire\Settings\Profile;
use App\Livewire\Hr\EmployeeReport;
use App\Livewire\Leave\ManageLeave;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\AttSetting;
use Illuminate\Support\Facades\Route;
use App\Livewire\Hr\EmployeeManagement;
use App\Livewire\Settings\LeaveSetting;
use App\Livewire\Settings\Systemsettings;
use App\Livewire\Attendance\AttendanceHistory;
use App\Livewire\Attendance\AttendanceTracker;
use App\Livewire\Attendance\AttendanceCalendar;
use App\Livewire\Attendance\MyAttendanceHistory;
use App\Livewire\Attendance\EmployeeAttendanceHistory;
use App\Livewire\HolidayManager;
use App\Livewire\Settings\AttendanceSetting;
use App\Livewire\Settings\PayrollSettingComponent;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        if ($user->hasAnyRole(['HR Manager', 'Employee'])) {
            return view('dashboard');
        }

        abort(403, 'Unauthorized');
    })->name('dashboard');

    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('/leave', action: Leaves::class)->name('leave');
});

Route::middleware(['auth', 'role:HR Manager'])->group(function () {
    Route::get('/system-settings', Systemsettings::class)->name('settings.system');
    Route::get('/leave-manage', action: ManageLeave::class)->name('leave_manage');
    Route::get('/leave-setting', LeaveSetting::class)->name('setting.leave');
    Route::get('/employee-report/{employeeId}', EmployeeReport::class)->name('employee.show');
});

// Route::get('/attendance-history', AttendanceHistory::class)->name('attendance.history');

Route::get('/my-attendance-history', MyAttendanceHistory::class)
    ->name('attendance.my-history')
    ->middleware(['auth', 'verified']);

Route::get('/employee-attendance-history', EmployeeAttendanceHistory::class)

    ->name('attendance.employee-history')
    ->middleware(['auth', 'verified', 'role:HR Manager']);

Route::get('/attendance-calendar', AttendanceCalendar::class)->name('attendance.calendar');

Route::get('/holidays', HolidayManager::class)
    ->name('holiday.manager')
    ->middleware(['auth', 'verified', 'role:HR Manager']);


Route::get('/attendance-settings', AttendanceSetting::class)
    ->middleware(['auth', 'verified', 'role:HR Manager'])
    ->name('attendance.setting');

Route::get('/payroll-settings', PayrollSettingComponent::class)
    ->middleware(['auth', 'verified', 'role:HR Manager'])
    ->name('payroll.setting');

Route::get('/attendance-tracker', AttendanceTracker::class)
    ->middleware(['auth'])
    ->name('attendance');

Route::get('/hr/employees', EmployeeManagement::class)
    ->name('hr.employees')
    ->middleware(['auth', 'verified', 'role:HR Manager']);


Route::middleware(['auth', 'role:HR Manager'])->group(function () {
    Route::get('/payroll', \App\Livewire\Payroll\PayrollManager::class)->name('payroll.manager');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-payslips', \App\Livewire\Payroll\MyPayslips::class)
        ->name('payroll.my-payslips')
        ->middleware('verified');
});
Route::get('/payslip/print', function () {
    $payslipId = session('print_payslip_id');

    if (!$payslipId) {
        abort(404);
    }

    $payslip = \App\Models\Payroll::with('employee')
        ->where('id', $payslipId)
        ->where('employee_id', auth()->id())
        ->firstOrFail();

    return view('payslip-print', compact('payslip'));
})->name('payslip.print');

require __DIR__ . '/auth.php';
