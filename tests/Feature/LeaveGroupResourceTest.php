<?php

namespace Tests\Feature;

use App\Filament\Resources\LeaveGroupResource\Pages\CreateLeaveGroup;
use App\Filament\Resources\LeaveGroupResource\Pages\EditLeaveGroup;
use App\Filament\Resources\LeaveGroupResource\Pages\ListLeaveGroups;
use App\Models\Leave_group;
use App\Models\LeaveGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveGroupResourceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'view leave group']);
        Permission::create(['name' => 'create leave group']);
        Permission::create(['name' => 'edit leave group']);
        Permission::create(['name' => 'delete leave group']);

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view leave group',
            'create leave group',
            'edit leave group',
            'delete leave group',
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        $this->actingAs($this->user);
    }


    public function test_leave_group_index_page_is_restricted_without_permission()
    {
        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission);

        $this->get('/admin/leave-groups')->assertForbidden();
    }

    public function test_leave_group_can_be_created_with_permission()
    {
        $this->assertTrue($this->user->hasPermissionTo('create leave group'));

        Livewire::test(CreateLeaveGroup::class)
            ->set('data.name', 'Test Group')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leave_groups', [
            'name' => 'Test Group',
        ]);
    }

    public function test_leave_group_creation_is_restricted_without_permission()
    {
        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission);

        $this->get('/admin/leave-groups/create')->assertForbidden();
        $this->assertDatabaseMissing('leave_groups', [
            'name' => 'Test Group',
        ]);
    }

    public function test_leave_group_can_be_updated_with_permission()
    {
        $this->assertTrue($this->user->hasPermissionTo('edit leave group'));

        $leaveGroup = LeaveGroup::factory()->create([
            'name' => 'Old Group',
        ]);

        Livewire::test(EditLeaveGroup::class, [
            'record' => $leaveGroup->getKey(),
        ])
            ->set('data.name', 'Updated Group')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leave_groups', [
            'id' => $leaveGroup->id,
            'name' => 'Updated Group',
        ]);
    }

    public function test_leave_group_update_is_restricted_without_permission()
    {
        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission);

        $leaveGroup = LeaveGroup::factory()->create([
            'name' => 'Old Group',
        ]);

        $this->get("/admin/leave-groups/{$leaveGroup->id}/edit")->assertForbidden();
        $this->assertDatabaseHas('leave_groups', [
            'id' => $leaveGroup->id,
            'name' => 'Old Group',
        ]);
    }

    public function test_leave_group_can_be_deleted_with_permission()
    {
        $this->assertTrue($this->user->hasPermissionTo('delete leave group'));

        $leaveGroup = LeaveGroup::factory()->create([
            'name' => 'Test Data',
        ]);

        $this->assertDatabaseHas('leave_groups', ['id' => $leaveGroup->id]);

        Livewire::test(ListLeaveGroups::class)
            ->assertTableActionVisible(\Filament\Tables\Actions\DeleteAction::getDefaultName(), $leaveGroup)
            ->callTableAction(\Filament\Tables\Actions\DeleteAction::getDefaultName(), $leaveGroup)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('leave_groups', ['id' => $leaveGroup->id]);
    }

    public function test_leave_group_deletion_is_restricted_without_permission()
    {
        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission);

        $leaveGroup = LeaveGroup::factory()->create([
            'name' => 'Test Data',
        ]);

        $this->assertDatabaseHas('leave_groups', ['id' => $leaveGroup->id]);

        $this->get('/admin/leave-groups')->assertForbidden();
        $this->assertDatabaseHas('leave_groups', ['id' => $leaveGroup->id]);
    }
}
