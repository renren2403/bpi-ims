<?php

namespace Database\Factories;

use App\Models\PAP;
use App\Models\PerformanceIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PerformanceIndicator>
 */
class PerformanceIndicatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pap_id' => PAP::factory(),
            'code' => 'IND-'.fake()->unique()->numberBetween(100, 999),
            'name' => fake()->unique()->sentence(6),
            'unit_of_measure' => fake()->randomElement(['Samples', 'Reports', 'Sites', 'Sessions', 'Clients']),
            'type' => fake()->randomElement(['output', 'outcome']),
            'is_active' => true,
        ];
    }
}
