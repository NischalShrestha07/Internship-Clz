<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\AppearanceController;
use App\Http\Controllers\Settings\SystemSettingController;
use App\Http\Controllers\Settings\LeaveSettingController;
use App\Http\Controllers\Settings\AttendanceSettingController;
use App\Http\Controllers\Settings\PayrollSettingController;
use App\Http\Controllers\Settings\DeleteUserController;
use App\Http\Controllers\Attendance\TrackerController;
use App\Http\Controllers\Attendance\MyHistoryController;
use App\Http\Controllers\Attendance\EmployeeHistoryController;
use App\Http\Controllers\Attendance\CalendarController;
use App\Http\Controllers\Leave\MyLeaveController;
use App\Http\Controllers\Leave\ManageLeaveController;
use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Hr\EmployeeReportController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\MyPayslipController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('settings.password');
    Route::post('settings/password', [PasswordController::class, 'update']);
    Route::get('settings/appearance', [AppearanceController::class, 'edit'])->name('settings.appearance');
    Route::post('settings/appearance', [AppearanceController::class, 'update']);
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('settings.profile');
    Route::post('settings/profile', [ProfileController::class, 'update']);
    Route::delete('settings/profile', [DeleteUserController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('/leave', [MyLeaveController::class, 'index'])->name('leave');
    Route::post('/leave', [MyLeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{id}', [MyLeaveController::class, 'show'])->name('leave.show');
    Route::patch('/leave/{id}/cancel', [MyLeaveController::class, 'cancel'])->name('leave.cancel');
    Route::get('/leave/csv', [MyLeaveController::class, 'exportCsv'])->name('leave.csv');
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markRead'])->name('notifications.read');
});

Route::middleware(['auth', 'role:HR Manager'])->group(function () {
    Route::get('/system-settings', [SystemSettingController::class, 'edit'])->name('settings.system');
    Route::post('/system-settings', [SystemSettingController::class, 'update']);
    Route::get('/leave-manage', [ManageLeaveController::class, 'index'])->name('leave_manage');
    Route::get('/leave-manage/{id}', [ManageLeaveController::class, 'show'])->name('leave_manage.show');
    Route::patch('/leave-manage/{id}', [ManageLeaveController::class, 'update'])->name('leave_manage.update');
    Route::get('/leave-manage/csv', [ManageLeaveController::class, 'exportCsv'])->name('leave_manage.csv');
    Route::get('/leave-setting', [LeaveSettingController::class, 'edit'])->name('setting.leave');
    Route::post('/leave-setting', [LeaveSettingController::class, 'update']);
    Route::get('/employee-report/{employeeId}', [EmployeeReportController::class, 'show'])->name('employee.show');
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holiday.manager');
    Route::post('/holidays', [HolidayController::class, 'store']);
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holiday.destroy');
    Route::get('/attendance-settings', [AttendanceSettingController::class, 'edit'])->name('attendance.setting');
    Route::post('/attendance-settings', [AttendanceSettingController::class, 'update']);
    Route::get('/payroll-settings', [PayrollSettingController::class, 'edit'])->name('payroll.setting');
    Route::post('/payroll-settings', [PayrollSettingController::class, 'update']);
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
    Route::get('/hr/employees', [EmployeeController::class, 'index'])->name('hr.employees');
    Route::get('/hr/employees/create', [EmployeeController::class, 'create'])->name('hr.employees.create');
    Route::post('/hr/employees', [EmployeeController::class, 'store'])->name('hr.employees.store');
    Route::get('/hr/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('hr.employees.edit');
    Route::put('/hr/employees/{id}', [EmployeeController::class, 'update'])->name('hr.employees.update');
    Route::delete('/hr/employees/{id}', [EmployeeController::class, 'destroy'])->name('hr.employees.destroy');
    Route::get('/hr/employees/{id}', [EmployeeController::class, 'show'])->name('hr.employees.show');
    Route::get('/hr/employees/{id}/attendance-report', [EmployeeController::class, 'attendanceReport'])->name('hr.employees.attendance-report');
    Route::get('/hr/employees/{id}/leave-report', [EmployeeController::class, 'leaveReport'])->name('hr.employees.leave-report');
    Route::get('/employee-report/{employeeId}/csv', [EmployeeReportController::class, 'exportCsv'])->name('employee.report.csv');
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.manager');
    Route::post('/payroll/generate', [PayrollController::class, 'generatePayroll'])->name('payroll.generate');
    Route::post('/payroll/generate-all', [PayrollController::class, 'generateAllPayroll'])->name('payroll.generate-all');
    Route::get('/payroll/csv', [PayrollController::class, 'downloadCsv'])->name('payroll.csv');
    Route::get('/payroll/salary-settings', [PayrollController::class, 'salarySettings'])->name('payroll.salary-settings');
    Route::post('/payroll/salary-settings', [PayrollController::class, 'updateSalarySettings'])->name('payroll.salary-settings.update');
    Route::get('/payroll/{id}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
    Route::post('/payroll/{id}/update', [PayrollController::class, 'update'])->name('payroll.update');
    Route::post('/payroll/{id}/mark-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
    Route::get('/payroll/{id}/details', [PayrollController::class, 'viewDetails'])->name('payroll.details');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
    Route::put('/notices/{id}', [NoticeController::class, 'update'])->name('notices.update');
    Route::delete('/notices/{id}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    Route::post('/notices/{id}/mark-read', [NoticeController::class, 'markRead'])->name('notices.mark-read');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-attendance-history', [MyHistoryController::class, 'index'])->name('attendance.my-history');
    Route::get('/my-attendance-history/{id}/sessions', [MyHistoryController::class, 'sessions'])->name('attendance.my-history.sessions');
    Route::get('/employee-attendance-history', [EmployeeHistoryController::class, 'index'])
        ->middleware('role:HR Manager')
        ->name('attendance.employee-history');
    Route::get('/employee-attendance-history/csv', [EmployeeHistoryController::class, 'downloadCSV'])
        ->middleware('role:HR Manager')
        ->name('attendance.employee-history.csv');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance-calendar', [CalendarController::class, 'index'])->name('attendance.calendar');
    Route::post('/attendance-calendar/event', [CalendarController::class, 'store'])->name('attendance.calendar.event.store');
    Route::get('/attendance-calendar/event/{event}/edit', [CalendarController::class, 'edit'])->name('attendance.calendar.event.edit');
    Route::post('/attendance-calendar/event/{event}', [CalendarController::class, 'update'])->name('attendance.calendar.event.update');
    Route::delete('/attendance-calendar/event/{event}', [CalendarController::class, 'destroy'])->name('attendance.calendar.event.destroy');
    Route::get('/attendance-tracker', [TrackerController::class, 'index'])->name('attendance');
    Route::post('/attendance/punch-in', [TrackerController::class, 'punchIn'])->name('attendance.punch-in');
    Route::post('/attendance/punch-out', [TrackerController::class, 'punchOut'])->name('attendance.punch-out');
    Route::post('/attendance/break-start', [TrackerController::class, 'startBreak'])->name('attendance.break-start');
    Route::post('/attendance/break-end', [TrackerController::class, 'endBreak'])->name('attendance.break-end');
    Route::get('/my-payslips', [MyPayslipController::class, 'index'])->name('payroll.my-payslips');
    Route::get('/my-payslips/{id}/view', [MyPayslipController::class, 'viewPayslip'])->name('payroll.payslip.view');
    Route::get('/my-payslips/{id}/download', [MyPayslipController::class, 'downloadPayslip'])->name('payroll.payslip.download');
});

Route::get('/payslip/print', [MyPayslipController::class, 'print'])->name('payslip.print');
