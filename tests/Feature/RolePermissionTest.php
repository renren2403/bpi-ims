<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
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
        $response = $this->get(route('admin.roles-permissions.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_encoder_is_forbidden_from_accessing_roles_permissions(): void
    {
        $encoder = $this->createUserWithRole('encoder');

        $response = $this->actingAs($encoder)->get(route('admin.roles-permissions.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_roles_permissions_index(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.roles-permissions.index'));

        $response->assertOk();
        $response->assertViewHas(['roles', 'permissionsByModule', 'availableModules']);
    }

    public function test_admin_can_toggle_permission_on_and_off_for_role(): void
    {
        $admin = $this->createUserWithRole('admin');
        $encoderRole = Role::where('name', 'encoder')->firstOrFail();
        $permission = Permission::where('name', 'bed2.pmo_approve')->firstOrFail();

        // Initially, encoder does NOT have bed2.pmo_approve
        $this->assertFalse($encoderRole->hasPermissionTo($permission));

        // 1. Toggle ON
        $responseOn = $this->actingAs($admin)->postJson(route('admin.roles-permissions.toggle'), [
            'role_id' => $encoderRole->id,
            'permission_id' => $permission->id,
        ]);

        $responseOn->assertOk();
        $responseOn->assertJson([
            'success' => true,
            'attached' => true,
        ]);
        $this->assertTrue($encoderRole->fresh()->hasPermissionTo($permission));

        // 2. Toggle OFF
        $responseOff = $this->actingAs($admin)->postJson(route('admin.roles-permissions.toggle'), [
            'role_id' => $encoderRole->id,
            'permission_id' => $permission->id,
        ]);

        $responseOff->assertOk();
        $responseOff->assertJson([
            'success' => true,
            'attached' => false,
        ]);
        $this->assertFalse($encoderRole->fresh()->hasPermissionTo($permission));
    }

    public function test_superadmin_permissions_cannot_be_toggled_off(): void
    {
        $admin = $this->createUserWithRole('admin');
        $superadminRole = Role::where('name', 'superadmin')->firstOrFail();
        $permission = Permission::where('name', 'bed2.view')->firstOrFail();

        $response = $this->actingAs($admin)->postJson(route('admin.roles-permissions.toggle'), [
            'role_id' => $superadminRole->id,
            'permission_id' => $permission->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_admin_can_dynamically_register_new_permission(): void
    {
        $admin = $this->createUserWithRole('admin');
        $encoderRole = Role::where('name', 'encoder')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.roles-permissions.permissions.store'), [
            'name' => 'reports.export_pdf',
            'label' => 'Export Accomplishment PDF',
            'module' => 'Monthly Reports',
            'description' => 'Allows downloading generated monthly accomplishment PDF packets.',
            'assign_to_roles' => [$encoderRole->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('permissions', [
            'name' => 'reports.export_pdf',
            'label' => 'Export Accomplishment PDF',
            'module' => 'Monthly Reports',
        ]);

        $newPermission = Permission::where('name', 'reports.export_pdf')->firstOrFail();
        $this->assertTrue($encoderRole->fresh()->hasPermissionTo($newPermission));

        // Audit log was recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'permission.created',
            'auditable_type' => Permission::class,
            'auditable_id' => $newPermission->id,
        ]);
    }

    public function test_register_new_permission_validates_unique_code_and_format(): void
    {
        $admin = $this->createUserWithRole('admin');

        // Invalid format with capital letters / spaces
        $response = $this->actingAs($admin)->post(route('admin.roles-permissions.permissions.store'), [
            'name' => 'Invalid Name With Spaces!',
            'label' => 'Some Action',
            'module' => 'General',
        ]);

        $response->assertSessionHasErrors('name');

        // Duplicate code
        $responseDuplicate = $this->actingAs($admin)->post(route('admin.roles-permissions.permissions.store'), [
            'name' => 'bed2.view',
            'label' => 'Duplicate View',
            'module' => 'BED-2 Target Plans',
        ]);

        $responseDuplicate->assertSessionHasErrors('name');
    }

    public function test_admin_can_dynamically_register_new_custom_role(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.roles-permissions.roles.store'), [
            'name' => 'regional-auditor',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('roles', [
            'name' => 'regional-auditor',
        ]);

        $role = Role::where('name', 'regional-auditor')->firstOrFail();
        $this->assertEquals('Regional Auditor', $role->display_title);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'role.created',
            'auditable_type' => Role::class,
            'auditable_id' => $role->id,
        ]);
    }
}
