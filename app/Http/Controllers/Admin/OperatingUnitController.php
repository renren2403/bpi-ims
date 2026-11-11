<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperatingUnitController extends Controller {
public function index(Request $request){
$query = OperatingUnit::withCount(['users','monthlyReports'])->latest();
if ($search = $request->input('search')){
$query->where(function ($q) use ($search) {
    $q->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%");
});    
}
if ($type = $request->input('type')){
    $query->where('type',$type);
}
if ($request->has('status') && $request->input('status') !== ''){
    $query->where('is_active', $request->boolean('status'));
}
$operatingUnits = $query->paginate(15)->withQueryString();
$totalCount = OperatingUnit::count();
$activeCount = OperatingUnit::where('is_active', true)->count();
$inactiveCount = OperatingUnit::where('is_active', false)->count();
//distinct types for filtering
$types = OperatingUnit::select('type')->whereNotNull('type')->distinct()->pluck('type');

return view('admin.operating-units.index',compact(
    'operatingUnits',
    'totalCount',
    'activeCount',
    'inactiveCount',
    'types'
));
}
public function create()
{
    $users = \App\Models\User::where('is_active', true)->orderBy('name')->get();
    return view('admin.operating-units.create', compact('users'));
}

public function store(Request $request)
{

 $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:operating_units,code'],
            'type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'division_chief_id' => ['nullable', 'exists:users,id'],
            'reporting_officer_id' => ['nullable', 'exists:users,id'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        OperatingUnit::create($validated);
        return redirect()->route('admin.operating-units.index')
            ->with('status', "Operating Unit '{$validated['name']}' has been successfully created!");
    }
    public function edit(OperatingUnit $operatingUnit)
    { 
        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get();
        return view('admin.operating-units.edit', compact('operatingUnit','users'));
        }
    public function update(Request $request, OperatingUnit $operatingUnit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('operating_units', 'code')->ignore($operatingUnit->id)],
            'type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'division_chief_id' => ['nullable', 'exists:users,id'],
            'reporting_officer_id' => ['nullable', 'exists:users,id'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', false);
        $operatingUnit->update($validated);
        return redirect()->route('admin.operating-units.index')
            ->with('status', "Operating Unit '{$operatingUnit->name}' updated successfully!");
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