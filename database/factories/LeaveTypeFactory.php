<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'leave_type' => $this->faker->randomElement([
                'Sick Leave',
                'Casual Leave',
                'Maternity Leave',
                'Annual Leave',
                'Emergency Leave',
            ]),
            'default_days'  => $this->faker->numberBetween(1, 30),

        ];
    }
}
