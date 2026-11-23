<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Bed2WorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    private function createUserWithRole(string $role, array $attributes = []): User
    {
        /** @var User $user */
        $user = User::factory()->create(array_merge([
            'approval_status' => 'approved',
            'is_active' => true,
        ], $attributes));
        $user->assignRole($role);

        return $user;
    }

    public function test_bottom_up_workflow_from_encoder_to_head_to_pmo_approval(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'National Seed Quality Division', 'code' => 'NSQCS']);
        $pap = PAP::factory()->create(['code' => 'PAP-SEED', 'name' => 'Seed Certification Project']);
        $ind = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'name' => 'Tested Samples']);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);
        $pmoAdmin = $this->createUserWithRole('admin');

        // Step 1: OU Encoder inputs BED-2 targets for their unit
        $createResponse = $this->actingAs($encoder)->post(route('bed2-plans.store'), [
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind->id,
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
        ]);

        $createResponse->assertRedirect();
        $plan = Bed2Plan::where('operating_unit_id', $unit->id)->where('year', 2026)->firstOrFail();
        $this->assertEquals('draft', $plan->status);
        $this->assertEquals(120, $plan->annual_target);

        // Step 2: Encoder batch submits all draft plans for FY 2026 to Division Chief
        $submitResponse = $this->actingAs($encoder)->post(route('bed2-plans.batch-submit-head'), [
            'year' => 2026,
            'operating_unit_id' => $unit->id,
        ]);

        $submitResponse->assertRedirect();
        $this->assertEquals('submitted_to_head', $plan->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.batch_submitted_to_head']);

        // Step 3: Division Chief (OU Head) reviews and batch endorses to PMO
        $endorseResponse = $this->actingAs($head)->post(route('bed2-plans.batch-head-approve'), [
            'year' => 2026,
            'operating_unit_id' => $unit->id,
        ]);

        $endorseResponse->assertRedirect();
        $this->assertEquals('head_approved', $plan->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.batch_head_approved']);

        // Step 4: PMO approves and locks the annual baseline
        $approveResponse = $this->actingAs($pmoAdmin)->post(route('bed2-plans.batch-pmo-approve'), [
            'year' => 2026,
            'operating_unit_id' => $unit->id,
        ]);

        $approveResponse->assertRedirect();
        $this->assertEquals('approved', $plan->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.batch_pmo_approved']);
    }

    public function test_pmo_can_directly_input_paps_for_another_operating_unit(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'Crop Pest Division', 'code' => 'CPMD']);
        $pap = PAP::factory()->create(['code' => 'PAP-PEST']);
        $ind = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);

        $pmoAdmin = $this->createUserWithRole('admin');

        // PMO specifies operating_unit_id directly
        $response = $this->actingAs($pmoAdmin)->post(route('bed2-plans.store'), [
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind->id,
            'year' => 2026,
            'annual_target' => 500,
            'm1_target' => 50,
            'm2_target' => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bed2_plans', [
            'operating_unit_id' => $unit->id,
            'performance_indicator_id' => $ind->id,
            'year' => 2026,
            'annual_target' => 500,
        ]);
    }

    public function test_pmo_evaluator_can_edit_ou_encoders_submitted_targets_directly(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->submittedToHead()->create([
            'operating_unit_id' => $unit->id,
            'annual_target' => 100,
        ]);

        $evaluator = $this->createUserWithRole('evaluator');

        // Evaluator modifies target directly
        $response = $this->actingAs($evaluator)->put(route('bed2-plans.update', $plan), [
            'annual_target' => 150,
            'm1_target' => 20,
            'remarks' => 'Adjusted by PMO to match approved bureau budget allocation.',
        ]);

        $response->assertRedirect();
        $plan->refresh();
        $this->assertEquals(150, $plan->annual_target);
        $this->assertStringContainsString('Adjusted by PMO', $plan->remarks);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.updated']);
    }

    public function test_pmo_or_head_can_return_plans_for_revision_with_remarks(): void
    {
        $unit = OperatingUnit::factory()->create();
        $plan = Bed2Plan::factory()->submittedToHead()->create([
            'operating_unit_id' => $unit->id,
        ]);

        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($head)->post(route('bed2-plans.batch-return'), [
            'year' => $plan->year,
            'operating_unit_id' => $unit->id,
            'remarks' => 'Milestones in Q2 are too low. Please reallocate.',
        ]);

        $response->assertRedirect();
        $plan->refresh();
        $this->assertEquals('returned', $plan->status);
        $this->assertStringContainsString('Milestones in Q2 are too low', $plan->remarks);
        $this->assertDatabaseHas('audit_logs', ['action' => 'bed2.batch_returned']);
    }

    public function test_quick_pap_and_indicator_can_be_created_on_the_fly(): void
    {
        $encoder = $this->createUserWithRole('encoder');

        // 1. Quick add PAP
        $papResponse = $this->actingAs($encoder)->postJson(route('paps.quick-store'), [
            'code' => 'PAP-FLY-01',
            'name' => 'Newly Initiated Drone Surveillance PAP',
            'category' => 'Operations',
        ]);

        $papResponse->assertStatus(201);
        $papResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('paps', ['code' => 'PAP-FLY-01']);

        $papId = $papResponse->json('pap.id');

        // 2. Quick add Indicator under newly created PAP
        $indResponse = $this->actingAs($encoder)->postJson(route('indicators.quick-store'), [
            'pap_id' => $papId,
            'code' => 'IND-FLY-01',
            'name' => 'Hectares surveyed via aerial drones',
            'unit_of_measure' => 'Hectares',
            'type' => 'output',
        ]);

        $indResponse->assertStatus(201);
        $indResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('performance_indicators', ['code' => 'IND-FLY-01']);
    }

    public function test_user_can_view_official_printable_dbm_bed2_form(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'Crop Research Division', 'code' => 'CRD']);
        $pap = PAP::factory()->create(['code' => 'PAP-RESEARCH']);
        $ind = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'name' => 'Research Papers Published']);

        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind->id,
            'year' => 2026,
            'annual_target' => 25,
            'status' => 'approved',
        ]);

        $encoder = $this->createUserWithRole('encoder', ['operating_unit_id' => $unit->id]);

        $response = $this->actingAs($encoder)->get(route('bed2-plans.export.print', [
            'year' => 2026,
            'operating_unit_id' => $unit->id,
        ]));

        $response->assertOk();
        $response->assertSee('DBM BED FORM NO. 2');
        $response->assertSee('Crop Research Division');
        $response->assertSee('Research Papers Published');
        $response->assertSee('25.00');
    }

    public function test_user_can_download_bed2_physical_plan_csv(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'Crop Research Division', 'code' => 'CRD']);
        $pap = PAP::factory()->create(['code' => 'PAP-RESEARCH']);
        $ind = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'name' => 'Research Trials Conducted']);

        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind->id,
            'year' => 2026,
            'annual_target' => 40,
        ]);

        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get(route('bed2-plans.export.csv', [
            'year' => 2026,
            'operating_unit_id' => $unit->id,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('BED-2_Physical_Plan_CRD_2026.csv', $response->headers->get('Content-Disposition'));
    }
}
