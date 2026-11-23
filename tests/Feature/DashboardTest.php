<?php

namespace Tests\Feature;

use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use App\Models\ReportingPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $evaluatorUser;
    protected User $headUser;
    protected User $encoderUser;
    protected User $executiveUser;
    protected OperatingUnit $cpmd;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        foreach (['superadmin', 'admin', 'evaluator', 'ou-head', 'encoder', 'higher-up'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->cpmd = OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Arthur Conan Doyle',
            'email' => 'admin@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
        $this->adminUser->assignRole('admin');

        $this->evaluatorUser = User::factory()->create([
            'name' => 'Franz Kafka',
            'email' => 'evaluator@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
        $this->evaluatorUser->assignRole('evaluator');

        $this->headUser = User::factory()->create([
            'name' => 'George Eliot',
            'email' => 'chief@bpi.gov.ph',
            'operating_unit_id' => $this->cpmd->id,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
        $this->headUser->assignRole('ou-head');

        $this->encoderUser = User::factory()->create([
            'name' => 'Jose Dalisay',
            'email' => 'encoder@bpi.gov.ph',
            'operating_unit_id' => $this->cpmd->id,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
        $this->encoderUser->assignRole('encoder');

        $this->executiveUser = User::factory()->create([
            'name' => 'Charles Dickens',
            'email' => 'director@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
        $this->executiveUser->assignRole('higher-up');

        ReportingPeriod::create([
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
            'name' => 'Current Fiscal Cycle',
            'window_start' => now()->startOfMonth(),
            'window_end' => now()->endOfMonth(),
            'is_active' => true,
            'status' => 'open',
        ]);
    }

    public function test_guest_is_redirected_from_all_dashboards(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('evaluator.dashboard'))->assertRedirect(route('login'));
        $this->get(route('head.dashboard'))->assertRedirect(route('login'));
        $this->get(route('encoder.dashboard'))->assertRedirect(route('login'));
        $this->get(route('executive.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_renders_live_metrics_for_admin(): void
    {
        OperatingUnit::create([
            'name' => 'Planning and Management Office',
            'code' => 'PMO',
            'type' => 'Office',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Executive Performance Command Center');
        $response->assertSee('Current Fiscal Cycle');
        $response->assertSee('CPMD');
        // PMO should be excluded from reporting operating units trajectory
        $response->assertDontSee('Planning and Management Office');
    }

    public function test_evaluator_dashboard_renders_queue_for_evaluator(): void
    {
        $pap = PAP::create(['code' => 'A.01.a', 'name' => 'Pest Detection', 'is_active' => true]);
        $indicator = PerformanceIndicator::create([
            'pap_id' => $pap->id,
            'name' => 'Hectares surveyed',
            'unit_of_measure' => 'hectares',
            'target_type' => 'cumulative',
            'is_active' => true,
        ]);

        Bed2Plan::create([
            'operating_unit_id' => $this->cpmd->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => (int) date('Y'),
            'annual_target' => 500,
            'status' => 'head_approved',
            'head_approved_by' => $this->headUser->id,
        ]);

        $response = $this->actingAs($this->evaluatorUser)->get(route('evaluator.dashboard'));

        $response->assertOk();
        $response->assertSee('Performance Review');
        $response->assertSee('Hectares surveyed');
        $response->assertSee('500.00');
    }

    public function test_head_dashboard_renders_scoped_data_for_division_chief(): void
    {
        $pap = PAP::create(['code' => 'A.01.a', 'name' => 'Pest Detection', 'is_active' => true]);
        $indicator = PerformanceIndicator::create([
            'pap_id' => $pap->id,
            'name' => 'Samples processed',
            'unit_of_measure' => 'samples',
            'target_type' => 'cumulative',
            'is_active' => true,
        ]);

        Bed2Plan::create([
            'operating_unit_id' => $this->cpmd->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => (int) date('Y'),
            'annual_target' => 250,
            'status' => 'submitted_to_head',
            'submitted_by' => $this->encoderUser->id,
        ]);

        $response = $this->actingAs($this->headUser)->get(route('head.dashboard'));

        $response->assertOk();
        $response->assertSee('Division Chief Command Desk');
        $response->assertSee('Crop Pest Management Division');
        $response->assertSee('Samples processed');
        $response->assertSee('250.00');
        $response->assertSee('Jose Dalisay'); // Division staff encoder
    }

    public function test_encoder_dashboard_renders_scoped_workspace_for_encoder(): void
    {
        $pap = PAP::create(['code' => 'A.01.a', 'name' => 'Pest Detection', 'is_active' => true]);
        $indicator = PerformanceIndicator::create([
            'pap_id' => $pap->id,
            'name' => 'Laboratories inspected',
            'unit_of_measure' => 'laboratories',
            'target_type' => 'cumulative',
            'is_active' => true,
        ]);

        Bed2Plan::create([
            'operating_unit_id' => $this->cpmd->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => (int) date('Y'),
            'annual_target' => 75,
            'status' => 'draft',
            'submitted_by' => $this->encoderUser->id,
        ]);

        $response = $this->actingAs($this->encoderUser)->get(route('encoder.dashboard'));

        $response->assertOk();
        $response->assertSee('OU Accomplishment Encoding Desk');
        $response->assertSee('Crop Pest Management Division');
        $response->assertSee('Laboratories inspected');
        $response->assertSee('75.00');
        $response->assertSee('Submit to Head');
    }

    public function test_executive_dashboard_renders_strategic_overview_for_director(): void
    {
        $response = $this->actingAs($this->executiveUser)->get(route('executive.dashboard'));

        $response->assertOk();
        $response->assertSee('Executive Leadership Briefing');
        $response->assertSee('Bureau Physical Performance Summary');
        $response->assertSee('Crop Pest Management Division');
    }

    public function test_central_dashboard_route_redirects_user_to_appropriate_role_view(): void
    {
        $this->actingAs($this->adminUser)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->evaluatorUser)->get(route('dashboard'))->assertRedirect(route('evaluator.dashboard'));
        $this->actingAs($this->headUser)->get(route('dashboard'))->assertRedirect(route('head.dashboard'));
        $this->actingAs($this->encoderUser)->get(route('dashboard'))->assertRedirect(route('encoder.dashboard'));
        $this->actingAs($this->executiveUser)->get(route('dashboard'))->assertRedirect(route('executive.dashboard'));
    }

    public function test_role_boundaries_are_strictly_enforced_between_dashboards(): void
    {
        // Encoder cannot access admin or evaluator dashboard
        $this->actingAs($this->encoderUser)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->encoderUser)->get(route('evaluator.dashboard'))->assertForbidden();
        $this->actingAs($this->encoderUser)->get(route('head.dashboard'))->assertForbidden();
        $this->actingAs($this->encoderUser)->get(route('executive.dashboard'))->assertForbidden();

        // Division Chief cannot access admin dashboard
        $this->actingAs($this->headUser)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->headUser)->get(route('encoder.dashboard'))->assertForbidden();
    }

    public function test_sidebar_dynamically_hides_unauthorized_governance_links_for_evaluator(): void
    {
        $response = $this->actingAs($this->evaluatorUser)->get(route('evaluator.dashboard'));

        $response->assertOk();
        // Evaluator CANNOT see Roles & Permissions or User Management on the sidebar
        $response->assertDontSee(route('admin.roles-permissions.index'), false);
        $response->assertDontSee(route('admin.users.index'), false);

        // Evaluator CAN see Operating Units, Reporting Cycle Rules, and Audit Logs
        $response->assertSee(route('admin.operating-units.index'), false);
        $response->assertSee(route('admin.reporting-periods.index'), false);
        $response->assertSee(route('admin.audit-logs.index'), false);
    }

    public function test_sidebar_renders_all_governance_links_for_admin(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(route('admin.roles-permissions.index'), false);
        $response->assertSee(route('admin.users.index'), false);
        $response->assertSee(route('admin.operating-units.index'), false);
        $response->assertSee(route('admin.paps.index'), false);
        $response->assertSee(route('admin.reporting-periods.index'), false);
        $response->assertSee(route('admin.audit-logs.index'), false);
    }

    public function test_encoder_sidebar_hides_entire_pmo_governance_section(): void
    {
        $response = $this->actingAs($this->encoderUser)->get(route('encoder.dashboard'));

        $response->assertOk();
        $response->assertDontSee('PMO Governance');
        $response->assertDontSee(route('admin.roles-permissions.index'), false);
        $response->assertDontSee(route('admin.users.index'), false);
        $response->assertDontSee(route('admin.operating-units.index'), false);
    }

    public function test_evaluator_dynamically_sees_roles_permission_button_when_granted_permission(): void
    {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'roles.manage', 'guard_name' => 'web']);
        $this->evaluatorUser->givePermissionTo('roles.manage');

        $response = $this->actingAs($this->evaluatorUser)->get(route('evaluator.dashboard'));

        $response->assertOk();
        // Now that the permission is granted, it dynamically shows on the sidebar!
        $response->assertSee(route('admin.roles-permissions.index'), false);
    }
}
