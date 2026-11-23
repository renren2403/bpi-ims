<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PAPController extends Controller
{
    /**
     * Display a listing of Programs / PAPs with indicator counts.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = PAP::withCount('performanceIndicators')->orderBy('code');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $paps = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_paps' => PAP::count(),
            'active_paps' => PAP::where('is_active', true)->count(),
            'total_indicators' => PerformanceIndicator::count(),
            'output_indicators' => PerformanceIndicator::where('type', 'output')->count(),
            'outcome_indicators' => PerformanceIndicator::where('type', 'outcome')->count(),
        ];

        $categories = PAP::CATEGORIES;

        return view('admin.paps.index', compact('paps', 'metrics', 'categories'));
    }

    /**
     * Show the form for creating a new PAP.
     */
    public function create(): View
    {
        $this->authorizeAccess();

        $categories = PAP::CATEGORIES;

        return view('admin.paps.create', compact('categories'));
    }

    /**
     * Store a newly created PAP in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:paps,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $pap = PAP::create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'category' => $validated['category'] ?? 'Operations',
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        try {
            AuditLog::record(
                action: 'pap.created',
                auditable: $pap,
                newValues: $pap->only(['code', 'name', 'category', 'is_active']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()->route('admin.paps.edit', $pap)
            ->with('status', "Program {$pap->code} created successfully! You can now add performance indicators.");
    }

    /**
     * Show the form for editing the specified PAP and its indicators.
     */
    public function edit(PAP $pap): View
    {
        $this->authorizeAccess();

        $pap->load(['performanceIndicators' => function ($q) {
            $q->orderBy('code');
        }]);

        $categories = PAP::CATEGORIES;

        return view('admin.paps.edit', compact('pap', 'categories'));
    }

    /**
     * Update the specified PAP in storage.
     */
    public function update(Request $request, PAP $pap): RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:paps,code,'.$pap->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $pap->only(['code', 'name', 'category', 'description', 'is_active']);

        $pap->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'category' => $validated['category'] ?? $pap->category,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        try {
            AuditLog::record(
                action: 'pap.updated',
                auditable: $pap,
                oldValues: $oldValues,
                newValues: $pap->only(['code', 'name', 'category', 'description', 'is_active']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Program {$pap->code} updated successfully.");
    }

    /**
     * Toggle the active status of the PAP.
     */
    public function toggleStatus(PAP $pap): RedirectResponse
    {
        $this->authorizeAccess();

        $pap->is_active = ! $pap->is_active;
        $pap->save();

        $state = $pap->is_active ? 'activated' : 'deactivated';

        try {
            AuditLog::record(
                action: 'pap.status_toggled',
                auditable: $pap,
                newValues: ['is_active' => $pap->is_active],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Program {$pap->code} has been {$state}.");
    }

    /**
     * Remove the specified PAP from storage.
     */
    public function destroy(PAP $pap): RedirectResponse
    {
        $this->authorizeAccess();

        if ($pap->bed2Plans()->exists()) {
            return back()->with('error', "Cannot delete {$pap->code} because it has active BED-2 Target Plans attached. Please deactivate it instead.");
        }

        $code = $pap->code;

        try {
            AuditLog::record(
                action: 'pap.deleted',
                auditable: $pap,
                oldValues: $pap->only(['code', 'name']),
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        $pap->delete();

        return redirect()->route('admin.paps.index')
            ->with('status', "Program {$code} and its indicators have been removed.");
    }

    /**
     * Quick-add endpoint (JSON API) for inline creation from the BED-2 form.
     */
    public function quickStore(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:paps,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $pap = PAP::create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'category' => $validated['category'] ?? 'Operations',
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        try {
            AuditLog::record(
                action: 'pap.quick_created',
                auditable: $pap,
                newValues: $pap->only(['code', 'name', 'category']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'pap' => $pap,
            'message' => "Program {$pap->code} registered successfully!",
        ], 201);
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->can('paps.view') && ! $user->can('paps.create') && ! $user->hasAnyRole(['superadmin', 'admin', 'evaluator']))) {
            abort(403, 'Unauthorized access to Programs and PAPs.');
        }
    }
}
