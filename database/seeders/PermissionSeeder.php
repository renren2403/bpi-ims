<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByModule = [
            'Programs & PAPs' => [
                ['name' => 'paps.view', 'label' => 'View Programs / PAPs Catalog', 'description' => 'Can view official program line items and indicator descriptions.'],
                ['name' => 'paps.create', 'label' => 'Register New Programs / PAPs', 'description' => 'Can add new PAPs and indicators to the central catalog.'],
                ['name' => 'paps.edit', 'label' => 'Edit Programs & Indicators', 'description' => 'Can update program titles, codes, and indicator metrics.'],
                ['name' => 'paps.delete', 'label' => 'Delete Programs / PAPs', 'description' => 'Can delete unattached programs and indicators.'],
                ['name' => 'paps.toggle_status', 'label' => 'Activate / Deactivate Programs', 'description' => 'Can toggle active status of programs.'],
            ],
            'BED-2 Target Plans' => [
                ['name' => 'bed2.view', 'label' => 'View Target Plans', 'description' => 'Can view annual target baselines and monthly milestone breakdowns.'],
                ['name' => 'bed2.create', 'label' => 'Encode Target Plans', 'description' => 'Can create draft physical target allocations for a fiscal year.'],
                ['name' => 'bed2.edit', 'label' => 'Edit Target Allocations', 'description' => 'Can modify monthly/quarterly targets in draft or during review.'],
                ['name' => 'bed2.delete', 'label' => 'Delete Target Plans', 'description' => 'Can delete draft or returned plan entries.'],
                ['name' => 'bed2.submit_head', 'label' => 'Submit to Division Chief', 'description' => 'Can forward draft targets to OU Head for internal division review.'],
                ['name' => 'bed2.head_endorse', 'label' => 'Endorse to PMO', 'description' => 'Can endorse division targets and submit forward to PMO.'],
                ['name' => 'bed2.pmo_approve', 'label' => 'Approve & Lock Annual Baseline', 'description' => 'Final approving authority that locks the baseline for the year.'],
                ['name' => 'bed2.return', 'label' => 'Return Targets with Revision Remarks', 'description' => 'Can return plans with remarks requesting adjustments.'],
                ['name' => 'bed2.manage_all_units', 'label' => 'Encode / Manage Any Operating Unit', 'description' => 'Can select and encode targets for all operating units bureau-wide.'],
            ],
            'Monthly Reports' => [
                ['name' => 'reports.view', 'label' => 'View Monthly Accomplishments', 'description' => 'Can view monthly progress reports and accomplishment matrices.'],
                ['name' => 'reports.create', 'label' => 'Encode Monthly Actuals', 'description' => 'Can input physical accomplishments against approved targets.'],
                ['name' => 'reports.edit', 'label' => 'Edit Accomplishment Entries', 'description' => 'Can adjust actuals and variance explanations during open cycle.'],
                ['name' => 'reports.upload_mov', 'label' => 'Upload Means of Verification (MOVs)', 'description' => 'Can attach PDF/image verification files.'],
                ['name' => 'reports.delete_mov', 'label' => 'Delete Uploaded MOVs', 'description' => 'Can remove uploaded proof files before locking.'],
                ['name' => 'reports.submit', 'label' => 'Submit Monthly Report to Head', 'description' => 'Can submit completed monthly accomplishment package.'],
                ['name' => 'reports.head_endorse', 'label' => 'Endorse Monthly Report to PMO', 'description' => 'Can sign off and forward monthly report to PMO.'],
                ['name' => 'reports.evaluate', 'label' => 'Evaluate & Lock Accomplishments', 'description' => 'PMO review and sign-off on bureau monthly accomplishments.'],
                ['name' => 'reports.return', 'label' => 'Return Monthly Report', 'description' => 'Can return monthly report to encoder for adjustments.'],
            ],
            'Unlock Requests' => [
                ['name' => 'unlock_requests.view', 'label' => 'View Unlock Requests', 'description' => 'Can view history of period unlock requests.'],
                ['name' => 'unlock_requests.create', 'label' => 'Submit Unlock Request', 'description' => 'Can submit formal request to unlock closed reporting window.'],
                ['name' => 'unlock_requests.review', 'label' => 'Approve / Reject Unlock Requests', 'description' => 'Authority to grant temporary reporting window reopening.'],
            ],
            'Administration & Governance' => [
                ['name' => 'users.view', 'label' => 'View User Directory', 'description' => 'Can view registered users and their assignments.'],
                ['name' => 'users.manage', 'label' => 'Create, Edit & Delete Users', 'description' => 'Can manage staff credentials and designations.'],
                ['name' => 'users.approve', 'label' => 'Approve / Reject Account Requests', 'description' => 'Can approve or decline pending self-registered users.'],
                ['name' => 'roles.manage', 'label' => 'Manage Dynamic Roles & Permissions', 'description' => 'Can toggle role permissions matrix and register new actions.'],
                ['name' => 'operating_units.manage', 'label' => 'Manage Operating Units', 'description' => 'Can add and update bureau divisions, stations, and centers.'],
                ['name' => 'reporting_periods.manage', 'label' => 'Manage Reporting Cycles', 'description' => 'Can open, close, and schedule monthly cutoff dates.'],
                ['name' => 'audit_logs.view', 'label' => 'View Security & Audit Logs', 'description' => 'Can view immutable activity audit trail.'],
            ],
        ];

        // 1. Create or update each permission
        foreach ($permissionsByModule as $module => $items) {
            foreach ($items as $item) {
                Permission::updateOrCreate(
                    ['name' => $item['name'], 'guard_name' => 'web'],
                    [
                        'label' => $item['label'],
                        'module' => $module,
                        'description' => $item['description'],
                    ]
                );
            }
        }

        // 2. Assign default permissions per role
        $roleDefaults = [
            'admin' => [
                'paps.*',
                'bed2.*',
                'reports.*',
                'unlock_requests.*',
                'users.*',
                'roles.manage',
                'operating_units.manage',
                'reporting_periods.manage',
                'audit_logs.view',
            ],
            'evaluator' => [
                'paps.view',
                'paps.create',
                'paps.edit',
                'paps.toggle_status',
                'bed2.view',
                'bed2.create',
                'bed2.edit',
                'bed2.pmo_approve',
                'bed2.return',
                'bed2.manage_all_units',
                'reports.view',
                'reports.evaluate',
                'reports.return',
                'unlock_requests.view',
                'unlock_requests.review',
                'operating_units.manage',
                'reporting_periods.manage',
                'audit_logs.view',
            ],
            'ou-head' => [
                'paps.view',
                'bed2.view',
                'bed2.create',
                'bed2.edit',
                'bed2.head_endorse',
                'bed2.return',
                'reports.view',
                'reports.head_endorse',
                'reports.return',
                'unlock_requests.view',
                'unlock_requests.create',
            ],
            'encoder' => [
                'paps.view',
                'bed2.view',
                'bed2.create',
                'bed2.edit',
                'bed2.delete',
                'bed2.submit_head',
                'reports.view',
                'reports.create',
                'reports.edit',
                'reports.upload_mov',
                'reports.delete_mov',
                'reports.submit',
                'unlock_requests.view',
                'unlock_requests.create',
            ],
            'higher-up' => [
                'paps.view',
                'bed2.view',
                'reports.view',
                'unlock_requests.view',
                'audit_logs.view',
            ],
        ];

        $allPermissions = Permission::all();

        foreach ($roleDefaults as $roleName => $assignedPatterns) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                continue;
            }

            $matchedPermissions = collect();
            foreach ($assignedPatterns as $pattern) {
                if (str_ends_with($pattern, '.*')) {
                    $prefix = substr($pattern, 0, -2);
                    $matched = $allPermissions->filter(fn ($p) => str_starts_with($p->name, $prefix));
                    $matchedPermissions = $matchedPermissions->merge($matched);
                } else {
                    $matched = $allPermissions->where('name', $pattern);
                    $matchedPermissions = $matchedPermissions->merge($matched);
                }
            }

            $role->syncPermissions($matchedPermissions->pluck('name')->all());
        }

        // Superadmin receives all permissions explicitly as well
        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            $superadmin->syncPermissions($allPermissions->pluck('name')->all());
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
