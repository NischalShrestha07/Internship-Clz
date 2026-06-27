<?php

namespace Tests\Feature;

use App\Filament\Resources\LeaveTypeResource\Pages\CreateLeaveType;
use App\Filament\Resources\LeaveTypeResource\Pages\EditLeaveType;
use App\Filament\Resources\LeaveTypeResource\Pages\ListLeaveTypes;
use App\Models\Leave_Type;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveTypeResourceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles are seeded or created
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Employee']);
    }

    public function test_admin_can_access_leave_type_index_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $response = $this->get('/admin/leave-types'); // Adjust URL based on your Filament panel route

        $response->assertStatus(200);
        Livewire::test(ListLeaveTypes::class)
            ->assertSuccessful();
    }

    public function test_non_admin_cannot_access_leave_type_index_page()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->actingAs($employee);

        $response = $this->get('/admin/leave-types');

        $response->assertStatus(403); // Expect forbidden access
    }

    public function test_admin_can_create_leave_type()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        Livewire::test(CreateLeaveType::class)
            ->set('data.leave_type', 'Casual')
            ->set('data.default_days', 10)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leave_types', [
            'leave_type' => 'Casual',
            'default_days' => 10,
        ]);
    }

    public function test_non_admin_cannot_create_leave_type()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->actingAs($employee);

        $response = $this->get('/admin/leave-types/create');

        $response->assertStatus(403); // Expect forbidden access
    }

    public function test_admin_can_update_leave_type()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $leaveType = LeaveType::create([
            'leave_type' => 'Old Type',
            'default_days' => 5,
        ]);

        Livewire::test(EditLeaveType::class, [
            'record' => $leaveType->getKey(),
        ])
            ->set('data.leave_type', 'Updated Type')
            ->set('data.default_days', 15)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leave_types', [
            'id' => $leaveType->id,
            'leave_type' => 'Updated Type',
            'default_days' => 15,
        ]);
    }

    public function test_non_admin_cannot_update_leave_type()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->actingAs($employee);

        $leaveType = LeaveType::create([
            'leave_type' => 'Old Type',
            'default_days' => 5,
        ]);

        $response = $this->get("/admin/leave-types/{$leaveType->id}/edit");

        $response->assertStatus(403); // Expect forbidden access
    }

    public function test_admin_can_delete_leave_type()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $leaveType = LeaveType::create([
            'leave_type' => 'Temporary',
            'default_days' => 7,
        ]);

        $this->assertDatabaseHas('leave_types', ['id' => $leaveType->id]);

        $leaveType->delete();

        $this->assertDatabaseMissing('leave_types', ['id' => $leaveType->id]);
    }

    public function test_employee_cannot_delete_leave_type()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->assertFalse($employee->can('delete leave types'));

        $leaveType = LeaveType::create([
            'leave_type' => 'Temporary',
            'default_days' => 7,
        ]);

        Livewire::actingAs($employee);

        try {
            Livewire::test(\App\Filament\Resources\LeaveTypeResource\Pages\ListLeaveTypes::class)
                ->callTableAction('delete', $leaveType);
        } catch (\Throwable $e) {

            $this->assertDatabaseHas('leave_types', ['id' => $leaveType->id]);
            return;
        }

        $this->assertDatabaseHas('leave_types', ['id' => $leaveType->id]);
    }
}
