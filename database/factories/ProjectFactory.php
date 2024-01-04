<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       

        return [
            'user_id' => null,
            'program_id' => null,
            'title' => fake()->sentence,
            'implementing_agency' => fake()->company,
            'monitoring_agency' => fake()->company,
            'project_leader' => fake()->name,
            'allocated_fund' => fake()->randomFloat(2, 1000, 10000),
            'total_usage' => fake()->randomFloat(2, 0, 5000),
            'start_date' => fake()->date,
            'end_date' => fake()->date,
            'status' => fake()->randomElement(['Not Active', 'Active', 'Completed']),
        ];
    }
}
