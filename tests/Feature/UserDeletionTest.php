<?php

namespace Tests\Feature;

use App\Models\OperatingUnit;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Create a user assigned to the given Spatie role.
     */
    private function createUserWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    public function test_guest_is_redirected_to_login_when_attempting_to_delete_user(): void
    {
        $target = $this->createUserWithRole('encoder');

        $response = $this->delete(route('admin.users.destroy', $target));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_non_admin_user_cannot_delete_another_user(): void
    {
        $encoder = $this->createUserWithRole('encoder');
        $target = $this->createUserWithRole('encoder');

        $response = $this->actingAs($encoder)->delete(route('admin.users.destroy', $target));

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_can_delete_regular_user_and_nullifies_operating_unit_officer_links(): void
    {
        $admin = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('encoder', ['name' => 'Target Encoder']);

        $unit = OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
            'reporting_officer_id' => $target->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $target));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', 'User Target Encoder has been deleted.');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseHas('operating_units', [
            'id' => $unit->id,
            'reporting_officer_id' => null,
        ]);
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'You cannot delete your own account.');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_superadmin_account(): void
    {
        $admin = $this->createUserWithRole('admin');
        $superadmin = $this->createUserWithRole('superadmin');

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $superadmin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'Only a Superadmin can delete a Superadmin account.');
        $this->assertDatabaseHas('users', ['id' => $superadmin->id]);
    }

    public function test_evaluator_cannot_delete_admin_account(): void
    {
        $evaluator = $this->createUserWithRole('evaluator');
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($evaluator)->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error', 'You do not have permission to delete an Administrator account.');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_superadmin_can_delete_another_superadmin_when_another_exists(): void
    {
        $superadminOne = $this->createUserWithRole('superadmin');
        $superadminTwo = $this->createUserWithRole('superadmin', ['name' => 'Secondary Superadmin']);

        $response = $this->actingAs($superadminOne)->delete(route('admin.users.destroy', $superadminTwo));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', 'User Secondary Superadmin has been deleted.');
        $this->assertDatabaseMissing('users', ['id' => $superadminTwo->id]);
    }

    public function test_user_management_views_render_delete_action_for_authorized_targets(): void
    {
        $admin = $this->createUserWithRole('admin');
        $encoder = $this->createUserWithRole('encoder', ['name' => 'Deletable Encoder']);
        $superadmin = $this->createUserWithRole('superadmin', ['name' => 'Protected Superadmin']);

        $indexResponse = $this->actingAs($admin)->get(route('admin.users.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('action="'.route('admin.users.destroy', $encoder).'"', false);
        $indexResponse->assertDontSee('action="'.route('admin.users.destroy', $admin).'"', false);
        $indexResponse->assertDontSee('action="'.route('admin.users.destroy', $superadmin).'"', false);

        $editResponse = $this->actingAs($admin)->get(route('admin.users.edit', $encoder));
        $editResponse->assertOk();
        $editResponse->assertSee('Delete Personnel Account');
        $editResponse->assertSee('action="'.route('admin.users.destroy', $encoder).'"', false);

        $editSelfResponse = $this->actingAs($admin)->get(route('admin.users.edit', $admin));
        $editSelfResponse->assertOk();
        $editSelfResponse->assertDontSee('Delete Personnel Account');
    }
}
