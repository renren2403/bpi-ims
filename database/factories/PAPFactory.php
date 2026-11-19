<?php

namespace Database\Factories;

use App\Models\PAP;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PAP>
 */
class PAPFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'PAP-'.fake()->unique()->numberBetween(100, 999),
            'name' => fake()->unique()->words(4, true),
            'description' => fake()->sentence(),
            'category' => 'Operations',
            'is_active' => true,
        ];
    }
}
