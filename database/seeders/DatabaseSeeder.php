<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakRecord;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveConfiguration;
use App\Models\LeaveGroup;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;


class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
        ]);
        $role1 = Role::create(['name' => 'Admin']);
        $user1->assignRole($role1);

        $user2 = User::factory()->create([
            'name' => 'Mahesh',
            'email' => 'mahesh@gmail.com',
            'password' => 'password',
        ]);
        $role2 = Role::create(['name' => 'Employee']);
        $user2->assignRole($role2);

        $user3 = User::factory()->create([
            'name' => 'Manish',
            'email' => 'manish@gmail.com',
            'password' => 'manish123',
        ]);
        $role3 = Role::create(['name' => 'HR Manager']);
        $user3->assignRole($role3);



        //leaveType seeder
        $leaveTypes = [
            ['leave_type' => 'Sick Leave', 'default_days' => 10],
            ['leave_type' => 'Casual Leave', 'default_days' => 7],
            ['leave_type' => 'Paid Leave', 'default_days' => 15],

        ];
        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }

        //leaveGroup seeder
        $leaveGroup = [
            ['name' => 'Frontend Developer'],
            ['name' => 'Backend Developer'],
            ['name' => 'Full Stack Developer'],
        ];
        foreach ($leaveGroup as $group) {
            LeaveGroup::create($group);
        }

        //Employee Seeder
        $faker = Faker::create();

        foreach (range(1, 3) as $i) {
            Employee::create([
                'employee_id'   => rand(1, 3),
                'address' => $faker->randomElement(['Kathmandu', 'Itahari']),
                'gender' => $faker->randomElement(['Male', 'Female']),
                'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                'phone_number' => $faker->phoneNumber,
                'joined_date' => $faker->date('Y-m-d', 'now'),
                'basic_salary' => $faker->numberBetween(20000, 60000),
                'position' => 'Backend Developer',
                'CV_file' => 'cv_frontend.pdf',
            ]);
        }


        ///Leave Seeder
        $faker = Faker::create();

        $leaveTypeIds = LeaveType::pluck('id')->toArray();

        foreach (range(1, 3) as $i) {
            $start = $faker->dateTimeBetween('-1 month', '+1 month');
            $end = (clone $start)->modify('+' . rand(1, 5) . ' days');
            $reasons = [
                'Medical appointment',
                'Family emergency',
                'Personal leave',
                'Vacation',
                'Official work',
            ];


            Leave::create([
                'leave_type_id'     => $faker->randomElement($leaveTypeIds),
                'leave_reason' => $faker->randomElement($reasons),
                'status'            => $faker->randomElement(['pending', 'approved', 'declined', 'cancelled']),
                'remarks'           => '',
                'employee_id'       => rand(2, 3),
                'start_date_time'   => $start->format('Y-m-d H:i:s'),
                'end_date_time'     => $end->format('Y-m-d H:i:s'),
            ]);
        }

        //leaveConfiguration seeder
        $faker = Faker::create();

        $leaveTypeIds = LeaveType::pluck('id')->toArray();
        $leaveGroupIds = LeaveGroup::pluck('id')->toArray(); // ensure LeaveGroup model exists

        foreach ($leaveGroupIds as $groupId) {
            foreach ($leaveTypeIds as $typeId) {
                LeaveConfiguration::create([
                    'leave_type_id' => $typeId,
                    'leave_group_id' => $groupId,
                    'days' => $faker->numberBetween(1, 10),
                ]);
            }
        }
    }
}
