<?php

namespace Tests\Feature;

use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use App\Models\ReportingPeriod;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeadReviewTest extends TestCase
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

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('head.reviews.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_review_console(): void
    {
        $encoder = $this->createUserWithRole('encoder');

        $response = $this->actingAs($encoder)->get(route('head.reviews.index'));

        $response->assertForbidden();
    }

    public function test_ou_head_can_view_for_review_page_scoped_to_their_unit(): void
    {
        $unitA = OperatingUnit::factory()->create(['name' => 'Crop Pest Management Division', 'code' => 'CPMD']);
        $unitB = OperatingUnit::factory()->create(['name' => 'Agricultural Fisheries Division', 'code' => 'AFD']);

        $headA = $this->createUserWithRole('ou-head', [
            'name' => 'George Eliot',
            'operating_unit_id' => $unitA->id,
        ]);

        $papA = PAP::factory()->create(['code' => 'PAP-CPMD', 'name' => 'CPMD Operations']);
        $papB = PAP::factory()->create(['code' => 'PAP-AFD', 'name' => 'AFD Operations']);

        $indA = PerformanceIndicator::factory()->create([
            'pap_id' => $papA->id,
            'name' => 'Crop Inspections Unique A',
            'unit_of_measure' => 'inspections',
        ]);
        $indB = PerformanceIndicator::factory()->create([
            'pap_id' => $papB->id,
            'name' => 'Fishery Surveys Unique B',
            'unit_of_measure' => 'surveys',
        ]);

        ReportingPeriod::create([
            'name' => 'January 2026 Cycle',
            'year' => 2026,
            'month' => 1,
            'window_start' => '2026-01-01 00:00:00',
            'window_end' => '2026-01-31 23:59:59',
            'is_active' => true,
            'status' => 'open',
        ]);

        // Plan for Unit A (pending review)
        Bed2Plan::factory()->create([
            'operating_unit_id' => $unitA->id,
            'pap_id' => $papA->id,
            'performance_indicator_id' => $indA->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
            'annual_target' => 500,
        ]);

        // Plan for Unit B (pending review)
        Bed2Plan::factory()->create([
            'operating_unit_id' => $unitB->id,
            'pap_id' => $papB->id,
            'performance_indicator_id' => $indB->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
            'annual_target' => 800,
        ]);

        $response = $this->actingAs($headA)->get(route('head.reviews.index'));

        $response->assertOk();
        $response->assertSee('Crop Pest Management Division');
        $response->assertSee('For Review');
        $response->assertSee('Crop Inspections Unique A');
        // Unit B plan indicator should not be visible to Head A
        $response->assertDontSee('Fishery Surveys Unique B');
    }

    public function test_ou_head_can_filter_by_status_tabs_and_pap(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'CPMD', 'code' => 'CPMD']);
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);

        $pap1 = PAP::factory()->create(['code' => 'PAP-1', 'name' => 'Project Alpha']);
        $pap2 = PAP::factory()->create(['code' => 'PAP-2', 'name' => 'Project Beta']);

        $ind1 = PerformanceIndicator::factory()->create([
            'pap_id' => $pap1->id,
            'name' => 'Indicator Alpha Submissions',
            'unit_of_measure' => 'reports',
        ]);
        $ind2 = PerformanceIndicator::factory()->create([
            'pap_id' => $pap2->id,
            'name' => 'Indicator Beta Approved',
            'unit_of_measure' => 'reports',
        ]);

        // Submitted to head plan
        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap1->id,
            'performance_indicator_id' => $ind1->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
        ]);

        // Endorsed plan
        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap2->id,
            'performance_indicator_id' => $ind2->id,
            'year' => 2026,
            'status' => 'head_approved',
        ]);

        // View endorsed tab: should see Beta, not Alpha indicator
        $response = $this->actingAs($head)->get(route('head.reviews.index', ['status' => 'head_approved', 'year' => 2026]));
        $response->assertOk();
        $response->assertSee('Indicator Beta Approved');
        $response->assertDontSee('Indicator Alpha Submissions');

        // View pending tab with PAP filter: should see Alpha indicator
        $responsePap = $this->actingAs($head)->get(route('head.reviews.index', [
            'status' => 'submitted_to_head',
            'pap_id' => $pap1->id,
            'year' => 2026,
        ]));
        $responsePap->assertOk();
        $responsePap->assertSee('Indicator Alpha Submissions');
        $responsePap->assertDontSee('Indicator Beta Approved');
    }

    public function test_ou_head_sees_for_review_sidebar_badge(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'CPMD', 'code' => 'CPMD']);
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);
        $pap = PAP::factory()->create();
        $ind = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'unit_of_measure' => 'reports']);

        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind->id,
            'year' => (int) date('Y'),
            'status' => 'submitted_to_head',
        ]);

        $response = $this->actingAs($head)->get(route('head.dashboard'));

        $response->assertOk();
        $response->assertSee('For Review');
        $response->assertSee(route('head.reviews.index'));
    }

    public function test_ou_head_can_batch_endorse_plans(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'CPMD', 'code' => 'CPMD']);
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);
        $pap = PAP::factory()->create();
        $ind1 = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'unit_of_measure' => 'reports']);
        $ind2 = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'unit_of_measure' => 'samples']);

        $plan1 = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind1->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
        ]);

        $plan2 = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind2->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
        ]);

        $response = $this->actingAs($head)->post(route('bed2-plans.batch-head-approve'), [
            'plan_ids' => [$plan1->id, $plan2->id],
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals('head_approved', $plan1->fresh()->status);
        $this->assertEquals('head_approved', $plan2->fresh()->status);
        $this->assertEquals($head->id, $plan1->fresh()->head_approved_by);
    }

    public function test_ou_head_can_batch_return_plans_with_remarks(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'CPMD', 'code' => 'CPMD']);
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);
        $pap = PAP::factory()->create();
        $ind1 = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'unit_of_measure' => 'reports']);
        $ind2 = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'unit_of_measure' => 'samples']);

        $plan1 = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind1->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
        ]);

        $plan2 = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind2->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
        ]);

        $response = $this->actingAs($head)->post(route('bed2-plans.batch-return'), [
            'plan_ids' => [$plan1->id, $plan2->id],
            'remarks' => 'Batch revision needed for annual physical targets.',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals('returned', $plan1->fresh()->status);
        $this->assertEquals('returned', $plan2->fresh()->status);
        $this->assertStringContainsString('Batch revision needed', $plan1->fresh()->remarks);
    }

    public function test_ou_head_can_return_plan_with_remarks(): void
    {
        $unit = OperatingUnit::factory()->create(['name' => 'CPMD', 'code' => 'CPMD']);
        $head = $this->createUserWithRole('ou-head', ['operating_unit_id' => $unit->id]);
        $pap = PAP::factory()->create();
        $ind = PerformanceIndicator::factory()->create(['pap_id' => $pap->id, 'unit_of_measure' => 'reports']);

        $plan = Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $ind->id,
            'year' => 2026,
            'status' => 'submitted_to_head',
        ]);

        $response = $this->actingAs($head)->post(route('bed2-plans.return', $plan), [
            'remarks' => 'Please revise Q2 quarterly target allocation.',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals('returned', $plan->fresh()->status);
        $this->assertStringContainsString('Please revise Q2 quarterly target allocation.', $plan->fresh()->remarks);
    }
}
