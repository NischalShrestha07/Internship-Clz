<?php

namespace Database\Factories;

use App\Models\Leave_group;
use App\Models\LeaveGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave_group>
 */
class LeaveGroupFactory extends Factory
{

    protected $model = LeaveGroup::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),

        ];
    }
}
