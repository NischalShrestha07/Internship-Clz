<?php

namespace Database\Factories;

use App\Models\LeaveConfiguration;
use App\Models\LeaveGroup;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveConfiguration>
 */
class LeaveConfigurationFactory extends Factory
{
    protected $model = LeaveConfiguration::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'leave_group_id' => LeaveGroup::factory(),
            'leave_type_id' => LeaveType::factory(),
            'days' => $this->faker->numberBetween(1, 10),
        ];
    }
}
