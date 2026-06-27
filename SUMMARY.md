## Goal
- Convert HRMS project from Livewire+Filament+Alpine+Flux to pure Laravel (Controllers+Blade+Vanilla JS).
- Fix confusing time displays (0.12 hrs → 0h 7m).
- Add real-world HRMS features (Notice system, Departments, Notifications).

## Constraints & Preferences
- Project is `/home/nischal/Nischal/Projects/hrms-staging`, Laravel 12.
- Keep all 16 models, helpers, migrations, seeders, database logic untouched.
- All views use `@extends('components.layouts.app')` / `@section('content')`.
- Use standard `<form>` POST (no Livewire or Alpine). Alpine used only for UI (modals, dropdowns).

## Progress
### Done
- **Full migration from Livewire/Filament** — 7 phases completed. All auth, attendance, leave, HR, payroll, settings, and holiday features converted to Controllers + Blade + Vanilla JS.
- **Time display fixed everywhere** — Changed from confusing decimal format (`0.25 hrs`, `4.25`) to human-readable `Xh Ym` (`0h 15m`, `4h 15m`). Applied across dashboard, tracker, history views, stats cards, and CSV exports.
- **Notice/Announcement system** — Completed the half-built feature. Model (`Notice`), Controller (CRUD + staff targeting), and `resources/views/hr/notices.blade.php`. HR Managers create notices for specific employees. Sidebar link added.
- **Department management** — New `departments` table, `Department` model, CRUD controller, and `resources/views/hr/departments.blade.php` with employee count per department. Departments assigned to employees via `department_id`.
- **In-app notification system** — 4 notification classes (`LeaveApproved`, `LeaveRejected`, `PayslipGenerated`, `NoticeIssued`) using Laravel's database channel. Notification bell in header with unread badge. Notifications page at `/notifications` with type-based icons. Leave approve/reject and payroll generation automatically send notifications.
- **Bug fixes** — DATEDIFF (SQLite compat), holiday delete route name, LeaveType casing (N+1), auth route middleware (`$errors`), obsolete test cleanup.
- **Tests: 18 passed, 0 failed** (npm build passes too).

### New Features Added
| Feature | Files | Description |
|---------|-------|-------------|
| **Notice Board** | Notice.php, NoticeController, hr/notices.blade.php | HR creates targeted notices; employees see theirs on a dedicated board |
| **Departments** | Department.php, DepartmentController, hr/departments.blade.php | Org structure with head of department, employee count, active/inactive status |
| **In-app Notifications** | 4 Notification classes, NotificationsController, notifications/index.blade.php | Real-time alerts for leave status changes, new payslips, and notices |

## Key Architectural Decisions
- Time formatted using `AttendanceHelper::decimalToHoursMinutes()` (centralized) — outputs `2h 30m` consistently.
- Raw decimal values preserved as `_raw` keys (e.g., `hours_raw`) for progress bar calculations.
- Notifications use Laravel's built-in `database` channel — no extra packages or services needed.
- Notices table already existed but was unused; completed with Model+Controller+Views.

## Key Files Added/Modified
- `app/Helpers/AttendanceHelper.php` — Added `minutesToHoursMinutes()`, updated `decimalToHoursMinutes()` to use `h`/`m` format.
- `app/Http/Controllers/DashboardController.php` — Time calculations now use `AttendanceHelper` for human-readable output.
- `app/Http/Controllers/Attendance/TrackerController.php` — Stats now use `decimalToHoursMinutes()` instead of `number_format()`.
- `app/Http/Controllers/NoticeController.php` — New: Notice CRUD.
- `app/Http/Controllers/DepartmentController.php` — New: Department CRUD.
- `app/Http/Controllers/NotificationsController.php` — New: Notification listing + read.
- `app/Notifications/LeaveApproved.php`, `LeaveRejected.php`, `PayslipGenerated.php`, `NoticeIssued.php` — New notification classes.
- `app/Models/Notice.php`, `Department.php` — New models.
- `app/Models/Employee.php` — Added `department_id` + `department()` relation.
- `resources/views/hr/notices.blade.php` — Notice board view.
- `resources/views/hr/departments.blade.php` — Department management view.
- `resources/views/notifications/index.blade.php` — Notification list view.
- `resources/views/partials/header.blade.php` — Added notification bell with unread badge.
- `resources/views/partials/sidebar.blade.php` — Added Notice Board + Department links.
- `database/migrations/2025_08_06_000001_create_departments_table.php` — New departments table.
- `database/migrations/2025_08_06_000002_add_department_id_to_employees_table.php` — Department FK on employees.

## Remaining Ideas (optional)
- Shift management (different start/end times per department).
- Leave carry-forward / pro-rata entitlement.
- Multi-level leave approval workflow.
- Comp-off for overtime.
- Email notifications (beyond database channel).
- More reports (attendance trends, payroll summary).
- Nepali language / localization.
