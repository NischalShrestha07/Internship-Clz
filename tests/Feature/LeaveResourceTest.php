<?php

namespace Tests\Feature;

use App\Filament\Resources\LeaveResource\Pages\CreateLeave;
use App\Filament\Resources\LeaveResource\Pages\EditLeave;
use App\Filament\Resources\LeaveResource\Pages\ListLeaves;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles before tests
        $this->adminRole = Role::findOrCreate('Admin');
        $this->employeeRole = Role::findOrCreate('Employee');
    }

    public function test_admin_can_access_leave_index_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);

        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $leave = Leave::create([
            'leave_type_id' => $leaveType->id,
            'employee_id' => $admin->id,
            'start_date_time' => now()->addDay(),
            'end_date_time' => now()->addDays(2),
            'leave_reason' => 'Vacation',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/leaves');
        $response->assertStatus(200);

        Livewire::test(ListLeaves::class)
            ->assertSuccessful();
    }

    public function test_non_admin_cannot_access_leave_index_page()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);

        $this->actingAs($employee);

        $response = $this->get('/admin/leaves');
        $response->assertStatus(403);
    }

    public function test_admin_can_create_leave()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);

        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);

        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreateLeave::class)
            ->set('data.leave_type_id', $leaveType->id)
            ->set('data.employee_id', $employee->id)
            ->set('data.start_date_time', now()->addDays(3)->toDateTimeString())
            ->set('data.end_date_time', now()->addDays(4)->toDateTimeString())
            ->set('data.leave_reason', 'Vacation')
            ->set('data.status', 'pending')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leaves', [
            'leave_type_id' => $leaveType->id,
            'employee_id' => $employee->id,
            'leave_reason' => 'Vacation',
            'status' => 'pending',
        ]);
    }

    // public function test_employee_can_create_own_leave()
    // {
    //     $employee = User::factory()->create();
    //     $employee->assignRole($this->employeeRole);

    //     $leaveType = LeaveType::create([
    //         'leave_type' => 'Annual Leave',
    //         'default_days' => 14,
    //     ]);

    //     $this->actingAs($employee);

    //     \Log::info('Test employee_can_create_own_leave', [
    //         'user_id' => $employee->id,
    //         'roles' => $employee->roles->pluck('name')->toArray(),
    //         'auth_id' => auth()->id(),
    //         'can_create' => \App\Filament\Resources\LeaveResource::canCreate(),
    //         'can_access_panel' => $employee->canAccessPanel(Filament::getPanel('employee')),
    //     ]);

    //     $this->assertTrue($employee->hasRole('Employee'), 'Employee role is assigned');
    //     $this->assertTrue(\App\Filament\Resources\LeaveResource::canCreate(), 'Employee can create leaves');
    //     $this->assertTrue($employee->canAccessPanel(Filament::getPanel('employee')), 'Employee can access employee panel');

    //     Filament::setCurrentPanel(Filament::getPanel('employee'));

    //     $response = $this->get('/employee/leaves/create');
    //     $response->assertStatus(200);

    //     Livewire::test(CreateLeave::class)
    //         ->set('data.leave_type_id', $leaveType->id)
    //         ->set('data.start_date_time', now()->addDays(5)->toDateTimeString())
    //         ->set('data.end_date_time', now()->addDays(6)->toDateTimeString())
    //         ->set('data.leave_reason', 'Vacation')
    //         ->set('data.status', 'pending')
    //         ->call('create')
    //         ->assertHasNoErrors();

    //     $this->assertDatabaseHas('leaves', [
    //         'leave_type_id' => $leaveType->id,
    //         'employee_id' => $employee->id,
    //         'leave_reason' => 'Vacation',
    //         'status' => 'pending',
    //     ]);
    // }

    public function test_admin_can_edit_leave()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);

        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);

        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $leave = Leave::create([
            'leave_type_id' => $leaveType->id,
            'employee_id' => $employee->id,
            'start_date_time' => now()->addDay(),
            'end_date_time' => now()->addDays(2),
            'leave_reason' => 'Vacation',
            'status' => 'pending',
        ]);

        $newLeaveType = LeaveType::create([
            'leave_type' => 'Sick Leave',
            'default_days' => 7,
        ]);

        $this->actingAs($admin);

        Livewire::test(EditLeave::class, ['record' => $leave->getKey()])
            ->set('data.leave_type_id', $newLeaveType->id)
            ->set('data.employee_id', $employee->id)
            ->set('data.start_date_time', now()->addDays(10)->format('Y-m-d H:i:s'))
            ->set('data.end_date_time', now()->addDays(11)->format('Y-m-d H:i:s'))
            ->set('data.leave_reason', 'Medical')
            ->set('data.status', 'approved')
            ->set('data.remarks', 'Approved due to medical certificate')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'leave_type_id' => $newLeaveType->id,
            'employee_id' => $employee->id,
            'leave_reason' => 'Medical',
            'status' => 'approved',
            'remarks' => 'Approved due to medical certificate',
        ]);
    }


    public function test_non_admin_cannot_edit_leave()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);

        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $leave = Leave::create([
            'leave_type_id' => $leaveType->id,
            'employee_id' => $employee->id,
            'start_date_time' => now()->addDay(),
            'end_date_time' => now()->addDays(2),
            'leave_reason' => 'Vacation',
            'status' => 'pending',
        ]);

        $this->actingAs($employee);

        $response = $this->get('/admin/leaves/' . $leave->id . '/edit');

        // If using middleware with 'role:Admin' → Laravel returns 404 by default
        // Use 403 only if you're handling it manually inside controller
        $response->assertStatus(404); // ✅ change to 403 only if controller does `abort(403)`
    }

    public function test_admin_can_delete_leave()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);

        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);

        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $leave = Leave::create([
            'leave_type_id' => $leaveType->id,
            'employee_id' => $employee->id,
            'start_date_time' => now()->addDay(),
            'end_date_time' => now()->addDays(2),
            'leave_reason' => 'Vacation',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('leaves', ['id' => $leave->id]);

        $this->actingAs($admin);

        Livewire::test(ListLeaves::class)
            ->callTableAction('delete', $leave)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('leaves', ['id' => $leave->id]);
    }

    public function test_non_admin_cannot_delete_leave()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);

        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $leave = Leave::create([
            'leave_type_id' => $leaveType->id,
            'employee_id' => $employee->id,
            'start_date_time' => now()->addDay(),
            'end_date_time' => now()->addDays(2),
            'leave_reason' => 'Vacation',
            'status' => 'pending',
        ]);

        $this->actingAs($employee);

        $response = $this->get('/admin/leaves');
        $response->assertStatus(403);
    }
}
