<?php

// AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountRequestSubmittedMail;
use App\Mail\NewAccountRequestAlertMail;
use App\Mail\PasswordChangedMail;
use App\Models\AuditLog;
use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Throwable;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ($accessError = $this->accountAccessError($user))) {
            return back()->withErrors([
                'email' => $accessError,
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $authenticatedUser */
            $authenticatedUser = Auth::user();
            $authenticatedUser->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            try {
                AuditLog::record(
                    action: 'auth.login',
                    auditable: $authenticatedUser,
                    newValues: [
                        'email' => $authenticatedUser->email,
                        'ip' => $request->ip(),
                    ],
                    request: $request,
                    userId: $authenticatedUser->id
                );
            } catch (Throwable $e) {
                report($e);
            }

            return $this->redirectUserByRole($authenticatedUser);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        $operatingUnits = OperatingUnit::where('is_active', true)->orderBy('name')->get();
        // Allow registering for operational roles (exclude superadmin/admin)
        $roles = Role::whereNotIn('name', ['superadmin', 'admin'])->orderBy('name')->get();

        return view('auth.register', compact('operatingUnits', 'roles'));
    }

    public function register(Request $request)
    {
        $role = $request->input('role');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['required', 'exists:roles,name'],
            'operating_unit_id' => [
                in_array($role, ['higher-up']) ? 'nullable' : 'required',
                'nullable',
                'exists:operating_units,id',
            ],
            'justification' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        // Check if an account with this email already exists
        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            if ($existingUser->isApproved()) {
                return back()->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors(['email' => 'An approved account with this email address already exists. Please sign in or reset your password.']);
            }

            if ($existingUser->isPendingApproval()) {
                return back()->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors(['email' => 'An account request for this email address is already pending PMO verification.']);
            }
        }

        $pmo = OperatingUnit::where('code', 'PMO')->first();

        // Assign operating unit according to role responsibility:
        // - higher-up (Executive Directorate): Null (encompasses all operating units bureau-wide)
        // - evaluator (PMO Evaluator): Defaults to Planning and Management Office (PMO)
        // - encoder / ou-head: Assigned to their selected Operating Unit
        if ($role === 'higher-up') {
            $operatingUnitId = null;
        } elseif ($role === 'evaluator') {
            $operatingUnitId = ! empty($validated['operating_unit_id']) ? $validated['operating_unit_id'] : $pmo?->id;
        } else {
            $operatingUnitId = $validated['operating_unit_id'] ?? null;
        }

        $justificationText = ! empty($validated['justification']) ? 'Justification: '.$validated['justification'] : null;

        // If user was previously declined, update their existing record and re-submit as pending
        if ($existingUser && $existingUser->isRejected()) {
            $existingUser->update([
                'name' => $validated['name'],
                'designation' => $validated['designation'] ?? null,
                'password' => Hash::make($validated['password']),
                'operating_unit_id' => $operatingUnitId,
                'is_active' => false,
                'approval_status' => 'pending',
                'approval_remarks' => $justificationText ? 'Re-application '.$justificationText : null,
            ]);

            $existingUser->syncRoles([$validated['role']]);
            $user = $existingUser;
            $statusMessage = 'Your account request has been re-submitted successfully! Please wait for the PMO Administrator to verify and approve your access.';
        } else {
            // New applicant registration
            $user = User::create([
                'name' => $validated['name'],
                'designation' => $validated['designation'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'operating_unit_id' => $operatingUnitId,
                'is_active' => false,
                'approval_status' => 'pending',
                'approval_remarks' => $justificationText,
            ]);

            $user->assignRole($validated['role']);
            $statusMessage = 'Your account request has been submitted successfully! Please wait for the PMO Administrator to verify and approve your access.';
        }

        $user->load(['operatingUnit', 'roles']);

        // 1. Send confirmation email to applicant
        try {
            Mail::to($user->email)->send(new AccountRequestSubmittedMail($user));
        } catch (Throwable $e) {
            report($e);
        }

        // 2. Alert PMO administrators about the new pending request
        try {
            $adminEmails = User::role(['superadmin', 'admin'])
                ->where('is_active', true)
                ->where('approval_status', 'approved')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (! empty($adminEmails)) {
                Mail::to($adminEmails)->send(new NewAccountRequestAlertMail($user));
            }
        } catch (Throwable $e) {
            report($e);
        }

        try {
            AuditLog::record(
                action: 'auth.register',
                auditable: $user,
                newValues: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $validated['role'],
                    'operating_unit_id' => $user->operating_unit_id,
                ],
                request: $request,
                userId: $user->id
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('login')->with('status', $statusMessage);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            try {
                AuditLog::record(
                    action: 'auth.logout',
                    auditable: $user,
                    request: $request,
                    userId: $user->id
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }

    public function redirectUserByRole($user)
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole('evaluator')) {
            return redirect()->intended(route('evaluator.dashboard'));
        }

        if ($user->hasRole('ou-head')) {
            return redirect()->intended(route('head.dashboard'));
        }

        if ($user->hasRole('encoder')) {
            return redirect()->intended(route('encoder.dashboard'));
        }

        if ($user->hasRole('higher-up')) {
            return redirect()->intended(route('executive.dashboard'));
        }

        return redirect()->intended(route('profile.show'));
    }

    /**
     * Show the forgot-password request form.
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to an active, approved user.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user && ($accessError = $this->accountAccessError($user))) {
            return back()->withErrors([
                'email' => $accessError,
            ])->onlyInput('email');
        }

        $status = PasswordBroker::sendResetLink($validated);

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form for a valid token.
     */
    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Complete a password reset request using a valid reset token.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user && ($accessError = $this->accountAccessError($user))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => $accessError]);
        }

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            if ($user) {
                try {
                    Mail::to($user->email)->send(new PasswordChangedMail($user, now()->format('F j, Y g:i A'), $request->ip()));
                } catch (Throwable $e) {
                    report($e);
                }
            }

            return redirect()->route('login')->with('status', __($status));
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    /**
     * Return an access denial message if the user account cannot sign in or reset its password.
     */
    private function accountAccessError(User $user): ?string
    {
        if ($user->isPendingApproval()) {
            return 'Your account request is currently pending verification and approval by the PMO Administrator.';
        }

        if ($user->isRejected()) {
            return 'Your account registration request was declined. Please contact the PMO for assistance.';
        }

        if (! $user->is_active) {
            return 'Your account has been deactivated. Please contact the PMO for access.';
        }

        return null;
    }
}
