<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\OperatingUnit;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserProfileAndBulkActionsTest extends TestCase
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

    public function test_user_can_view_profile_page(): void
    {
        $user = $this->createUserWithRole('encoder', [
            'name' => 'Maria Makiling',
            'designation' => 'Senior Agriculturist',
            'phone_number' => '+63 912 345 6789',
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('Maria Makiling');
        $response->assertSee('Senior Agriculturist');
        $response->assertSee('+63 912 345 6789');
    }

    public function test_user_can_update_profile_info(): void
    {
        $user = $this->createUserWithRole('encoder', [
            'name' => 'Original Name',
            'designation' => 'Clerk I',
            'phone_number' => null,
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name',
            'designation' => 'Information Officer I',
            'phone_number' => '09181234567',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Profile details updated successfully.');

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('Information Officer I', $user->designation);
        $this->assertSame('09181234567', $user->phone_number);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'profile.updated',
        ]);
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        $user = $this->createUserWithRole('encoder');

        $file = UploadedFile::fake()->create('profile.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertNotNull($user->avatar);
        $this->assertFileExists(public_path($user->avatar));

        // Now remove the avatar
        $removeResponse = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'remove_avatar' => '1',
        ]);

        $removeResponse->assertRedirect();
        $user->refresh();

        $this->assertNull($user->avatar);
    }

    public function test_admin_can_bulk_approve_pending_users(): void
    {
        Mail::fake();
        $admin = $this->createUserWithRole('admin');

        $unit = OperatingUnit::create([
            'name' => 'National Seed Quality Control Services',
            'code' => 'NSQCS',
            'type' => 'Division',
            'is_active' => true,
        ]);

        $user1 = $this->createUserWithRole('encoder', [
            'name' => 'Applicant One',
            'email' => 'app1@bpi.gov.ph',
            'operating_unit_id' => $unit->id,
            'approval_status' => 'pending',
            'is_active' => false,
        ]);

        $user2 = $this->createUserWithRole('encoder', [
            'name' => 'Applicant Two',
            'email' => 'app2@bpi.gov.ph',
            'operating_unit_id' => $unit->id,
            'approval_status' => 'pending',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.bulk'), [
            'action' => 'approve',
            'user_ids' => [$user1->id, $user2->id],
            'remarks' => 'Batch approved by Administrator',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', '2 account request(s) successfully approved and activated.');

        $user1->refresh();
        $user2->refresh();

        $this->assertSame('approved', $user1->approval_status);
        $this->assertTrue($user1->is_active);
        $this->assertSame('approved', $user2->approval_status);
        $this->assertTrue($user2->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.approved',
            'auditable_id' => $user1->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.approved',
            'auditable_id' => $user2->id,
        ]);
    }

    public function test_admin_can_bulk_decline_pending_users(): void
    {
        Mail::fake();
        $admin = $this->createUserWithRole('admin');

        $user1 = $this->createUserWithRole('encoder', [
            'name' => 'Declined Applicant',
            'email' => 'dec@bpi.gov.ph',
            'approval_status' => 'pending',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.bulk'), [
            'action' => 'reject',
            'user_ids' => [$user1->id],
            'remarks' => 'Incomplete documentation',
        ]);

        $response->assertRedirect();
        $user1->refresh();

        $this->assertSame('rejected', $user1->approval_status);
        $this->assertFalse($user1->is_active);
        $this->assertSame('Incomplete documentation', $user1->approval_remarks);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user.declined',
            'auditable_id' => $user1->id,
        ]);
    }

    public function test_user_login_records_last_login_and_audit_log(): void
    {
        $user = $this->createUserWithRole('encoder', [
            'email' => 'login.test@bpi.gov.ph',
            'password' => Hash::make('Secret123!'),
            'approval_status' => 'approved',
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'login.test@bpi.gov.ph',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_login_ip);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.login',
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_view_audit_logs_page(): void
    {
        $admin = $this->createUserWithRole('admin');

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'system.test_event',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

        $response->assertOk();
        $response->assertSee('Audit & Security Activity Logs', false);
        $response->assertSee('system.test_event');
    }
}
