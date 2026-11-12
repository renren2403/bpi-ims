<?php

// OperatingUnitController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperatingUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = OperatingUnit::withCount(['users', 'monthlyReports']);
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $sort = $request->input('sort');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if (in_array($sort, ['name', 'code'], true)) {
            $query->orderBy($sort, $direction)
                ->orderBy($sort === 'name' ? 'code' : 'name', $direction);
        } else {
            $query->latest();
            $sort = null;
            $direction = null;
        }

        $operatingUnits = $query->paginate(15)->withQueryString();
        $totalCount = OperatingUnit::count();
        $activeCount = OperatingUnit::where('is_active', true)->count();
        $inactiveCount = OperatingUnit::where('is_active', false)->count();
        // Merge standard types with any legacy stored values for the filter dropdown.
        $types = collect(OperatingUnit::TYPES)
            ->merge(OperatingUnit::query()->whereNotNull('type')->distinct()->pluck('type'))
            ->unique()
            ->values();

        return view('admin.operating-units.index', compact(
            'operatingUnits',
            'totalCount',
            'activeCount',
            'inactiveCount',
            'types',
            'sort',
            'direction'
        ));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $classificationTypes = OperatingUnit::TYPES;

        return view('admin.operating-units.create', compact('users', 'classificationTypes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateOperatingUnit($request, true);
        OperatingUnit::create($validated);

        return redirect()->route('admin.operating-units.index')
            ->with('status', "Operating Unit '{$validated['name']}' has been successfully created!");
    }

    public function edit(OperatingUnit $operatingUnit)
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        $classificationTypes = OperatingUnit::TYPES;

        return view('admin.operating-units.edit', compact('operatingUnit', 'users', 'classificationTypes'));
    }

    public function update(Request $request, OperatingUnit $operatingUnit)
    {
        $validated = $this->validateOperatingUnit($request, false, $operatingUnit);
        $operatingUnit->update($validated);

        return redirect()->route('admin.operating-units.index')
            ->with('status', "Operating Unit '{$operatingUnit->name}' updated successfully!");
    }

    /**
     * Validate and normalize operating unit payload for creation or update.
     *
     * @return array<string, mixed>
     */
    private function validateOperatingUnit(Request $request, bool $defaultActive, ?OperatingUnit $operatingUnit = null): array
    {
        $this->normalizeCodeInput($request);

        $uniqueCodeRule = $operatingUnit !== null
            ? Rule::unique('operating_units', 'code')->ignore($operatingUnit->id)
            : 'unique:operating_units,code';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', $uniqueCodeRule],
            'type' => ['nullable', 'string', Rule::in(OperatingUnit::TYPES)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'division_chief_id' => ['nullable', 'exists:users,id'],
            'reporting_officer_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', $defaultActive);

        return $validated;
    }

    /**
     * Trim and uppercase the operating unit acronym before validation.
     */
    private function normalizeCodeInput(Request $request): void
    {
        $request->merge([
            'code' => mb_strtoupper(trim((string) $request->input('code'))),
        ]);
    }

    public function toggleStatus(OperatingUnit $operatingUnit)
    {
        $operatingUnit->update([
            'is_active' => ! $operatingUnit->is_active,
        ]);
        $statusText = $operatingUnit->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('status', "Operating Unit '{$operatingUnit->name}' has been {$statusText}.");
    }

    public function destroy(OperatingUnit $operatingUnit)
    {
        // Safe delete protection: prevent accidental deletion if unit has relations
        if ($operatingUnit->users()->exists() || $operatingUnit->monthlyReports()->exists() || $operatingUnit->bed2Plans()->exists()) {
            return redirect()->back()
                ->withErrors(['error' => "Cannot delete '{$operatingUnit->name}' because it has linked personnel accounts, monthly accomplishment reports, or BED-2 targets. Deactivate it instead."]);
        }
        $name = $operatingUnit->name;
        $operatingUnit->delete();

        return redirect()->route('admin.operating-units.index')
            ->with('status', "Operating Unit '{$name}' has been deleted.");
    }
}
