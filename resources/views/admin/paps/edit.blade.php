@extends('layouts.app')
@section('title', 'Manage Program: ' . $pap->code)

@section('content')
    <div class="space-y-6">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('admin.paps.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition">&larr; Back to Programs</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">{{ $pap->code }}</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $pap->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Category: <span class="font-bold text-slate-700">{{ $pap->category }}</span> &bull; Manage program details and assigned performance indicators.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.paps.toggle-status', $pap) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold border transition {{ $pap->is_active ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                        {{ $pap->is_active ? 'Deactivate Program' : 'Activate Program' }}
                    </button>
                </form>
                <form action="{{ route('admin.paps.destroy', $pap) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently remove this Program? All indicators must have zero BED-2 plans attached.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">
                        Delete Program
                    </button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-2xl flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <span class="text-emerald-500 font-extrabold">&check;</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-2xl flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <span class="text-rose-500 font-extrabold">&times;</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl space-y-1">
                <p class="font-extrabold">Please check the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Edit Program Metadata (1 col) -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm sticky top-6">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                        <span>Program Details</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $pap->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $pap->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </h3>

                    <form method="POST" action="{{ route('admin.paps.update', $pap) }}" class="space-y-4 text-xs">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">PAP / Line-Item Code <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $pap->code) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-bold uppercase transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                            <select name="category" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $pap->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $pap->name) }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description / Mandate</label>
                            <textarea name="description" rows="4"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">{{ old('description', $pap->description) }}</textarea>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pap->is_active) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <label for="is_active" class="font-bold text-slate-700">Available in BED-2 dropdowns</label>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition shadow-sm">
                                Save Program Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Performance Indicators Directory (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Add New Indicator Box -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Add Performance Indicator</h3>
                            <p class="text-xs text-slate-500">Add measurable output or outcome targets under {{ $pap->code }}.</p>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-xl">
                            {{ $pap->performanceIndicators->count() }} Registered
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.paps.indicators.store', $pap) }}" class="space-y-4 text-xs">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Indicator Code <span class="text-rose-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. PI-01 or OO-1" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-bold uppercase transition">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Type <span class="text-rose-500">*</span></label>
                                <select name="type" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                                    <option value="output" {{ old('type') == 'output' ? 'selected' : '' }}>Output Indicator</option>
                                    <option value="outcome" {{ old('type') == 'outcome' ? 'selected' : '' }}>Outcome Indicator</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unit of Measure <span class="text-rose-500">*</span></label>
                                <input type="text" name="unit_of_measure" value="{{ old('unit_of_measure') }}" placeholder="e.g. Permits issued, % compliance" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Indicator Statement / Description <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Number of phytosanitary import clearances inspected and validated" required
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" id="ind_active" value="1" checked
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <label for="ind_active" class="font-bold text-slate-700">Set active immediately</label>
                            </div>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition">
                                + Add Indicator
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Indicators Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Performance Indicators Roster</h3>
                            <p class="text-xs text-slate-500">All registered metrics assigned to this program line item.</p>
                        </div>
                    </div>

                    @if ($pap->performanceIndicators->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">No Indicators Added Yet</p>
                            <p class="text-xs text-slate-400 mt-1">Use the form above to add measurable outputs or outcomes for this program.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-extrabold uppercase tracking-wider">
                                        <th class="py-3 px-4">Code</th>
                                        <th class="py-3 px-4">Indicator Statement</th>
                                        <th class="py-3 px-4">Unit</th>
                                        <th class="py-3 px-4">Type</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($pap->performanceIndicators as $indicator)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-3 px-4">
                                                <span class="font-extrabold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $indicator->code }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-900 leading-snug">{{ $indicator->name }}</p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">{{ $indicator->unit_of_measure }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($indicator->type === 'output')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-200">Output</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide bg-purple-50 text-purple-700 border border-purple-200">Outcome</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($indicator->is_active)
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <!-- Edit Button triggers modal -->
                                                    <button type="button" 
                                                        onclick="openEditIndicatorModal({{ json_encode($indicator) }})"
                                                        class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                                                        title="Edit Indicator">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>

                                                    <!-- Toggle Status -->
                                                    <form action="{{ route('admin.indicators.toggle-status', $indicator) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                            class="p-1.5 {{ $indicator->is_active ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' }} rounded-lg transition"
                                                            title="{{ $indicator->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                        </button>
                                                    </form>

                                                    <!-- Delete -->
                                                    <form action="{{ route('admin.indicators.destroy', $indicator) }}" method="POST" onsubmit="return confirm('Delete indicator {{ $indicator->code }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete Indicator">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Indicator Modal -->
    <div id="editIndicatorModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 text-xs animate-in fade-in zoom-in duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Edit Indicator</h3>
                    <p class="text-xs text-slate-500">Update code, title, or measurement unit.</p>
                </div>
                <button type="button" onclick="closeEditIndicatorModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form id="editIndicatorForm" method="POST" action="" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Indicator Code <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit_code" name="code" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-bold uppercase transition">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Type <span class="text-rose-500">*</span></label>
                        <select id="edit_type" name="type" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                            <option value="output">Output Indicator</option>
                            <option value="outcome">Outcome Indicator</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unit of Measure <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_unit" name="unit_of_measure" required
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Indicator Statement / Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_name" name="name" required
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1"
                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="edit_is_active" class="font-bold text-slate-700">Active (available in BED-2 dropdowns)</label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditIndicatorModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition shadow-sm">
                        Update Indicator
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditIndicatorModal(indicator) {
            const modal = document.getElementById('editIndicatorModal');
            const form = document.getElementById('editIndicatorForm');
            form.action = `/admin/indicators/${indicator.id}`;
            document.getElementById('edit_code').value = indicator.code;
            document.getElementById('edit_type').value = indicator.type;
            document.getElementById('edit_unit').value = indicator.unit_of_measure;
            document.getElementById('edit_name').value = indicator.name;
            document.getElementById('edit_is_active').checked = Boolean(indicator.is_active);
            modal.classList.remove('hidden');
        }

        function closeEditIndicatorModal() {
            document.getElementById('editIndicatorModal').classList.add('hidden');
        }
    </script>
@endsection
