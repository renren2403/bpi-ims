<?php

// UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountDeclinedMail;
use App\Mail\AdminPasswordResetMail;
use App\Models\AuditLog;
use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Throwable;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['operatingUnit', 'roles'])->latest();
        // Search by name or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by Operating Unit
        if ($ouId = $request->input('operating_unit_id')) {
            $query->where('operating_unit_id', $ouId);
        }

        // Filter by Role
        if ($roleName = $request->input('role')) {
            $query->role($roleName);
        }

        // Filter by Approval Status
        if ($approval = $request->input('approval_status')) {
            $query->where('approval_status', $approval);
        }

        // Filter by Active Status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $users = $query->paginate(15)->withQueryString();
        $operatingUnits = OperatingUnit::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $pendingCount = 0;
        try {
            if (Schema::hasColumn('users', 'approval_status')) {
                $pendingCount = User::where('approval_status', 'pending')->count();
            }
        } catch (Throwable $e) {
            $pendingCount = 0;
        }

        return view('admin.users.index', compact('users', 'operatingUnits', 'roles', 'pendingCount'));
    }

    public function create()
    {
        $operatingUnits = OperatingUnit::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('operatingUnits', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
            'operating_unit_id' => ['nullable', 'exists:operating_units,id'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ]);

        $role = $validated['role'];
        $pmo = OperatingUnit::where('code', 'PMO')->first();
        if (in_array($role, ['higher-up', 'superadmin'])) {
            $ouId = null;
        } elseif (in_array($role, ['admin', 'evaluator']) && empty($validated['operating_unit_id'])) {
            $ouId = $pmo?->id;
        } else {
            $ouId = $validated['operating_unit_id'] ?? null;
        }

        $user = User::create([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'operating_unit_id' => $ouId,
            'is_active' => $request->boolean('is_active', true),
            'approval_status' => 'approved',
        ]);

        $user->assignRole($validated['role']);

        try {
            AuditLog::record(
                action: 'user.created',
                auditable: $user,
                newValues: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $validated['role'],
                    'operating_unit_id' => $ouId,
                ],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('admin.users.index')->with('status', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $operatingUnits = OperatingUnit::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'operatingUnits', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'operating_unit_id' => ['nullable', 'exists:operating_units,id'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'approval_status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $oldValues = [
            'name' => $user->name,
            'designation' => $user->designation,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'operating_unit_id' => $user->operating_unit_id,
            'role' => $user->roles->first()?->name,
            'is_active' => $user->is_active,
            'approval_status' => $user->approval_status,
        ];

        $role = $validated['role'];
        $pmo = OperatingUnit::where('code', 'PMO')->first();
        if (in_array($role, ['higher-up', 'superadmin'])) {
            $ouId = null;
        } elseif (in_array($role, ['admin', 'evaluator']) && empty($validated['operating_unit_id'])) {
            $ouId = $pmo?->id;
        } else {
            $ouId = $validated['operating_unit_id'] ?? null;
        }

        $user->update([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'email' => $validated['email'],
            'operating_unit_id' => $ouId,
            'is_active' => $request->boolean('is_active'),
            'approval_status' => $validated['approval_status'],
        ]);

        $user->syncRoles([$validated['role']]);

        try {
            AuditLog::record(
                action: 'user.updated',
                auditable: $user,
                oldValues: $oldValues,
                newValues: [
                    'name' => $user->name,
                    'designation' => $user->designation,
                    'phone_number' => $user->phone_number,
                    'email' => $user->email,
                    'operating_unit_id' => $ouId,
                    'role' => $role,
                    'is_active' => $user->is_active,
                    'approval_status' => $user->approval_status,
                ],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully!');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $action = $validated['action'];
        $count = 0;

        foreach ($users as $user) {
            if ($action === 'approve') {
                $wasApproved = $user->isApproved();
                $user->update([
                    'approval_status' => 'approved',
                    'is_active' => true,
                    'approval_remarks' => $validated['remarks'] ?? 'Bulk approved by PMO Administrator on '.now()->toFormattedDateString(),
                ]);

                if (! $wasApproved) {
                    $this->sendDecisionEmail($user, new AccountApprovedMail($user));
                }

                try {
                    AuditLog::record(
                        action: 'user.approved',
                        auditable: $user,
                        newValues: ['approval_status' => 'approved', 'is_active' => true],
                        request: $request
                    );
                } catch (Throwable $e) {
                    report($e);
                }

                $count++;
            } elseif ($action === 'reject') {
                if ($user->id === Auth::id() || $user->hasRole('superadmin')) {
                    continue;
                }

                $wasRejected = $user->isRejected();
                $user->update([
                    'approval_status' => 'rejected',
                    'is_active' => false,
                    'approval_remarks' => $validated['remarks'] ?? 'Bulk declined by PMO Administrator on '.now()->toFormattedDateString(),
                ]);

                if (! $wasRejected) {
                    $this->sendDecisionEmail($user, new AccountDeclinedMail($user));
                }

                try {
                    AuditLog::record(
                        action: 'user.declined',
                        auditable: $user,
                        newValues: ['approval_status' => 'rejected', 'is_active' => false],
                        request: $request
                    );
                } catch (Throwable $e) {
                    report($e);
                }

                $count++;
            }
        }

        $actionVerb = $action === 'approve' ? 'approved and activated' : 'declined';

        return back()->with('status', "{$count} account request(s) successfully {$actionVerb}.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $state = $user->is_active ? 'activated' : 'deactivated';

        try {
            AuditLog::record(
                action: 'user.status_toggled',
                auditable: $user,
                newValues: ['is_active' => $user->is_active],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "User {$user->name} has been {$state}.");
    }

    public function approve(Request $request, User $user)
    {
        $wasApproved = $user->isApproved();

        $user->update([
            'approval_status' => 'approved',
            'is_active' => true,
            'approval_remarks' => $request->input('remarks', 'Approved by PMO Administrator on '.now()->toFormattedDateString()),
        ]);

        if (! $wasApproved) {
            $this->sendDecisionEmail($user, new AccountApprovedMail($user));
        }

        try {
            AuditLog::record(
                action: 'user.approved',
                auditable: $user,
                newValues: ['approval_status' => 'approved', 'is_active' => true],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Account request for {$user->name} has been approved and activated.");
    }

    public function reject(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot reject your own account.');
        }
        $wasRejected = $user->isRejected();
        $user->update([
            'approval_status' => 'rejected',
            'is_active' => false,
            'approval_remarks' => $request->input('remarks', 'Declined by PMO Administrator on '.now()->toFormattedDateString()),
        ]);
        if (! $wasRejected) {
            $this->sendDecisionEmail($user, new AccountDeclinedMail($user));
        }

        try {
            AuditLog::record(
                action: 'user.declined',
                auditable: $user,
                newValues: ['approval_status' => 'rejected', 'is_active' => false],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Account request for {$user->name} has been declined.");
    }

    private function sendDecisionEmail(User $user, Mailable $mailable): void
    {
        if (empty($user->email)) {
            return;
        }
        try {
            Mail::to($user->email)->send($mailable);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $this->sendDecisionEmail($user, new AdminPasswordResetMail($user));

        try {
            AuditLog::record(
                action: 'user.password_reset_by_admin',
                auditable: $user,
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Password for {$user->name} has been reset.");
    }

    /**
     * Permanently delete a user account while safeguarding admin hierarchy and active superadmins.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($reason = $user->deletionDenialReason(Auth::user())) {
            return redirect()->route('admin.users.index')->with('error', $reason);
        }

        if ($user->hasRole('superadmin') && User::role('superadmin')->count() <= 1) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete the last remaining Superadmin account.');
        }

        try {
            AuditLog::record(
                action: 'user.deleted',
                auditable: $user,
                oldValues: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()?->name,
                    'operating_unit_id' => $user->operating_unit_id,
                ],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        $name = $user->name;
        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "User {$name} has been deleted.");
    }
}
