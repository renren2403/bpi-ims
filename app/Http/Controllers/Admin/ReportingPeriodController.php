<?php
//ReportingPeriodController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportingPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportingPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = ReportingPeriod::withCount('monthlyReports')
            ->orderByDesc('year')
            ->orderByDesc('month');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }

        $periods = $query->paginate(12)->withQueryString();

        $activePeriod = ReportingPeriod::where('is_active', true)->first();
        $totalCount = ReportingPeriod::count();
        $openCount = ReportingPeriod::where('status', 'open')->count();
        $upcomingCount = ReportingPeriod::where('status', 'upcoming')->count();
        $closedCount = ReportingPeriod::where('status', 'closed')->count();

        // Available distinct years for filter
        $availableYears = ReportingPeriod::select('year')->distinct()->orderByDesc('year')->pluck('year');
        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int) date('Y'), (int) date('Y') - 1]);
        }

        return view('admin.reporting-periods.index', compact(
            'periods',
            'activePeriod',
            'totalCount',
            'openCount',
            'upcomingCount',
            'closedCount',
            'availableYears'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                Rule::unique('reporting_periods')->where(function ($query) use ($request) {
                    return $query->where('year', $request->input('year'));
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'window_start' => ['required', 'date'],
            'window_end' => ['required', 'date', 'after:window_start'],
            'status' => ['required', 'in:upcoming,open,closed'],
            'is_active' => ['nullable'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $isActive = $request->boolean('is_active');

        // Only one reporting period can be active at any given time
        if ($isActive) {
            ReportingPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        ReportingPeriod::create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'name' => $validated['name'],
            'window_start' => $validated['window_start'],
            'window_end' => $validated['window_end'],
            'is_active' => $isActive,
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('admin.reporting-periods.index')
            ->with('status', 'Reporting cycle period created successfully!');
    }

    public function update(Request $request, ReportingPeriod $reportingPeriod)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                Rule::unique('reporting_periods')
                    ->where(function ($query) use ($request) {
                        return $query->where('year', $request->input('year'));
                    })
                    ->ignore($reportingPeriod->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'window_start' => ['required', 'date'],
            'window_end' => ['required', 'date', 'after:window_start'],
            'status' => ['required', 'in:upcoming,open,closed'],
            'is_active' => ['nullable'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive && ! $reportingPeriod->is_active) {
            ReportingPeriod::where('id', '!=', $reportingPeriod->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $reportingPeriod->update([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'name' => $validated['name'],
            'window_start' => $validated['window_start'],
            'window_end' => $validated['window_end'],
            'is_active' => $isActive,
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()->route('admin.reporting-periods.index')
            ->with('status', "Reporting period '{$reportingPeriod->name}' updated successfully!");
    }

    public function toggleActive(ReportingPeriod $reportingPeriod)
    {
        if ($reportingPeriod->is_active) {
            $reportingPeriod->update([
                'is_active' => false,
                'status' => 'closed',
            ]);
            $message = "Reporting cycle '{$reportingPeriod->name}' was deactivated and locked.";
        } else {
            ReportingPeriod::where('id', '!=', $reportingPeriod->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $reportingPeriod->update([
                'is_active' => true,
                'status' => 'open',
            ]);
            $message = "Reporting cycle '{$reportingPeriod->name}' is now the official ACTIVE submission cycle!";
        }

        return redirect()->route('admin.reporting-periods.index')->with('status', $message);
    }

    public function destroy(ReportingPeriod $reportingPeriod)
    {
        if ($reportingPeriod->monthlyReports()->exists()) {
            return redirect()->route('admin.reporting-periods.index')
                ->withErrors(['error' => 'Cannot delete this reporting cycle because it already contains linked division monthly accomplishment submissions.']);
        }

        $name = $reportingPeriod->name;
        $reportingPeriod->delete();

        return redirect()->route('admin.reporting-periods.index')
            ->with('status', "Reporting cycle '{$name}' has been deleted.");
    }
}
