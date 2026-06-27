<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\PunchLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'John Doe', 'email' => 'john.doe@example.com'],
            ['name' => 'Richard Miles', 'email' => 'richard.miles@example.com'],
            ['name' => 'John Smith', 'email' => 'john.smith@example.com'],
            ['name' => 'Mike Litorus', 'email' => 'mike.litorus@example.com'],
            ['name' => 'Wilmer Deluna', 'email' => 'wilmer.deluna@example.com'],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password'),
            ]);

            for ($day = 1; $day <= 31; $day++) {
                $date = '2025-07-' . sprintf('%02d', $day);
                $isWeekend = Carbon::parse($date)->isWeekend();
                if ($isWeekend) {
                    Holiday::firstOrCreate([
                        'date' => $date,
                        'title' => 'Weekend',
                        'description' => '5 Days'
                    ]);
                    Attendance::create([
                        'employee_id' => $user->id,
                        'type' => 'Regular',
                        'remarks' => 'Weekend',
                        'employee_name' => $user->name,
                        'date' => $date,
                        'status' => 'Absent',
                        'notes' => 'Automatically marked as holiday',
                    ]);
                    continue;
                }

                $status = rand(0, 2) ? 'Present' : (rand(0, 1) ? 'Absent' : 'Late');

                $attendance = Attendance::create([
                    'employee_id' => $user->id,
                    'type' => 'Regular',
                    'remarks' => $status === 'Present' ? 'On time' : ($status === 'Late' ? 'Delayed' : 'Not present'),
                    'employee_name' => $user->name,
                    'date' => $date,
                    'status' => $status,

                ]);

                if ($status !== 'Absent') {
                    $punch_times = [
                        ['in' => '09:00', 'out' => '12:00', 'notes' => 'Morning session'],
                        ['in' => '13:00', 'out' => '17:00', 'notes' => 'Afternoon session'],
                    ];
                    if ($status === 'Late') {
                        $punch_times[0]['in'] = '10:00';
                    }
                    foreach ($punch_times as $time) {
                        PunchLog::create([
                            'attendance_id' => $attendance->id,
                            'punch_in' => $time['in'],
                            'punch_out' => $time['out'],
                            'notes' => $time['notes'],
                        ]);
                    }
                }
            }
        }
    }
}
