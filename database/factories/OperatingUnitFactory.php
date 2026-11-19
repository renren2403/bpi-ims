<?php

namespace Database\Factories;

use App\Models\OperatingUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OperatingUnit>
 */
class OperatingUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = strtoupper(fake()->unique()->lexify('???'));

        return [
            'name' => fake()->unique()->company().' Division',
            'code' => $code,
            'type' => 'Division',
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
