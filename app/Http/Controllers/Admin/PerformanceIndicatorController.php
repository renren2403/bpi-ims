<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PAP;
use App\Models\PerformanceIndicator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class PerformanceIndicatorController extends Controller
{
    /**
     * Store a newly created Performance Indicator under the given PAP.
     */
    public function store(Request $request, PAP $pap): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:500'],
            'unit_of_measure' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:output,outcome'],
            'is_active' => ['boolean'],
        ]);

        $indicator = $pap->performanceIndicators()->create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'unit_of_measure' => trim($validated['unit_of_measure']),
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        try {
            AuditLog::record(
                action: 'indicator.created',
                auditable: $indicator,
                newValues: $indicator->only(['pap_id', 'code', 'name', 'unit_of_measure', 'type']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Performance Indicator {$indicator->code} added successfully.");
    }

    /**
     * Update the specified Performance Indicator in storage.
     */
    public function update(Request $request, PerformanceIndicator $indicator): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:500'],
            'unit_of_measure' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:output,outcome'],
            'is_active' => ['boolean'],
        ]);

        $oldValues = $indicator->only(['code', 'name', 'unit_of_measure', 'type', 'is_active']);

        $indicator->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'unit_of_measure' => trim($validated['unit_of_measure']),
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active'),
        ]);

        try {
            AuditLog::record(
                action: 'indicator.updated',
                auditable: $indicator,
                oldValues: $oldValues,
                newValues: $indicator->only(['code', 'name', 'unit_of_measure', 'type', 'is_active']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Performance Indicator {$indicator->code} updated successfully.");
    }

    /**
     * Toggle the active status of the Performance Indicator.
     */
    public function toggleStatus(PerformanceIndicator $indicator): RedirectResponse
    {
        $indicator->is_active = ! $indicator->is_active;
        $indicator->save();

        $state = $indicator->is_active ? 'activated' : 'deactivated';

        try {
            AuditLog::record(
                action: 'indicator.status_toggled',
                auditable: $indicator,
                newValues: ['is_active' => $indicator->is_active],
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', "Performance Indicator {$indicator->code} has been {$state}.");
    }

    /**
     * Remove the specified Performance Indicator from storage.
     */
    public function destroy(PerformanceIndicator $indicator): RedirectResponse
    {
        if ($indicator->bed2Plans()->exists()) {
            return back()->with('error', "Cannot delete {$indicator->code} because it has active BED-2 Target Plans attached. Please deactivate it instead.");
        }

        $code = $indicator->code;

        try {
            AuditLog::record(
                action: 'indicator.deleted',
                auditable: $indicator,
                oldValues: $indicator->only(['pap_id', 'code', 'name']),
                request: request()
            );
        } catch (Throwable $e) {
            report($e);
        }

        $indicator->delete();

        return back()->with('status', "Performance Indicator {$code} removed successfully.");
    }

    /**
     * Quick-add endpoint (JSON API) for inline creation from the BED-2 form.
     */
    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pap_id' => ['required', 'exists:paps,id'],
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:500'],
            'unit_of_measure' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:output,outcome'],
        ]);

        $indicator = PerformanceIndicator::create([
            'pap_id' => $validated['pap_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'unit_of_measure' => trim($validated['unit_of_measure']),
            'type' => $validated['type'],
            'is_active' => true,
        ]);

        try {
            AuditLog::record(
                action: 'indicator.quick_created',
                auditable: $indicator,
                newValues: $indicator->only(['pap_id', 'code', 'name', 'unit_of_measure', 'type']),
                request: $request
            );
        } catch (Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'indicator' => $indicator,
            'message' => "Performance Indicator {$indicator->code} registered successfully!",
        ], 201);
    }
}
