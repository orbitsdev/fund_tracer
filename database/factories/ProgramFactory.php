<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'program_leader' => fake()->name(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'total_budget' => fake()->randomFloat(2, 1000, 10000),
            'total_usage' => fake()->randomFloat(2, 0, 5000),
            'status' => fake()->randomElement(['Pending', 'Approved', 'Completed']),
    
        ];
    }
}
