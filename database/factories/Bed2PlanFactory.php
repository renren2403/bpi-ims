<?php

namespace Database\Factories;

use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bed2Plan>
 */
class Bed2PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $monthly = 10.00;
        $quarterly = 30.00;
        $annual = 120.00;

        return [
            'operating_unit_id' => OperatingUnit::factory(),
            'pap_id' => PAP::factory(),
            'performance_indicator_id' => PerformanceIndicator::factory(),
            'year' => (int) date('Y'),
            'annual_target' => $annual,
            'q1_target' => $quarterly,
            'q2_target' => $quarterly,
            'q3_target' => $quarterly,
            'q4_target' => $quarterly,
            'm1_target' => $monthly,
            'm2_target' => $monthly,
            'm3_target' => $monthly,
            'm4_target' => $monthly,
            'm5_target' => $monthly,
            'm6_target' => $monthly,
            'm7_target' => $monthly,
            'm8_target' => $monthly,
            'm9_target' => $monthly,
            'm10_target' => $monthly,
            'm11_target' => $monthly,
            'm12_target' => $monthly,
            'status' => 'draft',
            'remarks' => null,
            'submitted_by' => null,
            'head_approved_by' => null,
            'pmo_approved_by' => null,
        ];
    }

    public function submittedToHead(?User $submitter = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted_to_head',
            'submitted_by' => $submitter?->id ?? User::factory(),
        ]);
    }

    public function headApproved(?User $head = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'head_approved',
            'head_approved_by' => $head?->id ?? User::factory(),
        ]);
    }

    public function approved(?User $pmo = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'pmo_approved_by' => $pmo?->id ?? User::factory(),
        ]);
    }

    public function returned(string $remarks = 'Please check targets'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'returned',
            'remarks' => $remarks,
        ]);
    }
}
