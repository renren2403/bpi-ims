<?php

namespace Tests\Feature;

use App\Mail\AdminPasswordResetMail;
use App\Mail\PasswordChangedMail;
use App\Mail\PasswordResetLinkMail;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetMailTest extends TestCase
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

    public function test_active_approved_user_can_request_and_complete_password_reset(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $user = $this->createUserWithRole('encoder', [
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        $this->get(route('password.request'))->assertOk();

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect();

        /** @var string $capturedToken */
        $capturedToken = '';

        Mail::assertQueued(PasswordResetLinkMail::class, function (PasswordResetLinkMail $mail) use ($user, &$capturedToken): bool {
            $capturedToken = $mail->token;

            return $mail->hasTo($user->email) && $mail->user->is($user);
        });

        $this->assertNotEmpty($capturedToken);

        $mailable = new PasswordResetLinkMail($user, $capturedToken);
        $mailable->assertHasSubject('BPI-IPMS Password Reset Request');
        $mailable->assertSeeInHtml('Juan Dela Cruz');
        $mailable->assertSeeInHtml(route('password.reset', [
            'token' => $capturedToken,
            'email' => $user->email,
        ]));
        $mailable->assertDontSeeInHtml('<code>', false);

        $this->get(route('password.reset', ['token' => $capturedToken, 'email' => $user->email]))
            ->assertOk();

        $this->post(route('password.update'), [
            'token' => $capturedToken,
            'email' => $user->email,
            'password' => 'NewSecure@123!',
            'password_confirmation' => 'NewSecure@123!',
        ])->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecure@123!', $user->password));

        Mail::assertQueued(PasswordChangedMail::class, function (PasswordChangedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });
    }

    public function test_pending_rejected_and_deactivated_users_cannot_request_password_reset(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $pending = $this->createUserWithRole('encoder', [
            'email' => 'pending@bpi.gov.ph',
            'is_active' => false,
            'approval_status' => 'pending',
        ]);

        $rejected = $this->createUserWithRole('encoder', [
            'email' => 'rejected@bpi.gov.ph',
            'is_active' => false,
            'approval_status' => 'rejected',
        ]);

        $inactive = $this->createUserWithRole('encoder', [
            'email' => 'inactive@bpi.gov.ph',
            'is_active' => false,
            'approval_status' => 'approved',
        ]);

        foreach ([$pending, $rejected, $inactive] as $blockedUser) {
            $this->post(route('password.email'), [
                'email' => $blockedUser->email,
            ])->assertSessionHasErrors('email');
        }

        Mail::assertNothingSent();
    }

    public function test_admin_resetting_user_password_sends_notification_email_without_plaintext_password(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $admin = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('encoder', [
            'name' => 'Maria Santos',
            'email' => 'maria@bpi.gov.ph',
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        $newPassword = 'TempResetPassword@2026';

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $target), [
                'new_password' => $newPassword,
            ])
            ->assertRedirect();

        $target->refresh();
        $this->assertTrue(Hash::check($newPassword, $target->password));

        Mail::assertQueued(AdminPasswordResetMail::class, function (AdminPasswordResetMail $mail) use ($target): bool {
            return $mail->hasTo($target->email) && $mail->user->is($target);
        });

        $mailable = new AdminPasswordResetMail($target);
        $mailable->assertHasSubject('BPI-IPMS Account Password Reset by PMO Administrator');
        $mailable->assertSeeInHtml('Maria Santos');
        $mailable->assertSeeInHtml('maria@bpi.gov.ph');
        $mailable->assertSeeInHtml(route('login'));
        $mailable->assertDontSeeInHtml($newPassword, false);
        $mailable->assertDontSeeInHtml('<code>', false);
    }

    public function test_non_admin_cannot_trigger_admin_password_reset(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $encoder = $this->createUserWithRole('encoder');
        $target = $this->createUserWithRole('encoder');

        $this->actingAs($encoder)
            ->post(route('admin.users.reset-password', $target), [
                'new_password' => 'Unauthorized@2026',
            ])
            ->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_user_updating_profile_password_sends_password_changed_notification(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        Mail::fake();

        $user = $this->createUserWithRole('encoder', [
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@bpi.gov.ph',
            'password' => Hash::make('CurrentPassword@123!'),
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        $this->actingAs($user)
            ->put(route('profile.password'), [
                'current_password' => 'CurrentPassword@123!',
                'password' => 'UpdatedPassword@123!',
                'password_confirmation' => 'UpdatedPassword@123!',
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertTrue(Hash::check('UpdatedPassword@123!', $user->password));

        Mail::assertQueued(PasswordChangedMail::class, function (PasswordChangedMail $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });

        $mailable = new PasswordChangedMail($user, now()->format('F j, Y g:i A'), '127.0.0.1');
        $mailable->assertHasSubject('[BPI-IPMS Security Notice] Your Password Was Changed');
        $mailable->assertSeeInHtml('Juan Dela Cruz');
        $mailable->assertSeeInHtml('juan@bpi.gov.ph');
        $mailable->assertSeeInHtml(route('password.request'));
        $mailable->assertDontSeeInHtml('<code>', false);
    }
}
