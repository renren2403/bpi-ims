<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Bed2PlanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function createUserWithRole(string $role, array $attributes = []): User
    {
        /** @var User $user */
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login_when_accessing_bed2_plans(): void
    {
        $response = $this->get(route('bed2-plans.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_encoder_can_view_bed2_plans_matrix_scoped_to_their_operating_unit(): void
    {
        $unitA = OperatingUnit::factory()->create(['name' => 'Crop Pest Division', 'code' => 'CPMD']);
        $unitB = OperatingUnit::factory()->create(['name' => 'Seed Quality Division', 'code' => 'NSQCS']);

        $encoderA = $this->createUserWithRole('encoder', ['operating_unit_id' => $unitA->id]);

        $planA = Bed2Plan::factory()->create([
            'operating_unit_id' => $unitA->id,
            'year' => 2026,
            'annual_target' => 500,
        ]);
        $planB = Bed2Plan::factory()->create([
            'operating_unit_id' => $unitB->id,
            'year' => 2026,
            'annual_target' => 999,
        ]);

        $response = $this->actingAs($encoderA)->get(route('bed2-plans.index', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee('Crop Pest Division');
        $response->assertSee('500');
        $response->assertDontSee('Seed Quality Division');
    }

    public function test_admin_has_bureau_wide_visibility_in_matrix(): void
    {
        $unitA = OperatingUnit::factory()->create(['name' => 'Crop Pest Division']);
        $unitB = OperatingUnit::factory()->create(['name' => 'Seed Quality Division']);

        $admin = $this->createUserWithRole('admin');

        Bed2Plan::factory()->create(['operating_unit_id' => $unitA->id, 'year' => 2026]);
        Bed2Plan::factory()->create(['operating_unit_id' => $unitB->id, 'year' => 2026]);

        $response = $this->actingAs($admin)->get(route('bed2-plans.index', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee('Crop Pest Division');
        $response->assertSee('Seed Quality Division');
    }

    public function test_encoder_can_create_draft_target_plan_with_quarterly_calculation(): void
    {
        $unit = OperatingUnit::factory()->create();
        $pap = PAP::factory()->create();
        $indicator = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.store'), [
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2026,
            'annual_target' => 120,
            'm1_target' => 10,
            'm2_target' => 10,
            'm3_target' => 10,
            'm4_target' => 10,
            'm5_target' => 10,
            'm6_target' => 10,
            'm7_target' => 10,
            'm8_target' => 10,
            'm9_target' => 10,
            'm10_target' => 10,
            'm11_target' => 10,
            'm12_target' => 10,
            'remarks' => 'Baseline physical target.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2026,
            'annual_target' => 120,
            'q1_target' => 30,
            'q2_target' => 30,
            'q3_target' => 30,
            'q4_target' => 30,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'bed2.created',
        ]);
    }

    public function test_unique_constraint_prevents_duplicate_plan_for_same_ou_indicator_and_year(): void
    {
        $unit = OperatingUnit::factory()->create();
        $pap = PAP::factory()->create();
        $indicator = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);

        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2026,
        ]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.store'), [
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2026,
            'annual_target' => 100,
        ]);

        $response->assertSessionHasErrors('performance_indicator_id');
    }

    public function test_encoder_can_submit_draft_plan_to_division_chief(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'status' => 'draft',
        ]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.submit-head', $plan));

        $response->assertRedirect();
        $plan->refresh();

        $this->assertSame('submitted_to_head', $plan->status);
        $this->assertSame($encoder->id, $plan->submitted_by);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.submitted_to_head']);
    }

    public function test_division_chief_can_endorse_submitted_plan_to_pmo(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->submittedToHead()->create([
            'operating_unit_id' => $unit->id,
        ]);

        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($head)->post(route('bed2-plans.head-approve', $plan));

        $response->assertRedirect();
        $plan->refresh();

        $this->assertSame('head_approved', $plan->status);
        $this->assertSame($head->id, $plan->head_approved_by);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.head_approved']);
    }

    public function test_division_chief_can_return_plan_with_remarks(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->submittedToHead()->create([
            'operating_unit_id' => $unit->id,
        ]);

        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($head)->post(route('bed2-plans.return', $plan), [
            'remarks' => 'Please adjust Q1 allocation due to procurement schedule.',
        ]);

        $response->assertRedirect();
        $plan->refresh();

        $this->assertSame('returned', $plan->status);
        $this->assertStringContainsString('procurement schedule', $plan->remarks);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.returned']);
    }

    public function test_pmo_admin_can_approve_and_lock_baseline(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->headApproved()->create([
            'operating_unit_id' => $unit->id,
        ]);

        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->post(route('bed2-plans.pmo-approve', $plan));

        $response->assertRedirect();
        $plan->refresh();

        $this->assertSame('approved', $plan->status);
        $this->assertSame($admin->id, $plan->pmo_approved_by);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.approved']);
    }

    public function test_encoder_cannot_edit_approved_plan(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->approved()->create([
            'operating_unit_id' => $unit->id,
            'annual_target' => 500,
        ]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->get(route('bed2-plans.edit', $plan));
        $response->assertForbidden();

        $updateResponse = $this->actingAs($encoder)->put(route('bed2-plans.update', $plan), [
            'annual_target' => 999,
        ]);
        $updateResponse->assertForbidden();

        $plan->refresh();
        $this->assertEquals(500, $plan->annual_target);
    }

    public function test_encoder_cannot_access_or_modify_another_operating_units_plan(): void
    {
        $unitA = OperatingUnit::factory()->create();
        $unitB = OperatingUnit::factory()->create();

        $planB = Bed2Plan::factory()->create(['operating_unit_id' => $unitB->id]);

        $encoderA = $this->createUserWithRole('encoder', ['operating_unit_id' => $unitA->id]);

        $this->actingAs($encoderA)->get(route('bed2-plans.show', $planB))->assertForbidden();
        $this->actingAs($encoderA)->get(route('bed2-plans.edit', $planB))->assertForbidden();
    }
}
