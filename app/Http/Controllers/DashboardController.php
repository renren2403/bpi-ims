<?php
namespace App\Http\Controllers;
use App\Models\AuditLog;
use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PerformanceIndicator;
use App\Models\ReportingPeriod;
use App\Models\UnlockRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
class DashboardController extends Controller
{
    public function admin(Request $request): View
    {
        $activeCycle = $this->getActiveCycle();
        $currentYear = $activeCycle?->year ?? (int) date('Y');
        $now = Carbon::now();
        $operatingUnits = OperatingUnit::coveredForReporting()
            ->where('is_active', true)
            ->withCount([
                'bed2Plans as total_plans_count' => fn ($q) => $q->where('year', $currentYear),
                'bed2Plans as approved_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'approved'),
                'bed2Plans as pending_pmo_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'head_approved'),
                'bed2Plans as pending_head_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'submitted_to_head'),
                'bed2Plans as draft_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'draft'),
            ])
            ->withSum([
                'bed2Plans as total_annual_target' => fn ($q) => $q->where('year', $currentYear),
            ], 'annual_target')
            ->orderBy('code')
            ->get();
        $totalOUs = $operatingUnits->count();
        $ousWithApprovedBed2 = $operatingUnits->where('approved_plans_count', '>', 0)->count();
        $ousWithSubmissions = $operatingUnits->where('total_plans_count', '>', 0)->count();
        $totalPlans = Bed2Plan::where('year', $currentYear)->count();
        $approvedPlans = Bed2Plan::where('year', $currentYear)->where('status', 'approved')->count();
        $pendingPmoPlans = Bed2Plan::where('year', $currentYear)->where('status', 'head_approved')->count();
        $pendingHeadPlans = Bed2Plan::where('year', $currentYear)->where('status', 'submitted_to_head')->count();
        $draftPlans = Bed2Plan::where('year', $currentYear)->where('status', 'draft')->count();
        $returnedPlans = Bed2Plan::where('year', $currentYear)->where('status', 'returned')->count();
        $complianceRate = $totalOUs > 0 ? round(($ousWithApprovedBed2 / $totalOUs) * 100, 1) : 0;
        $submissionRate = $totalOUs > 0 ? round(($ousWithSubmissions / $totalOUs) * 100, 1) : 0;
        $pendingUnlockRequests = UnlockRequest::with(['operatingUnit', 'requestedBy'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        $recentAuditLogs = AuditLog::with('user')
            ->latest()
            ->take(6)
            ->get();
        $recentBed2Plans = Bed2Plan::with(['operatingUnit', 'pap', 'performanceIndicator'])
            ->where('year', $currentYear)
            ->latest()
            ->take(6)
            ->get();
        return view('dashboard.admin', compact(
            'activeCycle',
            'currentYear',
            'now',
            'operatingUnits',
            'totalOUs',
            'ousWithApprovedBed2',
            'ousWithSubmissions',
            'complianceRate',
            'submissionRate',
            'totalPlans',
            'approvedPlans',
            'pendingPmoPlans',
            'pendingHeadPlans',
            'draftPlans',
            'returnedPlans',
            'pendingUnlockRequests',
            'recentAuditLogs',
            'recentBed2Plans'
        ));
    }
    /**
     * PMO Evaluator Performance Review Desk.
     */
    public function evaluator(Request $request): View
    {
        $activeCycle = $this->getActiveCycle();
        $currentYear = $activeCycle?->year ?? (int) date('Y');
        $now = Carbon::now();
        // BED-2 Plans awaiting PMO sign-off (endorsed by division heads)
        $pendingPmoPlans = Bed2Plan::with(['operatingUnit', 'pap', 'performanceIndicator', 'headApprovedBy', 'submittedBy'])
            ->where('year', $currentYear)
            ->where('status', 'head_approved')
            ->latest()
            ->get();
        $approvedPlansCount = Bed2Plan::where('year', $currentYear)->where('status', 'approved')->count();
        $returnedPlansCount = Bed2Plan::where('year', $currentYear)->where('status', 'returned')->count();
        $pendingHeadCount = Bed2Plan::where('year', $currentYear)->where('status', 'submitted_to_head')->count();
        $totalPlansCount = Bed2Plan::where('year', $currentYear)->count();
        $operatingUnits = OperatingUnit::coveredForReporting()
            ->where('is_active', true)
            ->withCount([
                'bed2Plans as total_plans_count' => fn ($q) => $q->where('year', $currentYear),
                'bed2Plans as approved_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'approved'),
                'bed2Plans as pending_pmo_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'head_approved'),
                'bed2Plans as pending_head_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'submitted_to_head'),
                'bed2Plans as returned_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'returned'),
                'bed2Plans as draft_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'draft'),
            ])
            ->withSum([
                'bed2Plans as total_annual_target' => fn ($q) => $q->where('year', $currentYear),
            ], 'annual_target')
            ->orderBy('code')
            ->get();
        $totalOUs = $operatingUnits->count();
        $clearedOUsCount = $operatingUnits->where('approved_plans_count', '>', 0)->count();
        $pendingUnlockCount = UnlockRequest::where('status', 'pending')->count();
        return view('dashboard.evaluator', compact(
            'activeCycle',
            'currentYear',
            'now',
            'pendingPmoPlans',
            'approvedPlansCount',
            'returnedPlansCount',
            'pendingHeadCount',
            'totalPlansCount',
            'operatingUnits',
            'totalOUs',
            'clearedOUsCount',
            'pendingUnlockCount'
        ));
    }
    /**
     * Division Chief / OU Head Command Desk.
     */
    public function head(Request $request): View
    {
        $user = Auth::user();
        $operatingUnit = $user->operatingUnit;
        $activeCycle = $this->getActiveCycle();
        $currentYear = $activeCycle?->year ?? (int) date('Y');
        $now = Carbon::now();
        $myPlans = collect();
        $staffMembers = collect();
        if ($operatingUnit) {
            $myPlans = Bed2Plan::with(['pap', 'performanceIndicator', 'submittedBy'])
                ->where('operating_unit_id', $operatingUnit->id)
                ->where('year', $currentYear)
                ->orderBy('created_at', 'desc')
                ->get();
            $staffMembers = User::where('operating_unit_id', $operatingUnit->id)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->get();
        }
        $totalIndicators = $myPlans->count();
        $pendingHeadPlans = $myPlans->where('status', 'submitted_to_head');
        $pendingHeadCount = $pendingHeadPlans->count();
        $endorsedCount = $myPlans->where('status', 'head_approved')->count();
        $approvedCount = $myPlans->where('status', 'approved')->count();
        $draftCount = $myPlans->where('status', 'draft')->count();
        $returnedCount = $myPlans->where('status', 'returned')->count();
        $totalAnnualTarget = (float) $myPlans->sum('annual_target');
        $activeReportingPeriod = $activeCycle;
        return view('dashboard.head', compact(
            'user',
            'operatingUnit',
            'activeCycle',
            'activeReportingPeriod',
            'currentYear',
            'now',
            'myPlans',
            'pendingHeadPlans',
            'pendingHeadCount',
            'endorsedCount',
            'approvedCount',
            'draftCount',
            'returnedCount',
            'totalIndicators',
            'totalAnnualTarget',
            'staffMembers'
        ));
    }
    /**
     * OU Encoder Accomplishment & Target Desk.
     */
    public function encoder(Request $request): View
    {
        $user = Auth::user();
        $operatingUnit = $user->operatingUnit;
        $activeCycle = $this->getActiveCycle();
        $currentYear = $activeCycle?->year ?? (int) date('Y');
        $now = Carbon::now();
        $myPlans = collect();
        if ($operatingUnit) {
            $myPlans = Bed2Plan::with(['pap', 'performanceIndicator', 'headApprovedBy', 'pmoApprovedBy'])
                ->where('operating_unit_id', $operatingUnit->id)
                ->where('year', $currentYear)
                ->latest()
                ->get();
        }
        $totalIndicators = $myPlans->count();
        $draftPlans = $myPlans->where('status', 'draft');
        $draftCount = $draftPlans->count();
        $submittedToHeadCount = $myPlans->where('status', 'submitted_to_head')->count();
        $endorsedToPmoCount = $myPlans->where('status', 'head_approved')->count();
        $approvedCount = $myPlans->where('status', 'approved')->count();
        $returnedPlans = $myPlans->where('status', 'returned');
        $returnedCount = $returnedPlans->count();
        $totalAnnualTarget = (float) $myPlans->sum('annual_target');
        return view('dashboard.encoder', compact(
            'user',
            'operatingUnit',
            'activeCycle',
            'currentYear',
            'now',
            'myPlans',
            'draftPlans',
            'draftCount',
            'submittedToHeadCount',
            'endorsedToPmoCount',
            'approvedCount',
            'returnedPlans',
            'returnedCount',
            'totalIndicators',
            'totalAnnualTarget'
        ));
    }
    public function executive(Request $request): View
    {
        $activeCycle = $this->getActiveCycle();
        $currentYear = $activeCycle?->year ?? (int) date('Y');
        $now = Carbon::now();
        $operatingUnits = OperatingUnit::coveredForReporting()
            ->where('is_active', true)
            ->withCount([
                'bed2Plans as total_plans_count' => fn ($q) => $q->where('year', $currentYear),
                'bed2Plans as approved_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'approved'),
                'bed2Plans as pending_pmo_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'head_approved'),
                'bed2Plans as pending_head_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'submitted_to_head'),
                'bed2Plans as draft_plans_count' => fn ($q) => $q->where('year', $currentYear)->where('status', 'draft'),
            ])
            ->withSum([
                'bed2Plans as total_annual_target' => fn ($q) => $q->where('year', $currentYear),
                'bed2Plans as approved_annual_target' => fn ($q) => $q->where('year', $currentYear)->where('status', 'approved'),
            ], 'annual_target')
            ->orderBy('code')
            ->get();
        $totalOUs = $operatingUnits->count();
        $ousWithApprovedBed2 = $operatingUnits->where('approved_plans_count', '>', 0)->count();
        $complianceRate = $totalOUs > 0 ? round(($ousWithApprovedBed2 / $totalOUs) * 100, 1) : 0;
        $totalBureauTarget = (float) Bed2Plan::where('year', $currentYear)->sum('annual_target');
        $approvedBureauTarget = (float) Bed2Plan::where('year', $currentYear)->where('status', 'approved')->sum('annual_target');
        $totalIndicatorsTracked = PerformanceIndicator::where('is_active', true)->count();
        $pendingApprovalCount = Bed2Plan::where('year', $currentYear)
            ->whereIn('status', ['submitted_to_head', 'head_approved'])
            ->count();
        return view('dashboard.executive', compact(
            'activeCycle',
            'currentYear',
            'now',
            'operatingUnits',
            'totalOUs',
            'ousWithApprovedBed2',
            'complianceRate',
            'totalBureauTarget',
            'approvedBureauTarget',
            'totalIndicatorsTracked',
            'pendingApprovalCount'
        ));
    }
    private function getActiveCycle(): ?ReportingPeriod
    {
        return ReportingPeriod::where('is_active', true)
            ->orWhere('status', 'open')
            ->orderByDesc('is_active')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();
    }
}