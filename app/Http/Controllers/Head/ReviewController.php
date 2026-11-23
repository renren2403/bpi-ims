<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\Bed2Plan;
use App\Models\PAP;
use App\Models\ReportingPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display the OU Head 'For Review' queue of division baseline submissions.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $operatingUnit = $user->operatingUnit;

        $activeCycle = ReportingPeriod::where('is_active', true)
            ->orWhere('status', 'open')
            ->orderByDesc('is_active')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        $selectedYear = (int) $request->input('year', $activeCycle?->year ?? date('Y'));
        $statusTab = $request->input('status', 'submitted_to_head');

        $baseQuery = Bed2Plan::with(['pap', 'performanceIndicator', 'submittedBy', 'headApprovedBy'])
            ->where('year', $selectedYear);

        if ($operatingUnit) {
            $baseQuery->where('operating_unit_id', $operatingUnit->id);
        }

        // Metrics for tabs
        $metrics = [
            'pending' => (clone $baseQuery)->where('status', 'submitted_to_head')->count(),
            'endorsed' => (clone $baseQuery)->where('status', 'head_approved')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'returned' => (clone $baseQuery)->where('status', 'returned')->count(),
            'total' => (clone $baseQuery)->count(),
        ];

        // Apply tab filtering
        $query = clone $baseQuery;
        if ($statusTab === 'submitted_to_head') {
            $query->where('status', 'submitted_to_head');
        } elseif ($statusTab === 'head_approved') {
            $query->where('status', 'head_approved');
        } elseif ($statusTab === 'returned') {
            $query->where('status', 'returned');
        } elseif ($statusTab === 'approved') {
            $query->where('status', 'approved');
        }

        if ($papFilter = $request->input('pap_id')) {
            $query->where('pap_id', $papFilter);
        }

        $plans = $query->latest()->paginate(15)->withQueryString();

        $totalPendingTarget = (float) (clone $baseQuery)->where('status', 'submitted_to_head')->sum('annual_target');

        $availableYears = Bed2Plan::select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->all();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y'), (int) date('Y') + 1];
        } elseif (! in_array((int) date('Y'), $availableYears, true)) {
            $availableYears[] = (int) date('Y');
            rsort($availableYears);
        }

        $paps = PAP::where('is_active', true)->orderBy('code')->get();

        return view('head.reviews.index', compact(
            'user',
            'operatingUnit',
            'activeCycle',
            'selectedYear',
            'statusTab',
            'metrics',
            'plans',
            'totalPendingTarget',
            'availableYears',
            'paps'
        ));
    }
}
