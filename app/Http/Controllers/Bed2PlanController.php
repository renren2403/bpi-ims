<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Bed2Plan;
use App\Models\OperatingUnit;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class Bed2PlanController extends Controller
{
    /**
     * Display a listing of BED-2 target plans with matrix filters.
     */
    public function index(Request $request): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $query = Bed2Plan::with([
            'operatingUnit',
            'pap',
            'performanceIndicator',
            'submittedBy',
            'headApprovedBy',
            'pmoApprovedBy',
        ]);

        // Role-based data isolation
        if (! $currentUser->can('bed2.manage_all_units') && ! $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator', 'higher-up'])) {
            $query->where('operating_unit_id', $currentUser->operating_unit_id);
        } elseif ($ouId = $request->input('operating_unit_id')) {
            $query->where('operating_unit_id', $ouId);
        }

        // Year filter (default to current year)
        $year = (int) $request->input('year', date('Y'));
        $query->where('year', $year);

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // PAP filter
        if ($papId = $request->input('pap_id')) {
            $query->where('pap_id', $papId);
        }

        // Search query (indicator name or code)
        if ($search = $request->input('search')) {
            $query->whereHas('performanceIndicator', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $plans = $query->orderBy('operating_unit_id')
            ->orderBy('pap_id')
            ->paginate(15)
            ->withQueryString();

        // Metrics for dashboard tabs
        $metricsQuery = Bed2Plan::where('year', $year);
        if (! $currentUser->can('bed2.manage_all_units') && ! $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator', 'higher-up'])) {
            $metricsQuery->where('operating_unit_id', $currentUser->operating_unit_id);
        } elseif ($ouId = $request->input('operating_unit_id')) {
            $metricsQuery->where('operating_unit_id', $ouId);
        }

        $metrics = [
            'total' => (clone $metricsQuery)->count(),
            'approved' => (clone $metricsQuery)->where('status', 'approved')->count(),
            'pending_head' => (clone $metricsQuery)->where('status', 'submitted_to_head')->count(),
            'pending_pmo' => (clone $metricsQuery)->where('status', 'head_approved')->count(),
            'draft' => (clone $metricsQuery)->where('status', 'draft')->count(),
            'returned' => (clone $metricsQuery)->where('status', 'returned')->count(),
        ];

        $operatingUnits = OperatingUnit::coveredForReporting()->where('is_active', true)->orderBy('name')->get();
        $paps = PAP::where('is_active', true)->orderBy('code')->get();

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

        return view('bed2-plans.index', compact('plans', 'metrics', 'operatingUnits', 'paps', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new BED-2 plan.
     */
    public function create(Request $request): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Admin/evaluators and users with manage_all_units see all units; encoders/heads see assigned unit
        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator']);

        if ($isPrivileged) {
            $operatingUnits = OperatingUnit::coveredForReporting()->where('is_active', true)->orderBy('name')->get();
            $defaultOuId = $request->input('operating_unit_id', $operatingUnits->first()?->id);
        } else {
            $operatingUnits = OperatingUnit::where('id', $currentUser->operating_unit_id)->get();
            $defaultOuId = $currentUser->operating_unit_id;
        }

        $paps = PAP::with(['performanceIndicators' => function ($q) {
            $q->where('is_active', true)->orderBy('code');
        }])->where('is_active', true)->orderBy('code')->get();

        $year = (int) $request->input('year', date('Y'));
        $categories = PAP::CATEGORIES;

        return view('bed2-plans.create', compact('operatingUnits', 'paps', 'defaultOuId', 'year', 'categories'));
    }

    /**
     * Store a newly created BED-2 plan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator']);
        $ouId = $isPrivileged ? $request->input('operating_unit_id') : $currentUser->operating_unit_id;

        $validated = $request->validate([
            'operating_unit_id' => [
                $isPrivileged ? 'required' : 'nullable',
                'exists:operating_units,id',
            ],
            'pap_id' => ['required', 'exists:paps,id'],
            'performance_indicator_id' => [
                'required',
                'exists:performance_indicators,id',
                Rule::unique('bed2_plans')->where(function ($query) use ($ouId, $request) {
                    return $query->where('operating_unit_id', $ouId)
                        ->where('year', $request->input('year'));
                }),
            ],
            'year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'annual_target' => ['required', 'numeric', 'min:0'],
            'm1_target' => ['nullable', 'numeric', 'min:0'],
            'm2_target' => ['nullable', 'numeric', 'min:0'],
            'm3_target' => ['nullable', 'numeric', 'min:0'],
            'm4_target' => ['nullable', 'numeric', 'min:0'],
            'm5_target' => ['nullable', 'numeric', 'min:0'],
            'm6_target' => ['nullable', 'numeric', 'min:0'],
            'm7_target' => ['nullable', 'numeric', 'min:0'],
            'm8_target' => ['nullable', 'numeric', 'min:0'],
            'm9_target' => ['nullable', 'numeric', 'min:0'],
            'm10_target' => ['nullable', 'numeric', 'min:0'],
            'm11_target' => ['nullable', 'numeric', 'min:0'],
            'm12_target' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ], [
            'performance_indicator_id.unique' => 'A BED-2 plan for this Performance Indicator and Operating Unit already exists for the selected year.',
        ]);

        $mTargets = [];
        for ($i = 1; $i <= 12; $i++) {
            $mTargets["m{$i}_target"] = (float) ($validated["m{$i}_target"] ?? 0);
        }

        // Calculate Quarterly Targets
        $q1 = $mTargets['m1_target'] + $mTargets['m2_target'] + $mTargets['m3_target'];
        $q2 = $mTargets['m4_target'] + $mTargets['m5_target'] + $mTargets['m6_target'];
        $q3 = $mTargets['m7_target'] + $mTargets['m8_target'] + $mTargets['m9_target'];
        $q4 = $mTargets['m10_target'] + $mTargets['m11_target'] + $mTargets['m12_target'];

        $plan = Bed2Plan::create(array_merge($validated, $mTargets, [
            'operating_unit_id' => $ouId,
            'q1_target' => $q1,
            'q2_target' => $q2,
            'q3_target' => $q3,
            'q4_target' => $q4,
            'status' => 'draft',
            'submitted_by' => $currentUser->id,
        ]));

        try {
            AuditLog::record(
                action: 'bed2.created',
                auditable: $plan,
                newValues: [
                    'operating_unit_id' => $plan->operating_unit_id,
                    'performance_indicator_id' => $plan->performance_indicator_id,
                    'year' => $plan->year,
                    'annual_target' => $plan->annual_target,
                ],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('bed2-plans.show', $plan)
            ->with('status', 'BED-2 Target Plan created successfully in Draft status.');
    }

    /**
     * Display the specified BED-2 plan dossier.
     */
    public function show(Bed2Plan $bed2Plan): View
    {
        $this->authorizeView($bed2Plan);

        $bed2Plan->load([
            'operatingUnit',
            'pap',
            'performanceIndicator',
            'submittedBy',
            'headApprovedBy',
            'pmoApprovedBy',
        ]);

        return view('bed2-plans.show', compact('bed2Plan'));
    }

    /**
     * Show the form for editing the specified BED-2 plan.
     */
    public function edit(Bed2Plan $bed2Plan): View
    {
        $this->authorizeEdit($bed2Plan);

        $bed2Plan->load(['operatingUnit', 'pap', 'performanceIndicator']);

        return view('bed2-plans.edit', compact('bed2Plan'));
    }

    /**
     * Update the specified BED-2 plan in storage.
     */
    public function update(Request $request, Bed2Plan $bed2Plan): RedirectResponse
    {
        $this->authorizeEdit($bed2Plan);

        $validated = $request->validate([
            'annual_target' => ['required', 'numeric', 'min:0'],
            'm1_target' => ['nullable', 'numeric', 'min:0'],
            'm2_target' => ['nullable', 'numeric', 'min:0'],
            'm3_target' => ['nullable', 'numeric', 'min:0'],
            'm4_target' => ['nullable', 'numeric', 'min:0'],
            'm5_target' => ['nullable', 'numeric', 'min:0'],
            'm6_target' => ['nullable', 'numeric', 'min:0'],
            'm7_target' => ['nullable', 'numeric', 'min:0'],
            'm8_target' => ['nullable', 'numeric', 'min:0'],
            'm9_target' => ['nullable', 'numeric', 'min:0'],
            'm10_target' => ['nullable', 'numeric', 'min:0'],
            'm11_target' => ['nullable', 'numeric', 'min:0'],
            'm12_target' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldValues = [
            'annual_target' => $bed2Plan->annual_target,
            'q1_target' => $bed2Plan->q1_target,
            'q2_target' => $bed2Plan->q2_target,
            'q3_target' => $bed2Plan->q3_target,
            'q4_target' => $bed2Plan->q4_target,
        ];

        $mTargets = [];
        for ($i = 1; $i <= 12; $i++) {
            $mTargets["m{$i}_target"] = (float) ($validated["m{$i}_target"] ?? 0);
        }

        $q1 = $mTargets['m1_target'] + $mTargets['m2_target'] + $mTargets['m3_target'];
        $q2 = $mTargets['m4_target'] + $mTargets['m5_target'] + $mTargets['m6_target'];
        $q3 = $mTargets['m7_target'] + $mTargets['m8_target'] + $mTargets['m9_target'];
        $q4 = $mTargets['m10_target'] + $mTargets['m11_target'] + $mTargets['m12_target'];

        $bed2Plan->update(array_merge($validated, $mTargets, [
            'q1_target' => $q1,
            'q2_target' => $q2,
            'q3_target' => $q3,
            'q4_target' => $q4,
        ]));

        try {
            AuditLog::record(
                action: 'bed2.updated',
                auditable: $bed2Plan,
                oldValues: $oldValues,
                newValues: [
                    'annual_target' => $bed2Plan->annual_target,
                    'q1_target' => $q1,
                    'q2_target' => $q2,
                    'q3_target' => $q3,
                    'q4_target' => $q4,
                ],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('bed2-plans.show', $bed2Plan)
            ->with('status', 'BED-2 Target Plan updated successfully.');
    }

    /**
     * Remove the specified BED-2 plan from storage.
     */
    public function destroy(Bed2Plan $bed2Plan): RedirectResponse
    {
        $this->authorizeEdit($bed2Plan);

        if (! in_array($bed2Plan->status, ['draft', 'returned'])) {
            return back()->with('error', 'Only draft or returned plans can be deleted.');
        }

        try {
            AuditLog::record(
                action: 'bed2.deleted',
                auditable: $bed2Plan,
                oldValues: [
                    'operating_unit_id' => $bed2Plan->operating_unit_id,
                    'performance_indicator_id' => $bed2Plan->performance_indicator_id,
                    'year' => $bed2Plan->year,
                ],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        $bed2Plan->delete();

        return redirect()->route('bed2-plans.index')
            ->with('status', 'BED-2 Target Plan deleted.');
    }

    /**
     * Submit a draft plan to Division Chief for review.
     */
    public function submitToHead(Bed2Plan $bed2Plan): RedirectResponse
    {
        $this->authorizeEdit($bed2Plan);

        if (! in_array($bed2Plan->status, ['draft', 'returned'])) {
            return back()->with('error', 'Only draft or returned plans can be submitted to the Division Chief.');
        }

        if (! $bed2Plan->isBalanced()) {
            $variance = $bed2Plan->balanceVariance();
            $detail = $variance > 0
                ? 'under-allocated by '.number_format($variance, 2)
                : 'over-allocated by '.number_format(abs($variance), 2);

            return back()->with('error', "Cannot submit plan: Monthly targets do not balance with the annual commitment ({$detail}). Please edit targets so monthly totals equal annual target.");
        }

        $bed2Plan->update([
            'status' => 'submitted_to_head',
            'submitted_by' => Auth::id(),
        ]);

        try {
            AuditLog::record(
                action: 'bed2.submitted_to_head',
                auditable: $bed2Plan,
                newValues: ['status' => 'submitted_to_head'],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Target plan submitted to Division Chief for endorsement.');
    }

    /**
     * Division Chief endorses plan to PMO.
     */
    public function headApprove(Bed2Plan $bed2Plan): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->hasRole('ou-head') && ! $currentUser->hasAnyRole(['superadmin', 'admin'])) {
            abort(403, 'Only the Division Chief or Administrator can endorse this plan.');
        }

        if ($currentUser->hasRole('ou-head') && $bed2Plan->operating_unit_id !== $currentUser->operating_unit_id) {
            abort(403, 'You may only endorse plans belonging to your assigned Operating Unit.');
        }

        if ($bed2Plan->status !== 'submitted_to_head') {
            return back()->with('error', 'Plan must be under division review to be endorsed.');
        }

        if (! $bed2Plan->isBalanced()) {
            return back()->with('error', 'Cannot endorse plan to PMO: Target allocation is unbalanced. Please return it to encoder for revision.');
        }

        $bed2Plan->update([
            'status' => 'head_approved',
            'head_approved_by' => $currentUser->id,
        ]);

        try {
            AuditLog::record(
                action: 'bed2.head_approved',
                auditable: $bed2Plan,
                newValues: ['status' => 'head_approved'],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Target plan endorsed and forwarded to PMO for final validation.');
    }

    /**
     * Return plan for revision (by OU Head or PMO).
     */
    public function returnPlan(Request $request, Bed2Plan $bed2Plan): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:1000'],
        ]);

        $stage = $currentUser->hasRole('ou-head') ? 'Division Chief' : 'PMO';

        $bed2Plan->update([
            'status' => 'returned',
            'remarks' => "Returned by {$stage}: {$validated['remarks']}",
        ]);

        try {
            AuditLog::record(
                action: 'bed2.returned',
                auditable: $bed2Plan,
                newValues: ['status' => 'returned', 'remarks' => $bed2Plan->remarks],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Target plan returned to Encoder for revision.");
    }

    /**
     * PMO Administrator validates and locks the baseline.
     */
    public function pmoApprove(Bed2Plan $bed2Plan): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator'])) {
            abort(403, 'Only PMO Administrators or Evaluators can approve and lock baseline targets.');
        }

        if (! in_array($bed2Plan->status, ['head_approved', 'submitted_to_head'])) {
            return back()->with('error', 'Plan is not in a valid stage for PMO approval.');
        }

        if (! $bed2Plan->isBalanced()) {
            return back()->with('error', 'Cannot approve baseline: Target allocation is unbalanced.');
        }

        $bed2Plan->update([
            'status' => 'approved',
            'pmo_approved_by' => $currentUser->id,
        ]);

        try {
            AuditLog::record(
                action: 'bed2.approved',
                auditable: $bed2Plan,
                newValues: ['status' => 'approved'],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Target plan approved! Annual baseline is now officially locked for accomplishment reporting.');
    }

    /**
     * Batch submit all draft/returned plans for an Operating Unit & Year to Division Chief.
     */
    public function batchSubmitHead(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $validated = $request->validate([
            'year' => ['required', 'integer'],
            'operating_unit_id' => ['nullable', 'exists:operating_units,id'],
        ]);

        $year = (int) $validated['year'];
        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator']);
        $ouId = ($isPrivileged && ! empty($validated['operating_unit_id']))
            ? (int) $validated['operating_unit_id']
            : $currentUser->operating_unit_id;

        $plans = Bed2Plan::where('operating_unit_id', $ouId)
            ->where('year', $year)
            ->whereIn('status', ['draft', 'returned'])
            ->get();

        if ($plans->isEmpty()) {
            return back()->with('error', 'No draft or returned plans found for submission.');
        }

        $unbalancedPlans = $plans->filter(fn (Bed2Plan $p) => ! $p->isBalanced());

        if ($unbalancedPlans->isNotEmpty()) {
            $unbalancedCount = $unbalancedPlans->count();
            return back()->with('error', "Cannot submit plans: {$unbalancedCount} plan(s) are unbalanced. All monthly milestone allocations must balance with their annual target before submission.");
        }

        foreach ($plans as $plan) {
            $plan->update([
                'status' => 'submitted_to_head',
                'submitted_by' => $currentUser->id,
            ]);
        }

        try {
            AuditLog::record(
                action: 'bed2.batch_submitted_to_head',
                auditable: $plans->first(),
                newValues: ['count' => $plans->count(), 'year' => $year, 'operating_unit_id' => $ouId],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "{$plans->count()} target indicators successfully submitted to Division Chief for review.");
    }

    /**
     * Batch endorse all submitted plans for an Operating Unit & Year to PMO.
     */
    public function batchHeadApprove(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->can('bed2.head_endorse') && ! $currentUser->hasAnyRole(['superadmin', 'admin', 'ou-head'])) {
            abort(403, 'Unauthorized to endorse target plans to PMO.');
        }

        $validated = $request->validate([
            'year' => ['nullable', 'integer'],
            'operating_unit_id' => ['nullable', 'exists:operating_units,id'],
            'plan_ids' => ['nullable', 'array'],
            'plan_ids.*' => ['integer', 'exists:bed2_plans,id'],
        ]);

        $year = ! empty($validated['year']) ? (int) $validated['year'] : null;
        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin']);
        $ouId = ($isPrivileged && ! empty($validated['operating_unit_id']))
            ? (int) $validated['operating_unit_id']
            : $currentUser->operating_unit_id;

        $query = Bed2Plan::where('status', 'submitted_to_head');

        if ($ouId) {
            $query->where('operating_unit_id', $ouId);
        }

        if ($year) {
            $query->where('year', $year);
        }

        if (! empty($validated['plan_ids'])) {
            $query->whereIn('id', $validated['plan_ids']);
        }

        $plans = $query->get();

        if ($plans->isEmpty()) {
            return back()->with('error', 'No plans pending division review found to endorse.');
        }

        $unbalancedPlans = $plans->filter(fn (Bed2Plan $p) => ! $p->isBalanced());
        if ($unbalancedPlans->isNotEmpty()) {
            return back()->with('error', "Cannot endorse plans to PMO: {$unbalancedPlans->count()} plan(s) are unbalanced.");
        }

        foreach ($plans as $plan) {
            $plan->update([
                'status' => 'head_approved',
                'head_approved_by' => $currentUser->id,
            ]);
        }

        try {
            AuditLog::record(
                action: 'bed2.batch_head_approved',
                auditable: $plans->first(),
                newValues: ['count' => $plans->count(), 'year' => $year, 'operating_unit_id' => $ouId],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "{$plans->count()} target indicators endorsed and forwarded to PMO for final validation.");
    }

    /**
     * Batch approve and lock all plans for an Operating Unit & Year (by PMO).
     */
    public function batchPmoApprove(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->can('bed2.pmo_approve') && ! $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator'])) {
            abort(403, 'Only PMO Administrators or Evaluators can approve and lock baseline targets.');
        }

        $validated = $request->validate([
            'year' => ['required', 'integer'],
            'operating_unit_id' => ['required', 'exists:operating_units,id'],
        ]);

        $year = (int) $validated['year'];
        $ouId = (int) $validated['operating_unit_id'];

        $plans = Bed2Plan::where('operating_unit_id', $ouId)
            ->where('year', $year)
            ->whereIn('status', ['head_approved', 'submitted_to_head', 'draft'])
            ->get();

        if ($plans->isEmpty()) {
            return back()->with('error', 'No active plans found to approve for this operating unit.');
        }

        $unbalancedPlans = $plans->filter(fn (Bed2Plan $p) => ! $p->isBalanced());
        if ($unbalancedPlans->isNotEmpty()) {
            return back()->with('error', "Cannot approve baselines: {$unbalancedPlans->count()} plan(s) are unbalanced.");
        }

        foreach ($plans as $plan) {
            $plan->update([
                'status' => 'approved',
                'pmo_approved_by' => $currentUser->id,
            ]);
        }

        try {
            AuditLog::record(
                action: 'bed2.batch_pmo_approved',
                auditable: $plans->first(),
                newValues: ['count' => $plans->count(), 'year' => $year, 'operating_unit_id' => $ouId],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Annual target baseline for FY {$year} locked! {$plans->count()} indicators officially approved.");
    }

    /**
     * Batch return plans for revision (by OU Head or PMO).
     */
    public function batchReturn(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->can('bed2.return') && ! $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator', 'ou-head'])) {
            abort(403, 'Unauthorized to return target plans.');
        }

        $validated = $request->validate([
            'year' => ['nullable', 'integer'],
            'operating_unit_id' => ['nullable', 'exists:operating_units,id'],
            'remarks' => ['required', 'string', 'max:1000'],
            'plan_ids' => ['nullable', 'array'],
            'plan_ids.*' => ['integer', 'exists:bed2_plans,id'],
        ]);

        $year = ! empty($validated['year']) ? (int) $validated['year'] : null;
        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator']);
        $ouId = ($isPrivileged && ! empty($validated['operating_unit_id']))
            ? (int) $validated['operating_unit_id']
            : $currentUser->operating_unit_id;

        $stage = $currentUser->hasRole('ou-head') ? 'Division Chief' : 'PMO';
        $remarks = "Returned by {$stage}: {$validated['remarks']}";

        $query = Bed2Plan::whereIn('status', ['submitted_to_head', 'head_approved']);

        if ($ouId) {
            $query->where('operating_unit_id', $ouId);
        }

        if ($year) {
            $query->where('year', $year);
        }

        if (! empty($validated['plan_ids'])) {
            $query->whereIn('id', $validated['plan_ids']);
        }

        $plans = $query->get();

        if ($plans->isEmpty()) {
            return back()->with('error', 'No pending plans found to return for this operating unit.');
        }

        foreach ($plans as $plan) {
            $plan->update([
                'status' => 'returned',
                'remarks' => $remarks,
            ]);
        }

        try {
            AuditLog::record(
                action: 'bed2.batch_returned',
                auditable: $plans->first(),
                newValues: ['count' => $plans->count(), 'year' => $year, 'operating_unit_id' => $ouId, 'remarks' => $remarks],
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "{$plans->count()} target plan(s) returned for revision.");
    }

    /**
     * Display printable official DBM BED Form No. 2 (Physical Plan).
     */
    public function printDocument(Request $request): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $year = (int) $request->input('year', date('Y'));
        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator', 'higher-up']);

        if ($isPrivileged && $request->filled('operating_unit_id')) {
            $operatingUnit = OperatingUnit::findOrFail($request->input('operating_unit_id'));
        } elseif ($currentUser->operating_unit_id && strtoupper($currentUser->operatingUnit?->code ?? '') !== 'PMO') {
            $operatingUnit = OperatingUnit::findOrFail($currentUser->operating_unit_id);
        } else {
            $operatingUnit = OperatingUnit::coveredForReporting()->where('is_active', true)->firstOrFail();
        }

        $operatingUnit->load(['divisionChief', 'reportingOfficer']);

        $plans = Bed2Plan::with(['pap', 'performanceIndicator', 'submittedBy', 'headApprovedBy', 'pmoApprovedBy'])
            ->where('operating_unit_id', $operatingUnit->id)
            ->where('year', $year)
            ->get();

        // Group plans by PAP
        $plansByPap = $plans->groupBy('pap_id');

        $overallAnnual = $plans->sum('annual_target');
        $overallQ1 = $plans->sum('q1_target');
        $overallQ2 = $plans->sum('q2_target');
        $overallQ3 = $plans->sum('q3_target');
        $overallQ4 = $plans->sum('q4_target');

        // Extract latest signatory records if available
        $latestApprovedPlan = $plans->firstWhere('status', 'approved') ?? $plans->first();

        return view('bed2-plans.print', [
            'operatingUnit' => $operatingUnit,
            'year' => $year,
            'plans' => $plans,
            'plansByPap' => $plansByPap,
            'overallAnnual' => $overallAnnual,
            'overallQ1' => $overallQ1,
            'overallQ2' => $overallQ2,
            'overallQ3' => $overallQ3,
            'overallQ4' => $overallQ4,
            'latestApprovedPlan' => $latestApprovedPlan,
        ]);
    }

    /**
     * Export DBM BED-2 Physical Plan targets as CSV/Excel.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $year = (int) $request->input('year', date('Y'));
        $isPrivileged = $currentUser->can('bed2.manage_all_units') || $currentUser->hasAnyRole(['superadmin', 'admin', 'evaluator', 'higher-up']);

        if ($isPrivileged && $request->filled('operating_unit_id')) {
            $operatingUnit = OperatingUnit::findOrFail($request->input('operating_unit_id'));
        } elseif ($currentUser->operating_unit_id && strtoupper($currentUser->operatingUnit?->code ?? '') !== 'PMO') {
            $operatingUnit = OperatingUnit::findOrFail($currentUser->operating_unit_id);
        } else {
            $operatingUnit = OperatingUnit::coveredForReporting()->where('is_active', true)->firstOrFail();
        }

        $plans = Bed2Plan::with(['pap', 'performanceIndicator'])
            ->where('operating_unit_id', $operatingUnit->id)
            ->where('year', $year)
            ->orderBy('pap_id')
            ->get();

        $filename = "BED-2_Physical_Plan_{$operatingUnit->code}_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($plans, $operatingUnit, $year) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header information
            fputcsv($file, ['DBM BED FORM NO. 2 - PHYSICAL PLAN']);
            fputcsv($file, ['Department:', 'Department of Agriculture (DA)']);
            fputcsv($file, ['Agency:', 'Bureau of Plant Industry (BPI)']);
            fputcsv($file, ['Operating Unit:', "{$operatingUnit->name} ({$operatingUnit->code})"]);
            fputcsv($file, ['Fiscal Year:', $year]);
            fputcsv($file, []);

            // Column Headers
            fputcsv($file, [
                'Program / PAP Code',
                'Program / PAP Name',
                'Indicator Code',
                'Performance Indicator',
                'Type',
                'Unit of Measure',
                'Jan',
                'Feb',
                'Mar',
                'Q1 Total',
                'Apr',
                'May',
                'Jun',
                'Q2 Total',
                'Jul',
                'Aug',
                'Sep',
                'Q3 Total',
                'Oct',
                'Nov',
                'Dec',
                'Q4 Total',
                'Annual Physical Target',
                'Status',
                'Remarks',
            ]);

            foreach ($plans as $plan) {
                fputcsv($file, [
                    $plan->pap?->code,
                    $plan->pap?->name,
                    $plan->performanceIndicator?->code,
                    $plan->performanceIndicator?->name,
                    strtoupper($plan->performanceIndicator?->type ?? 'OUTPUT'),
                    $plan->performanceIndicator?->unit_of_measure,
                    $plan->m1_target,
                    $plan->m2_target,
                    $plan->m3_target,
                    $plan->q1_target,
                    $plan->m4_target,
                    $plan->m5_target,
                    $plan->m6_target,
                    $plan->q2_target,
                    $plan->m7_target,
                    $plan->m8_target,
                    $plan->m9_target,
                    $plan->q3_target,
                    $plan->m10_target,
                    $plan->m11_target,
                    $plan->m12_target,
                    $plan->q4_target,
                    $plan->annual_target,
                    $plan->statusLabel(),
                    $plan->remarks,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function authorizeView(Bed2Plan $bed2Plan): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->can('bed2.manage_all_units') || $user->hasAnyRole(['superadmin', 'admin', 'evaluator', 'higher-up'])) {
            return;
        }

        if ($bed2Plan->operating_unit_id !== $user->operating_unit_id) {
            abort(403, 'Unauthorized access to another Operating Unit target plan.');
        }
    }

    private function authorizeEdit(Bed2Plan $bed2Plan): void
    {
        /** @var User $user */
        $user = Auth::user();

        // PMO and users with manage_all_units or admin can edit anytime (even submitted or approved)
        if ($user->can('bed2.manage_all_units') || $user->hasAnyRole(['superadmin', 'admin', 'evaluator'])) {
            return;
        }

        if (! $bed2Plan->isEditable()) {
            abort(403, 'This plan is locked in review or has been approved, and cannot be modified.');
        }

        if ($bed2Plan->operating_unit_id !== $user->operating_unit_id) {
            abort(403, 'Unauthorized modification to another Operating Unit target plan.');
        }
    }
}
