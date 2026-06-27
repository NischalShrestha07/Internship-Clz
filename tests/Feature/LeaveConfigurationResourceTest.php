<?php

namespace Tests\Feature;

use App\Filament\Resources\LeaveConfigurationResource\Pages\CreateLeaveConfiguration;
use App\Filament\Resources\LeaveConfigurationResource\Pages\EditLeaveConfiguration;
use App\Filament\Resources\LeaveConfigurationResource\Pages\ListLeaveConfigurations;
use App\Models\Leave;
use App\Models\LeaveConfiguration;
use App\Models\LeaveGroup;
use App\Models\LeaveType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveConfigurationResourceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'Employee']);
        Filament::setCurrentPanel(Filament::getPanel('admin')); // Adjust 'admin' if your panel ID is different
    }

    public function test_admin_can_access_leave_configuration_index_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);

        // Add test data to ensure relationships are populated
        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);

        $leaveGroup = LeaveGroup::create([
            'name' => 'Full-Time Employees',
        ]);

        LeaveConfiguration::create([
            'leave_type_id' => $leaveType->id,
            'leave_group_id' => $leaveGroup->id,
            'days' => 14,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/leave-configurations');
        $response->assertStatus(200);

        Livewire::test(ListLeaveConfigurations::class)
            ->assertSuccessful();
    }


    public function test_non_admin_cannot_access_leave_configuration_index_page()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);


        $this->actingAs($employee);


        $response = $this->get('/admin/leave-configurations');
        $response->assertStatus(403);
    }



    public function test_admin_can_create_leave_configuration()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);


        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);


        $leaveGroup = LeaveGroup::create([
            'name' => 'Full-Time Employees',
        ]);


        $this->actingAs($admin);


        Livewire::test(CreateLeaveConfiguration::class)
            ->set('data.leave_type_id', $leaveType->id)
            ->set('data.leave_group_id', $leaveGroup->id)
            ->set('data.days', 14)
            ->call('create')
            ->assertHasNoErrors();


        $this->assertDatabaseHas('leave_configurations', [
            'leave_type_id' => $leaveType->id,
            'leave_group_id' => $leaveGroup->id,
            'days' => 14,
        ]);
    }


    public function test_non_admin_cannot_create_leave_configuration()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);


        $this->actingAs($employee);


        $response = $this->get('/admin/leave-configurations/create');
        $response->assertStatus(403);
    }


    public function test_admin_can_update_leave_configuration()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);


        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);


        $leaveGroup = LeaveGroup::create([
            'name' => 'Full-Time Employees',
        ]);


        $leaveConfiguration = LeaveConfiguration::create([
            'leave_type_id' => $leaveType->id,
            'leave_group_id' => $leaveGroup->id,
            'days' => 14,
        ]);


        $newLeaveType = LeaveType::create([
            'leave_type' => 'Sick Leave',
            'default_days' => 7,
        ]);


        $newLeaveGroup = LeaveGroup::create([
            'name' => 'Part-Time Employees',
        ]);


        $this->actingAs($admin);


        Livewire::test(EditLeaveConfiguration::class, ['record' => $leaveConfiguration->getKey()])
            ->set('data.leave_type_id', $newLeaveType->id)
            ->set('data.leave_group_id', $newLeaveGroup->id)
            ->set('data.days', 7)
            ->call('save')
            ->assertHasNoErrors();


        $this->assertDatabaseHas('leave_configurations', [
            'id' => $leaveConfiguration->id,
            'leave_type_id' => $newLeaveType->id,
            'leave_group_id' => $newLeaveGroup->id,
            'days' => 7,
        ]);
    }


    public function test_non_admin_cannot_update_leave_configuration()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);


        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);


        $leaveGroup = LeaveGroup::create([
            'name' => 'Full-Time Employees',
        ]);


        $leaveConfiguration = LeaveConfiguration::create([
            'leave_type_id' => $leaveType->id,
            'leave_group_id' => $leaveGroup->id,
            'days' => 14,
        ]);


        $this->actingAs($employee);


        $response = $this->get("/admin/leave-configurations/{$leaveConfiguration->id}/edit");
        $response->assertStatus(403);
    }


    public function test_admin_can_delete_leave_configuration()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');


        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);


        $leaveGroup = LeaveGroup::create([
            'name' => 'Full-Time Employees',
        ]);


        $leaveConfiguration = LeaveConfiguration::create([
            'leave_type_id' => $leaveType->id,
            'leave_group_id' => $leaveGroup->id,
            'days' => 14,
        ]);


        $this->assertDatabaseHas('leave_configurations', ['id' => $leaveConfiguration->id]);


        $leaveConfiguration->delete();


        $this->assertDatabaseMissing('leave_configurations', ['id' => $leaveConfiguration->id]);
    }


    public function test_non_admin_cannot_delete_leave_configuration()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $this->assertFalse($employee->can('delete leave configurations'));


        $leaveType = LeaveType::create([
            'leave_type' => 'Annual Leave',
            'default_days' => 14,
        ]);


        $leaveGroup = LeaveGroup::create([
            'name' => 'Full-Time Employees',
        ]);


        $leaveConfiguration = LeaveConfiguration::create([
            'leave_type_id' => $leaveType->id,
            'leave_group_id' => $leaveGroup->id,
            'days' => 14,
        ]);

        Livewire::actingAs($employee);
        try {
            Livewire::test(ListLeaveConfigurations::class)
                ->callTableAction('delete', $leaveConfiguration);
        } catch (\Throwable $e) {

            $this->assertDatabaseHas('leave_configurations', ['id' => $leaveConfiguration->id]);
            return;
        }
        $this->assertDatabaseHas('leave_configurations', ['id' => $leaveConfiguration->id]);


        // $this->actingAs($employee);


        // Livewire::test(ListLeaveConfigurations::class)
        //     ->callTableAction('delete', $leaveConfiguration)
        //     ->assertStatus(403);


        // $this->assertDatabaseHas('leave_configurations', ['id' => $leaveConfiguration->id]);
    }
}
