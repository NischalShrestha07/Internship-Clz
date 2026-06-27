<?php

namespace Database\Factories;

use App\Models\Leave;
use App\Models\Leave_Type;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave>
 */
class LeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Leave::class;
    public function definition(): array
    {
        return [
            'leave_type_id' => LeaveType::factory(),
            'employee_id' => User::factory(),
            'start_date_time' => now(),
            'end_date_time' => now()->addDays(1),
            'leave_reason' => 'Test leave',
            'status' => 'pending',
        ];
    }
}
