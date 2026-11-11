<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

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
        } catch (\Throwable $e) {
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
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'operating_unit_id' => $ouId,
            'is_active' => $request->boolean('is_active', true),
            'approval_status' => 'approved',
        ]);

        $user->assignRole($validated['role']);

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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'operating_unit_id' => ['nullable', 'exists:operating_units,id'],
            'role' => ['required', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'approval_status' => ['required', 'in:pending,approved,rejected'],
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

        $user->update([
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'email' => $validated['email'],
            'operating_unit_id' => $ouId,
            'is_active' => $request->boolean('is_active'),
            'approval_status' => $validated['approval_status'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $state = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('status', "User {$user->name} has been {$state}.");
    }

    public function approve(Request $request, User $user)
    {
        $user->update([
            'approval_status' => 'approved',
            'is_active' => true,
            'approval_remarks' => $request->input('remarks', 'Approved by PMO Administrator on '.now()->toFormattedDateString()),
        ]);

        return back()->with('status', "Account request for {$user->name} has been approved and activated.");
    }

    public function reject(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot reject your own account.');
        }

        $user->update([
            'approval_status' => 'rejected',
            'is_active' => false,
            'approval_remarks' => $request->input('remarks', 'Declined by PMO Administrator on '.now()->toFormattedDateString()),
        ]);

        return back()->with('status', "Account request for {$user->name} has been declined.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('status', "Password for {$user->name} has been reset.");
    }
}
