<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\OperatingUnit;
use Spatie\Permission\Models\Role;

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

        if ($user) {
            // 1. Pending registration check
            if ($user->approval_status === 'pending') {
                return back()->withErrors([
                    'email' => 'Your account request is currently pending verification and approval by the PMO Administrator.',
                ])->onlyInput('email');
            }

            // 2. Rejected registration check
            if ($user->approval_status === 'rejected') {
                return back()->withErrors([
                    'email' => 'Your account registration request was declined. Please contact the PMO for assistance.',
                ])->onlyInput('email');
            }

            // 3. Deactivated user check
            if (! $user->is_active) {
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact the PMO for access.',
                ])->onlyInput('email');
            }
        }

        $remember = $request->boolean('remember');
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return $this->redirectUserByRole(Auth::user());
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'operating_unit_id' => ['required', 'exists:operating_units,id'],
            'role' => ['required', 'exists:roles,name'],
            'justification' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'operating_unit_id' => $validated['operating_unit_id'],
            'is_active' => false, // Inactive until approved
            'approval_status' => 'pending',
            'approval_remarks' => !empty($validated['justification']) ? 'Justification: ' . $validated['justification'] : null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('login')->with(
            'status',
            'Your account request has been submitted successfully! Please wait for the PMO Administrator to verify and approve your access.'
        );
    }

    public function logout(Request $request)
    {
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
}
