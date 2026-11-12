<?php
//UnderConstructionController.php
namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class UnderConstructionController extends Controller
{
    /**
     * Metadata definitions for placeholder modules under construction.
     *
     * @var array<string, array{title: string, section: string, icon: string, description: string, planned_features: list<string>}>
     */
    private const MODULES = [
        'bed2-plans' => [
            'title' => 'BED-2 Target Plans',
            'section' => 'Workspace',
            'icon' => 'document',
            'description' => 'Annual BED-2 physical target commitments, PAP indicator allocations, and milestone baselines across operating units are currently being finalized.',
            'planned_features' => [
                'Annual PAP and performance indicator target encoding per operating unit',
                'Quarterly and monthly physical milestone breakdown alignment',
                'Division Chief and PMO target plan review and baseline locking',
            ],
        ],
        'monthly-reports' => [
            'title' => 'Monthly Reports',
            'section' => 'Workspace',
            'icon' => 'chart',
            'description' => 'Monthly physical accomplishment encoding, variance explanations, and Means of Verification (MOV) submission workflows are currently being built.',
            'planned_features' => [
                'Monthly actual accomplishment entry against approved BED-2 targets',
                'Root-cause analysis and slippage remarks for under-performing indicators',
                'Certified Means of Verification (MOV) document upload and validation',
            ],
        ],
        'unlock-requests' => [
            'title' => 'Unlock Requests',
            'section' => 'Workspace',
            'icon' => 'lock-open',
            'description' => 'Division Chief-endorsed requests to reopen closed reporting periods and time-bound PMO unlock authorizations are currently being built.',
            'planned_features' => [
                'Formal unlock inquiry submission with justification for locked cycles',
                'Division Chief endorsement routing prior to PMO approval',
                'Time-boxed temporary submission window tracking and automatic re-locking',
            ],
        ],
        'audit-logs' => [
            'title' => 'Audit & Security Logs',
            'section' => 'PMO Governance',
            'icon' => 'shield',
            'description' => 'Chronological COA-compliant activity logs, submission histories, and administrative security event monitoring are currently being built.',
            'planned_features' => [
                'Immutable trail of report certifications, validations, and unlock approvals',
                'User management, role changes, and password reset security events',
                'Filterable exportable logs by operating unit, actor, and date range',
            ],
        ],
    ];

    /**
     * Display the under-construction page for BED-2 Target Plans.
     */
    public function bed2Plans(): View
    {
        return $this->renderModule('bed2-plans');
    }

    /**
     * Display the under-construction page for Monthly Reports.
     */
    public function monthlyReports(): View
    {
        return $this->renderModule('monthly-reports');
    }

    /**
     * Display the under-construction page for Unlock Requests.
     */
    public function unlockRequests(): View
    {
        return $this->renderModule('unlock-requests');
    }

    /**
     * Display the under-construction page for Audit & Security Logs.
     */
    public function auditLogs(): View
    {
        return $this->renderModule('audit-logs');
    }

    /**
     * Render the shared under-construction view for a configured module key.
     */
    private function renderModule(string $key): View
    {
        abort_unless(isset(self::MODULES[$key]), 404);

        return view('under-construction', [
            'module' => self::MODULES[$key],
        ]);
    }
}
