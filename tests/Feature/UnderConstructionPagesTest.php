<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class UnderConstructionPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Create a user assigned to the given Spatie role.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function createUserWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    #[TestWith(['bed2-plans.index'])]
    #[TestWith(['monthly-reports.index'])]
    #[TestWith(['unlock-requests.index'])]
    #[TestWith(['admin.audit-logs.index'])]
    public function test_guest_is_redirected_to_login_from_under_construction_pages(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertRedirect(route('login'));
    }

    #[TestWith(['bed2-plans.index', 'BED-2 Target Plans'])]
    #[TestWith(['monthly-reports.index', 'Monthly Reports'])]
    #[TestWith(['unlock-requests.index', 'Unlock Requests'])]
    public function test_authenticated_workspace_user_can_view_workspace_under_construction_pages(
        string $routeName,
        string $expectedTitle,
    ): void {
        $encoder = $this->createUserWithRole('encoder');

        $response = $this->actingAs($encoder)->get(route($routeName));

        $response->assertOk()
            ->assertViewIs('under-construction')
            ->assertSee($expectedTitle)
            ->assertSee('Under Construction')
            ->assertSee('This Page Is Under Construction')
            ->assertSee(route('dashboard'), false);
    }

    #[TestWith(['encoder'])]
    #[TestWith(['ou-head'])]
    #[TestWith(['higher-up'])]
    public function test_non_governance_roles_cannot_access_audit_and_security_logs(string $role): void
    {
        $user = $this->createUserWithRole($role);

        $response = $this->actingAs($user)->get(route('admin.audit-logs.index'));

        $response->assertForbidden();
    }

    #[TestWith(['superadmin'])]
    #[TestWith(['admin'])]
    #[TestWith(['evaluator'])]
    public function test_governance_roles_can_view_audit_and_security_logs_under_construction_page(string $role): void
    {
        $user = $this->createUserWithRole($role);

        $response = $this->actingAs($user)->get(route('admin.audit-logs.index'));

        $response->assertOk()
            ->assertViewIs('under-construction')
            ->assertSee('Audit & Security Logs', false)
            ->assertSee('Under Construction')
            ->assertSee('This Page Is Under Construction');
    }

    public function test_navigation_and_admin_dashboard_render_links_to_under_construction_pages(): void
    {
        $admin = $this->createUserWithRole('admin');
        $encoder = $this->createUserWithRole('encoder');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('bed2-plans.index'), false)
            ->assertSee(route('monthly-reports.index'), false)
            ->assertSee(route('unlock-requests.index'), false)
            ->assertSee(route('admin.audit-logs.index'), false);

        $this->actingAs($encoder)
            ->get(route('encoder.dashboard'))
            ->assertOk()
            ->assertSee(route('bed2-plans.index'), false)
            ->assertSee(route('monthly-reports.index'), false)
            ->assertSee(route('unlock-requests.index'), false)
            ->assertDontSee(route('admin.audit-logs.index'), false);
    }
}
