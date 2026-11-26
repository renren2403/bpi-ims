<?php

namespace Tests\Feature;

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

    public function test_unbalanced_plan_cannot_be_submitted_to_division_chief(): void
    {
        $unit = OperatingUnit::factory()->create();
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $plan = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'annual_target' => 100,
            'm1_target' => 10,
            'm2_target' => 10,
            'm3_target' => 10,
            'm4_target' => 10,
            'm5_target' => 10,
            'm6_target' => 10,
            'm7_target' => 0,
            'm8_target' => 0,
            'm9_target' => 0,
            'm10_target' => 0,
            'm11_target' => 0,
            'm12_target' => 0,
            'status' => 'draft',
        ]);

        $this->assertFalse($plan->isBalanced());

        $response = $this->actingAs($encoder)->post(route('bed2-plans.submit-head', $plan));

        $response->assertSessionHas('error');
        $plan->refresh();
        $this->assertSame('draft', $plan->status);
    }

    public function test_balanced_plan_can_be_submitted_to_division_chief(): void
    {
        $unit = OperatingUnit::factory()->create();
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $plan = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
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
            'status' => 'draft',
        ]);

        $this->assertTrue($plan->isBalanced());

        $response = $this->actingAs($encoder)->post(route('bed2-plans.submit-head', $plan));

        $response->assertSessionHas('status');
        $plan->refresh();
        $this->assertSame('submitted_to_head', $plan->status);
    }

    public function test_batch_submission_blocks_when_any_plan_is_unbalanced(): void
    {
        $unit = OperatingUnit::factory()->create();
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $planBalanced = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'year' => 2026,
            'annual_target' => 120,
            'm1_target' => 10, 'm2_target' => 10, 'm3_target' => 10,
            'm4_target' => 10, 'm5_target' => 10, 'm6_target' => 10,
            'm7_target' => 10, 'm8_target' => 10, 'm9_target' => 10,
            'm10_target' => 10, 'm11_target' => 10, 'm12_target' => 10,
            'status' => 'draft',
        ]);

        $planUnbalanced = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'year' => 2026,
            'annual_target' => 200,
            'm1_target' => 10, 'm2_target' => 10, 'm3_target' => 10,
            'm4_target' => 0, 'm5_target' => 0, 'm6_target' => 0,
            'm7_target' => 0, 'm8_target' => 0, 'm9_target' => 0,
            'm10_target' => 0, 'm11_target' => 0, 'm12_target' => 0,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.batch-submit-head'), [
            'year' => 2026,
            'operating_unit_id' => $unit->id,
        ]);

        $response->assertSessionHas('error');
        $planBalanced->refresh();
        $planUnbalanced->refresh();
        $this->assertSame('draft', $planBalanced->status);
        $this->assertSame('draft', $planUnbalanced->status);
    }

    public function test_division_chief_cannot_endorse_unbalanced_plan(): void
    {
        $unit = OperatingUnit::factory()->create();
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);

        $plan = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'annual_target' => 300,
            'm1_target' => 10,
            'm2_target' => 0,
            'm3_target' => 0,
            'm4_target' => 0,
            'm5_target' => 0,
            'm6_target' => 0,
            'm7_target' => 0,
            'm8_target' => 0,
            'm9_target' => 0,
            'm10_target' => 0,
            'm11_target' => 0,
            'm12_target' => 0,
            'status' => 'submitted_to_head',
        ]);

        $response = $this->actingAs($head)->post(route('bed2-plans.head-approve', $plan));

        $response->assertSessionHas('error');
        $plan->refresh();
        $this->assertSame('submitted_to_head', $plan->status);
    }

    public function test_encoder_can_rollover_and_initialize_start_of_year_target_plans(): void
    {
        $unit = OperatingUnit::factory()->create();
        $pap = PAP::factory()->create();
        $indicator = PerformanceIndicator::factory()->create([
            'pap_id' => $pap->id,
            'is_active' => true,
        ]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.rollover'), [
            'year' => 2027,
            'operating_unit_id' => $unit->id,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2027,
            'status' => 'draft',
            'annual_target' => 0,
        ]);
    }

    public function test_encoder_can_batch_create_multiple_target_plans_at_once(): void
    {
        $unit = OperatingUnit::factory()->create();
        $pap = PAP::factory()->create();
        $ind1 = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);
        $ind2 = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.store'), [
            'year' => 2026,
            'plans' => [
                [
                    'pap_id' => $pap->id,
                    'performance_indicator_id' => $ind1->id,
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
                ],
                [
                    'pap_id' => $pap->id,
                    'performance_indicator_id' => $ind2->id,
                    'annual_target' => 40,
                    'm1_target' => 0,
                    'm2_target' => 0,
                    'm3_target' => 10,
                    'm4_target' => 0,
                    'm5_target' => 0,
                    'm6_target' => 10,
                    'm7_target' => 0,
                    'm8_target' => 0,
                    'm9_target' => 10,
                    'm10_target' => 0,
                    'm11_target' => 0,
                    'm12_target' => 10,
                ],
            ],
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'performance_indicator_id' => $ind1->id,
            'year' => 2026,
            'annual_target' => 120,
            'q1_target' => 30,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'performance_indicator_id' => $ind2->id,
            'year' => 2026,
            'annual_target' => 40,
            'q1_target' => 10,
            'status' => 'draft',
        ]);
    }

    public function test_encoder_can_encode_plans_with_hierarchy_fields_and_inline_indicators(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'National Plant Quarantine Services Division', 'code' => 'NPQSD']);
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $pap = PAP::factory()->create([
            'code' => 'AFRSP',
            'name' => 'Agriculture and Fishery Regulatory Support Program',
            'category' => 'III. OPERATIONS',
        ]);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.store'), [
            'year' => 2026,
            'plans' => [
                [
                    'pap_id' => $pap->id,
                    'sub_program' => 'Quality control and inspection and issuance of regulatory documents',
                    'activity' => 'Issuance of Biosafety Permit',
                    'sub_activity' => '1. Receive and conduct of initial technical assessment of the application received prior to endorsement to Joint Assessment Group',
                    'indicator_name' => '6 GM applications assessed for sufficiency in substance and endorsement of communication letter',
                    'unit_of_measure' => 'Applications',
                    'annual_target' => 6,
                    'm1_target' => 0,
                    'm2_target' => 0,
                    'm3_target' => 0,
                    'm4_target' => 0,
                    'm5_target' => 0,
                    'm6_target' => 1,
                    'm7_target' => 1,
                    'm8_target' => 1,
                    'm9_target' => 1,
                    'm10_target' => 1,
                    'm11_target' => 1,
                    'm12_target' => 0,
                ],
            ],
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('performance_indicators', [
            'pap_id' => $pap->id,
            'sub_program' => 'Quality control and inspection and issuance of regulatory documents',
            'activity' => 'Issuance of Biosafety Permit',
            'sub_activity' => '1. Receive and conduct of initial technical assessment of the application received prior to endorsement to Joint Assessment Group',
            'name' => '6 GM applications assessed for sufficiency in substance and endorsement of communication letter',
            'unit_of_measure' => 'Applications',
        ]);

        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'year' => 2026,
            'annual_target' => 6,
            'q1_target' => 0,
            'q2_target' => 1,
            'q3_target' => 3,
            'q4_target' => 2,
            'status' => 'draft',
        ]);
    }

    public function test_encoder_can_delete_draft_plan_via_ajax(): void
    {
        $unit = OperatingUnit::factory()->create();
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);
        $plan = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($encoder)->deleteJson(route('bed2-plans.destroy', $plan));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('bed2_plans', ['id' => $plan->id]);
    }

    public function test_encoder_can_batch_delete_draft_plans(): void
    {
        $unit = OperatingUnit::factory()->create();
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);
        $plan1 = Bed2Plan::factory()->create(['operating_unit_id' => $unit->id, 'status' => 'draft']);
        $plan2 = Bed2Plan::factory()->create(['operating_unit_id' => $unit->id, 'status' => 'draft']);

        $response = $this->actingAs($encoder)->post(route('bed2-plans.batch-destroy'), [
            'plan_ids' => [$plan1->id, $plan2->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('bed2_plans', ['id' => $plan1->id]);
        $this->assertDatabaseMissing('bed2_plans', ['id' => $plan2->id]);
    }

    public function test_encoder_can_encode_plan_from_clean_slate_with_free_text_program_name(): void
    {
        $unit = OperatingUnit::factory()->create();
        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $postData = [
            'year' => (int) date('Y'),
            'plans' => [
                [
                    'program_name' => 'Custom Seed Certification Program',
                    'activity' => 'Field Inspection and Seed Testing',
                    'sub_program' => 'Quality Assurance',
                    'sub_activity' => 'Purity and germination lab testing',
                    'indicator_name' => 'Number of seed lots tested and certified',
                    'unit_of_measure' => 'seed lots',
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
                ],
            ],
        ];

        $response = $this->actingAs($encoder)->post(route('bed2-plans.store'), $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('paps', [
            'name' => 'Custom Seed Certification Program',
        ]);
        $this->assertDatabaseHas('performance_indicators', [
            'name' => 'Number of seed lots tested and certified',
            'activity' => 'Field Inspection and Seed Testing',
            'sub_program' => 'Quality Assurance',
            'sub_activity' => 'Purity and germination lab testing',
        ]);
        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'annual_target' => 120,
            'q1_target' => 30,
            'q2_target' => 30,
            'q3_target' => 30,
            'q4_target' => 30,
            'status' => 'draft',
        ]);
    }
}
