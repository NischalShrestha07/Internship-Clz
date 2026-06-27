<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles and store their IDs
        $this->adminRole = Role::create(['name' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'Employee']);
        Filament::setCurrentPanel(Filament::getPanel('admin')); // Adjust 'admin' if your panel ID is different
    }


    public function test_admin_can_access_user_index_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);


        $this->actingAs($admin);


        $response = $this->get('/admin/users');
        $response->assertStatus(200);


        Livewire::test(ListUsers::class)
            ->assertSuccessful();
    }


    public function test_non_admin_cannot_access_user_index_page()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);


        $this->actingAs($employee);


        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }


    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);


        $this->actingAs($admin);


        Livewire::test(CreateUser::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.roles', [$this->adminRole->id]) // Use role ID
            ->set('data.employee.address', '123 Test Street')
            ->set('data.employee.gender', 'male')
            ->set('data.employee.date_of_birth', '1990-01-01')
            ->set('data.employee.phone_number', '1234567890')
            ->set('data.employee.joined_date', '2023-01-01')
            ->set('data.employee.position', 'Developer')
            ->set('data.employee.basic_salary', '50000')
            ->call('create')
            ->assertHasNoErrors();


        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        $this->assertDatabaseHas('employees', [
            'address' => '123 Test Street',
            'phone_number' => '1234567890',
            'position' => 'Developer',
        ]);


        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $this->adminRole->id,
            'model_type' => User::class,
        ]);
    }


    public function test_non_admin_cannot_create_user()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);


        $this->actingAs($employee);


        $response = $this->get('/admin/users/create');
        $response->assertStatus(403);
    }


    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);


        $this->actingAs($admin);


        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);


        // Create an employee record for the user
        $user->employee()->create([
            'address' => 'Old Address',
            'gender' => 'male',
            'date_of_birth' => '1980-01-01',
            'phone_number' => '9876543210',
            'joined_date' => '2022-01-01',
            'position' => 'Old Position',
            'basic_salary' => '40000',
        ]);


        Livewire::test(EditUser::class, ['record' => $user->getKey()])
            ->set('data.name', 'Updated Name')
            ->set('data.email', 'updated@example.com')
            ->set('data.employee.address', '456 New Street')
            ->set('data.employee.gender', 'female')
            ->set('data.employee.date_of_birth', '1990-01-01')
            ->set('data.employee.phone_number', '0987654321')
            ->set('data.employee.joined_date', '2023-01-01')
            ->set('data.employee.position', 'Senior Developer')
            ->set('data.employee.basic_salary', '60000')
            ->call('save')
            ->assertHasNoErrors();


        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);


        $this->assertDatabaseHas('employees', [
            'employee_id' => $user->id,
            'address' => '456 New Street',
            'phone_number' => '0987654321',
            'position' => 'Senior Developer',
            'basic_salary' => '60000',
        ]);
    }


    public function test_non_admin_cannot_update_user()
    {
        $employee = User::factory()->create();
        $employee->assignRole($this->employeeRole);


        $this->actingAs($employee);


        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        $response = $this->get("/admin/users/{$user->id}/edit");
        $response->assertStatus(403);
    }


    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->adminRole);


        $this->actingAs($admin);


        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);


        $this->assertDatabaseHas('users', ['id' => $user->id]);


        Livewire::test(ListUsers::class)
            ->callTableAction('delete', $user)
            ->assertHasNoErrors();


        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }


    public function test_non_admin_cannot_delete_user()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');


        // $this->actingAs($employee);
        $this->assertFalse($employee->can('delete users'));



        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // $this->assertDatabaseHas('users', ['id' => $user->id]);


        // Livewire::test(ListUsers::class)
        //     ->callTableAction('delete', $user)
        //     ->assertStatus(403);


        // $this->assertDatabaseHas('users', ['id' => $user->id]);

        Livewire::actingAs($employee);
        try {
            Livewire::test(ListUsers::class)
                ->callTableAction('delete', $user);
        } catch (\Throwable $e) {

            $this->assertDatabaseHas('users', ['id' => $user->id]);
            return;
        }

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
