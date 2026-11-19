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

class PAPManagementTest extends TestCase
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

    public function test_guest_cannot_access_paps_governance(): void
    {
        $response = $this->get(route('admin.paps.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_encoder_cannot_access_admin_paps_governance(): void
    {
        $encoder = $this->createUserWithRole('encoder');

        $response = $this->actingAs($encoder)->get(route('admin.paps.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_paps_directory(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create([
            'code' => 'PAP-01',
            'name' => 'National Plant Health Program',
            'category' => 'Operations',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.paps.index'));

        $response->assertOk();
        $response->assertSee('PAP-01');
        $response->assertSee('National Plant Health Program');
    }

    public function test_admin_can_filter_paps_by_search_category_and_status(): void
    {
        $admin = $this->createUserWithRole('admin');

        $match = PAP::factory()->create([
            'code' => 'TARGET-01',
            'name' => 'Targeted Inspection Effort',
            'category' => 'Operations',
            'is_active' => true,
        ]);

        $nonMatch = PAP::factory()->create([
            'code' => 'OTHER-02',
            'name' => 'Other Administrative Project',
            'category' => 'General Administration and Support',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.paps.index', [
            'search' => 'Inspection',
            'category' => 'Operations',
            'is_active' => '1',
        ]));

        $response->assertOk();
        $response->assertSee('TARGET-01');
        $response->assertDontSee('OTHER-02');
    }

    public function test_admin_can_view_create_pap_page(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.paps.create'));

        $response->assertOk();
        $response->assertSee('Register Program / PAP');
    }

    public function test_admin_can_create_a_pap(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.paps.store'), [
            'code' => 'PAP-09',
            'name' => 'Bureau Crop Protection & Pest Management',
            'category' => 'Operations',
            'description' => 'Mandated surveillance and border inspections.',
            'is_active' => '1',
        ]);

        $pap = PAP::where('code', 'PAP-09')->first();
        $this->assertNotNull($pap);
        $this->assertEquals('Bureau Crop Protection & Pest Management', $pap->name);
        $this->assertEquals('Operations', $pap->category);
        $this->assertTrue($pap->is_active);

        $response->assertRedirect(route('admin.paps.edit', $pap));
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'pap.created',
            'auditable_type' => PAP::class,
            'auditable_id' => $pap->id,
        ]);
    }

    public function test_admin_can_view_edit_pap_page_with_indicators(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create(['code' => 'PAP-10', 'name' => 'Seed Certification']);
        $indicator = PerformanceIndicator::factory()->create([
            'pap_id' => $pap->id,
            'code' => 'PI-01',
            'name' => 'Percentage of seed samples tested within standard turnaround',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.paps.edit', $pap));

        $response->assertOk();
        $response->assertSee('PAP-10');
        $response->assertSee('Seed Certification');
        $response->assertSee('PI-01');
        $response->assertSee('Percentage of seed samples tested within standard turnaround');
    }

    public function test_admin_can_update_a_pap(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create(['code' => 'PAP-11', 'name' => 'Initial Title']);

        $response = $this->actingAs($admin)->put(route('admin.paps.update', $pap), [
            'code' => 'PAP-11-REV',
            'name' => 'Updated Program Title',
            'category' => 'Support to Operations',
            'description' => 'Updated description.',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('status');
        $pap->refresh();
        $this->assertEquals('PAP-11-REV', $pap->code);
        $this->assertEquals('Updated Program Title', $pap->name);
        $this->assertEquals('Support to Operations', $pap->category);
    }

    public function test_admin_can_toggle_pap_active_status(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patch(route('admin.paps.toggle-status', $pap));

        $response->assertSessionHas('status');
        $pap->refresh();
        $this->assertFalse($pap->is_active);

        // Toggle back
        $this->actingAs($admin)->patch(route('admin.paps.toggle-status', $pap));
        $pap->refresh();
        $this->assertTrue($pap->is_active);
    }

    public function test_admin_can_delete_pap_without_plans(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create(['code' => 'PAP-UNUSED']);

        $response = $this->actingAs($admin)->delete(route('admin.paps.destroy', $pap));

        $response->assertRedirect(route('admin.paps.index'));
        $this->assertDatabaseMissing('paps', ['code' => 'PAP-UNUSED']);
    }

    public function test_admin_cannot_delete_pap_with_associated_bed2_plans(): void
    {
        $admin = $this->createUserWithRole('admin');

        $unit = OperatingUnit::factory()->create();
        $pap = PAP::factory()->create(['code' => 'PAP-LOCKED']);
        $indicator = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);

        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2026,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.paps.destroy', $pap));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('paps', ['id' => $pap->id]);
    }

    public function test_admin_can_add_performance_indicator_to_pap(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create(['code' => 'PAP-IND']);

        $response = $this->actingAs($admin)->post(route('admin.paps.indicators.store', $pap), [
            'code' => 'PI-01',
            'name' => 'Number of phytosanitary certificates issued',
            'unit_of_measure' => 'certificates',
            'type' => 'output',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('performance_indicators', [
            'pap_id' => $pap->id,
            'code' => 'PI-01',
            'name' => 'Number of phytosanitary certificates issued',
            'unit_of_measure' => 'certificates',
            'type' => 'output',
        ]);
    }

    public function test_admin_can_update_performance_indicator(): void
    {
        $admin = $this->createUserWithRole('admin');

        $pap = PAP::factory()->create();
        $indicator = PerformanceIndicator::factory()->create([
            'pap_id' => $pap->id,
            'code' => 'PI-OLD',
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.indicators.update', $indicator), [
            'code' => 'PI-NEW',
            'name' => 'New Name for Indicator',
            'unit_of_measure' => 'reports',
            'type' => 'outcome',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('status');
        $indicator->refresh();
        $this->assertEquals('PI-NEW', $indicator->code);
        $this->assertEquals('New Name for Indicator', $indicator->name);
        $this->assertEquals('outcome', $indicator->type);
    }

    public function test_admin_can_toggle_indicator_status(): void
    {
        $admin = $this->createUserWithRole('admin');

        $indicator = PerformanceIndicator::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patch(route('admin.indicators.toggle-status', $indicator));

        $response->assertSessionHas('status');
        $indicator->refresh();
        $this->assertFalse($indicator->is_active);
    }

    public function test_admin_cannot_delete_indicator_with_associated_bed2_plans(): void
    {
        $admin = $this->createUserWithRole('admin');

        $unit = OperatingUnit::factory()->create();
        $pap = PAP::factory()->create();
        $indicator = PerformanceIndicator::factory()->create(['pap_id' => $pap->id]);

        Bed2Plan::factory()->create([
            'operating_unit_id' => $unit->id,
            'pap_id' => $pap->id,
            'performance_indicator_id' => $indicator->id,
            'year' => 2026,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.indicators.destroy', $indicator));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('performance_indicators', ['id' => $indicator->id]);
    }

    public function test_authenticated_user_can_quick_create_pap_via_json_endpoint(): void
    {
        $encoder = $this->createUserWithRole('encoder');

        $response = $this->actingAs($encoder)->postJson(route('paps.quick-store'), [
            'code' => 'PAP-QUICK',
            'name' => 'Rapid Plant Health Assessment Project',
            'category' => 'Operations',
            'description' => 'Fast tracked via BED-2 encoding form.',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'pap' => [
                'code' => 'PAP-QUICK',
                'name' => 'Rapid Plant Health Assessment Project',
            ],
        ]);

        $this->assertDatabaseHas('paps', ['code' => 'PAP-QUICK']);
    }

    public function test_authenticated_user_can_quick_create_indicator_via_json_endpoint(): void
    {
        $encoder = $this->createUserWithRole('encoder');
        $pap = PAP::factory()->create(['code' => 'PAP-PARENT']);

        $response = $this->actingAs($encoder)->postJson(route('indicators.quick-store'), [
            'pap_id' => $pap->id,
            'code' => 'PI-QUICK',
            'name' => 'Number of quarantine inspections executed',
            'unit_of_measure' => 'inspections',
            'type' => 'output',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'indicator' => [
                'pap_id' => $pap->id,
                'code' => 'PI-QUICK',
                'unit_of_measure' => 'inspections',
            ],
        ]);

        $this->assertDatabaseHas('performance_indicators', [
            'pap_id' => $pap->id,
            'code' => 'PI-QUICK',
        ]);
    }
}
