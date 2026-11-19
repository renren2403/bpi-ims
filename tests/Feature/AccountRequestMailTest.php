<?php

namespace Tests\Feature;

use App\Mail\AccountApprovedMail;
use App\Mail\AccountDeclinedMail;
use App\Mail\AccountRequestSubmittedMail;
use App\Mail\NewAccountRequestAlertMail;
use App\Models\OperatingUnit;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountRequestMailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a user assigned to the given Spatie role.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function createUserWithRole(string $role, array $attributes = []): User
    {
        /** @var User $user */
        $user = User::factory()->createOne($attributes);
        $user->assignRole($role);

        return $user;
    }

    public function test_approving_pending_account_sends_approval_email(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $admin = $this->createUserWithRole('admin');

        $unit = OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        $pendingUser = $this->createUserWithRole('encoder', [
            'name' => 'Maria Santos',
            'email' => 'maria@bpi.gov.ph',
            'operating_unit_id' => $unit->id,
            'is_active' => false,
            'approval_status' => 'pending',
        ]);
        $this->actingAs($admin)
            ->post(route('admin.users.approve', $pendingUser))
            ->assertRedirect();
        $pendingUser->refresh();
        $this->assertSame('approved', $pendingUser->approval_status);
        $this->assertTrue($pendingUser->is_active);
        Mail::assertQueued(AccountApprovedMail::class, function (AccountApprovedMail $mail) use ($pendingUser): bool {
            return $mail->hasTo($pendingUser->email) && $mail->user->is($pendingUser);
        });
        $mailable = new AccountApprovedMail($pendingUser);
        $mailable->assertHasSubject('BPI-IPMS Account Request Approved');
        $mailable->assertSeeInHtml('Maria Santos');
        $mailable->assertSeeInHtml('Crop Pest Management Division (CPMD)');
        $mailable->assertSeeInHtml(route('login'));
        $mailable->assertDontSeeInHtml('<code>', false);
    }

    public function test_declining_pending_account_sends_declined_email(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $admin = $this->createUserWithRole('admin');

        $pendingUser = $this->createUserWithRole('ou-head', [
            'name' => 'Pedro Reyes',
            'email' => 'pedro@bpi.gov.ph',
            'is_active' => false,
            'approval_status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.reject', $pendingUser), [
                'remarks' => 'Please verify your operating unit assignment.',
            ])
            ->assertRedirect();

        $pendingUser->refresh();
        $this->assertSame('rejected', $pendingUser->approval_status);
        $this->assertFalse($pendingUser->is_active);

        Mail::assertQueued(AccountDeclinedMail::class, function (AccountDeclinedMail $mail) use ($pendingUser): bool {
            return $mail->hasTo($pendingUser->email) && $mail->user->is($pendingUser);
        });

        $mailable = new AccountDeclinedMail($pendingUser);
        $mailable->assertHasSubject('BPI-IPMS Account Request Declined');
        $mailable->assertSeeInHtml('Pedro Reyes');
        $mailable->assertSeeInHtml('Please verify your operating unit assignment.');
        $mailable->assertDontSeeInHtml('<code>', false);
    }

    public function test_submitting_account_request_sends_confirmation_email_to_requester(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $admin = $this->createUserWithRole('admin', [
            'name' => 'PMO Administrator',
            'email' => 'admin@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);
        $unit = OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Juan Dela Cruz',
            'designation' => 'Agriculturist II',
            'email' => 'juan@bpi.gov.ph',
            'role' => 'encoder',
            'operating_unit_id' => $unit->id,
            'justification' => 'Assigned division encoder.',
            'password' => 'Password@123!',
            'password_confirmation' => 'Password@123!',
        ]);

        $response->assertRedirect(route('login'));

        /** @var User $user */
        $user = User::where('email', 'juan@bpi.gov.ph')->firstOrFail();

        // 1. Verify confirmation email to applicant
        Mail::assertQueued(AccountRequestSubmittedMail::class, function (AccountRequestSubmittedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });

        $mailable = new AccountRequestSubmittedMail($user);
        $mailable->assertHasSubject('BPI-IPMS Account Request Received - Pending PMO Verification');
        $mailable->assertSeeInHtml('Juan Dela Cruz');
        $mailable->assertSeeInHtml('OU Encoder');
        $mailable->assertSeeInHtml('Crop Pest Management Division (CPMD)');
        $mailable->assertDontSeeInHtml('<code>', false);

        // 2. Verify alert email to PMO Administrator
        Mail::assertQueued(NewAccountRequestAlertMail::class, function (NewAccountRequestAlertMail $mail) use ($admin, $user): bool {
            return $mail->hasTo($admin->email) && $mail->applicant->is($user);
        });

        $adminMailable = new NewAccountRequestAlertMail($user);
        $adminMailable->assertHasSubject('[BPI-IPMS Action Required] New Account Request - Juan Dela Cruz');
        $adminMailable->assertSeeInHtml('Juan Dela Cruz');
        $adminMailable->assertSeeInHtml('Agriculturist II');
        $adminMailable->assertSeeInHtml('OU Encoder');
        $adminMailable->assertSeeInHtml('Crop Pest Management Division (CPMD)');
        $adminMailable->assertSeeInHtml('Assigned division encoder.');
        $adminMailable->assertSeeInHtml(route('admin.users.index', ['approval_status' => 'pending']));
        $adminMailable->assertDontSeeInHtml('<code>', false);
    }

    public function test_declined_user_can_reapply_for_an_account(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $admin = $this->createUserWithRole('admin', [
            'name' => 'PMO Administrator',
            'email' => 'admin@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        $unit = OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        $rejectedUser = $this->createUserWithRole('encoder', [
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@bpi.gov.ph',
            'operating_unit_id' => $unit->id,
            'is_active' => false,
            'approval_status' => 'rejected',
            'approval_remarks' => 'Declined: wrong division.',
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Juan Dela Cruz Jr.',
            'designation' => 'Senior Agriculturist',
            'email' => 'juan@bpi.gov.ph',
            'role' => 'ou-head',
            'operating_unit_id' => $unit->id,
            'justification' => 'Corrected division assignment.',
            'password' => 'NewPassword@123!',
            'password_confirmation' => 'NewPassword@123!',
        ]);

        $response->assertRedirect(route('login'));

        $rejectedUser->refresh();
        $this->assertSame('pending', $rejectedUser->approval_status);
        $this->assertSame('Juan Dela Cruz Jr.', $rejectedUser->name);
        $this->assertSame('Senior Agriculturist', $rejectedUser->designation);
        $this->assertTrue($rejectedUser->hasRole('ou-head'));

        Mail::assertQueued(AccountRequestSubmittedMail::class, function (AccountRequestSubmittedMail $mail) use ($rejectedUser): bool {
            return $mail->hasTo($rejectedUser->email);
        });

        Mail::assertQueued(NewAccountRequestAlertMail::class, function (NewAccountRequestAlertMail $mail) use ($admin): bool {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_approved_and_pending_users_cannot_re_register(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $unit = OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        $approved = $this->createUserWithRole('encoder', [
            'email' => 'approved@bpi.gov.ph',
            'operating_unit_id' => $unit->id,
            'approval_status' => 'approved',
        ]);

        $pending = $this->createUserWithRole('encoder', [
            'email' => 'pending@bpi.gov.ph',
            'operating_unit_id' => $unit->id,
            'approval_status' => 'pending',
        ]);

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => $approved->email,
            'role' => 'encoder',
            'operating_unit_id' => $unit->id,
            'password' => 'Password@123!',
            'password_confirmation' => 'Password@123!',
        ])->assertSessionHasErrors('email');

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => $pending->email,
            'role' => 'encoder',
            'operating_unit_id' => $unit->id,
            'password' => 'Password@123!',
            'password_confirmation' => 'Password@123!',
        ])->assertSessionHasErrors('email');

        Mail::assertNothingOutgoing();
    }
}
