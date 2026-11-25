<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class RolePermissionController extends Controller
{
    /**
     * Display the dynamic role and permission matrix.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $roles = Role::with('permissions')->orderBy('id')->get();

        $permissionsQuery = Permission::orderBy('module')->orderBy('name');

        if ($search = $request->input('search')) {
            $permissionsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%");
            });
        }

        if ($moduleFilter = $request->input('module')) {
            $permissionsQuery->where('module', $moduleFilter);
        }

        $allPermissions = $permissionsQuery->get();
        $permissionsByModule = $allPermissions->groupBy(fn ($p) => $p->display_module);

        $availableModules = Permission::select('module')
            ->distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module')
            ->all();

        return view('admin.roles-permissions.index', [
            'roles' => $roles,
            'permissionsByModule' => $permissionsByModule,
            'totalPermissions' => $allPermissions->count(),
            'availableModules' => $availableModules,
            'currentSearch' => $search,
            'currentModule' => $moduleFilter,
        ]);
    }

    /**
     * Instant toggle of a single permission for a specific role (AJAX).
     */
    public function toggle(Request $request): JsonResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permission_id' => ['required', 'exists:permissions,id'],
        ]);

        /** @var Role $role */
        $role = Role::findOrFail($validated['role_id']);
        /** @var Permission $permission */
        $permission = Permission::findOrFail($validated['permission_id']);

        // Protect Superadmin role from being stripped of permissions
        if ($role->name === 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Superadmin unconditionally retains all permissions across the system.',
                'attached' => true,
            ], 422);
        }

        $hasPermission = $role->hasPermissionTo($permission);

        if ($hasPermission) {
            $role->revokePermissionTo($permission);
            $attached = false;
            $action = 'role.permission_revoked';
            $message = "Action '{$permission->display_label}' has been disabled for {$role->display_title}.";
        } else {
            $role->givePermissionTo($permission);
            $attached = true;
            $action = 'role.permission_granted';
            $message = "Action '{$permission->display_label}' has been enabled for {$role->display_title}.";
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        try {
            AuditLog::record(
                action: $action,
                auditable: $role,
                oldValues: [
                    'permission_name' => $permission->name,
                    'is_granted' => $hasPermission,
                ],
                newValues: [
                    'permission_name' => $permission->name,
                    'is_granted' => $attached,
                ],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'attached' => $attached,
            'message' => $message,
            'role_id' => $role->id,
            'permission_id' => $permission->id,
            'role_title' => $role->display_title,
            'permission_label' => $permission->display_label,
        ]);
    }

    /**
     * Dynamically register a new action / permission on the fly from the UI.
     */
    public function storePermission(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_.-]+$/',
                'unique:permissions,name',
            ],
            'label' => ['required', 'string', 'max:255'],
            'module' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'assign_to_roles' => ['nullable', 'array'],
            'assign_to_roles.*' => ['exists:roles,id'],
        ], [
            'name.regex' => 'The action code may only contain lowercase letters, numbers, dots, hyphens, and underscores (e.g. reports.export_pdf).',
            'name.unique' => 'An action with this exact code key already exists.',
        ]);

        $permission = Permission::create([
            'name' => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
            'label' => trim($validated['label']),
            'module' => trim($validated['module']),
            'description' => $validated['description'] ?? null,
        ]);

        // Automatically assign to selected roles
        if (! empty($validated['assign_to_roles'])) {
            $roles = Role::whereIn('id', $validated['assign_to_roles'])->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }

        // Always grant new permission to superadmin
        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin && ! $superadmin->hasPermissionTo($permission)) {
            $superadmin->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        try {
            AuditLog::record(
                action: 'permission.created',
                auditable: $permission,
                newValues: $permission->only(['name', 'label', 'module', 'description']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "New action '{$permission->display_label}' registered successfully!",
                'permission' => $permission,
            ], 201);
        }

        return back()->with('status', "Action '{$permission->display_label}' ({$permission->name}) has been registered and added to the matrix.");
    }

    /**
     * Dynamically register a new custom role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_-]+$/',
                'unique:roles,name',
            ],
        ], [
            'name.regex' => 'Role slug may only contain lowercase letters, numbers, hyphens, and underscores.',
            'name.unique' => 'A role with this name already exists.',
        ]);

        $role = Role::create([
            'name' => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        try {
            AuditLog::record(
                action: 'role.created',
                auditable: $role,
                newValues: ['name' => $role->name],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Custom role '{$role->display_title}' registered successfully.");
    }

    /**
     * Authorize user access to role and permission management.
     */
    private function authorizeAccess(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || (! $user->can('roles.manage') && ! $user->hasAnyRole(['superadmin', 'admin']))) {
            abort(403, 'Unauthorized access to Dynamic Roles and Permissions management.');
        }
    }
}
