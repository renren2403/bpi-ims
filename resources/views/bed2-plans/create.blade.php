<!--create.blade.php BED-2 Target Plan-->
@extends('layouts.app')
@section('title', 'Encode BED-2 Target Plan')

@section('content')
    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Encode BED-2 Target Plan</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Commit annual physical indicator targets and allocate monthly/quarterly milestone breakdown.
                </p>
            </div>
            <a href="{{ route('bed2-plans.index', ['year' => $year]) }}"
                class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">
                &larr; Back to Matrix
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl space-y-1 shadow-sm">
                <p class="font-extrabold">Please correct the following inputs:</p>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bed2-plans.store') }}" id="bed2Form" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Scope & Program Identification -->
            <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">1. Indicator & Unit Assignment</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Define the operational jurisdiction, program, and performance indicator for this target baseline.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <!-- Operating Unit -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Operating Unit <span class="text-rose-500">*</span></label>
                        @hasanyrole('superadmin|admin|evaluator')
                            <select name="operating_unit_id" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                                @foreach ($operatingUnits as $ou)
                                    <option value="{{ $ou->id }}" {{ old('operating_unit_id', $defaultOuId) == $ou->id ? 'selected' : '' }}>
                                        {{ $ou->name }} ({{ $ou->code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="operating_unit_id" value="{{ $defaultOuId }}">
                            <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 font-bold text-slate-800">
                                {{ auth()->user()->operatingUnit?->name ?? 'Assigned Unit' }} ({{ auth()->user()->operatingUnit?->code ?? 'N/A' }})
                            </div>
                        @endhasanyrole
                    </div>

                    <!-- Fiscal Year -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fiscal Year <span class="text-rose-500">*</span></label>
                        <input type="number" name="year" value="{{ old('year', $year) }}" required min="2020" max="2050"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                    </div>

                    <!-- PAP Selector -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-bold text-slate-700 uppercase tracking-wider">Program / PAP <span class="text-rose-500">*</span></label>
                            <button type="button" onclick="openQuickPapModal()" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/60 px-2 py-0.5 rounded-lg transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>+ Quick Add</span>
                            </button>
                        </div>
                        <select name="pap_id" id="papSelect" onchange="onPapChange()" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                            <option value="">-- Select Program / Project --</option>
                            @foreach ($paps as $pap)
                                <option value="{{ $pap->id }}" {{ old('pap_id') == $pap->id ? 'selected' : '' }}>
                                    {{ $pap->code }} &bull; {{ Str::limit($pap->name, 35) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Indicator Picker -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider">Performance Indicator <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="openQuickIndicatorModal()" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/60 px-2 py-0.5 rounded-lg transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span>+ Quick Add</span>
                        </button>
                    </div>
                    <select name="performance_indicator_id" id="indicatorSelect" required onchange="onIndicatorChange()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                        <option value="">-- Select Performance Indicator --</option>
                    </select>
                    <div id="unitBadge" class="hidden mt-2 inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <span class="text-[10px] text-slate-400 uppercase font-bold">Unit of Measure:</span>
                        <span id="unitText" class="font-extrabold text-slate-800"></span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Physical Target Allocations -->
            <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">2. Physical Target Breakdown</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Enter the annual target commitment and distribute across monthly milestones.</p>
                    </div>

                    <!-- Shortcut Distribution Buttons -->
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-400 uppercase font-bold">Auto-Fill:</span>
                        <button type="button" onclick="distributeEvenly(12)"
                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-[11px] transition">
                            12 Months Evenly
                        </button>
                        <button type="button" onclick="distributeEvenly(4)"
                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-[11px] transition">
                            4 Quarters Evenly
                        </button>
                    </div>
                </div>

                <!-- Annual Target Master Input & Balance Counter -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-center p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                    <div>
                        <label class="block font-bold text-slate-800 uppercase tracking-wider mb-1">
                            Annual Physical Target Commitment <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" name="annual_target" id="annualTargetInput"
                                value="{{ old('annual_target', 0) }}" oninput="calculateTargets()" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white font-black text-base text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Live Balance Indicator -->
                    <div class="p-3 rounded-xl border bg-white flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Monthly Sum Allocated</p>
                            <p class="text-base font-extrabold text-slate-800" id="monthlySumText">0.00</p>
                        </div>
                        <div id="balancePill" class="px-3 py-1.5 rounded-xl text-xs font-extrabold flex items-center gap-1.5 bg-emerald-100 text-emerald-800">
                            ✓ Balanced
                        </div>
                    </div>
                </div>

                <!-- 4 Quarters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Q1 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px]">Quarter 1</h4>
                            <span class="font-mono font-extrabold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded text-[11px]" id="q1Subtotal">
                                0.00
                            </span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M1 &bull; January</label>
                                <input type="number" step="0.01" min="0" name="m1_target" value="{{ old('m1_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M2 &bull; February</label>
                                <input type="number" step="0.01" min="0" name="m2_target" value="{{ old('m2_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M3 &bull; March</label>
                                <input type="number" step="0.01" min="0" name="m3_target" value="{{ old('m3_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px]">Quarter 2</h4>
                            <span class="font-mono font-extrabold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded text-[11px]" id="q2Subtotal">
                                0.00
                            </span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M4 &bull; April</label>
                                <input type="number" step="0.01" min="0" name="m4_target" value="{{ old('m4_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M5 &bull; May</label>
                                <input type="number" step="0.01" min="0" name="m5_target" value="{{ old('m5_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M6 &bull; June</label>
                                <input type="number" step="0.01" min="0" name="m6_target" value="{{ old('m6_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px]">Quarter 3</h4>
                            <span class="font-mono font-extrabold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded text-[11px]" id="q3Subtotal">
                                0.00
                            </span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M7 &bull; July</label>
                                <input type="number" step="0.01" min="0" name="m7_target" value="{{ old('m7_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M8 &bull; August</label>
                                <input type="number" step="0.01" min="0" name="m8_target" value="{{ old('m8_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M9 &bull; September</label>
                                <input type="number" step="0.01" min="0" name="m9_target" value="{{ old('m9_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px]">Quarter 4</h4>
                            <span class="font-mono font-extrabold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded text-[11px]" id="q4Subtotal">
                                0.00
                            </span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M10 &bull; October</label>
                                <input type="number" step="0.01" min="0" name="m10_target" value="{{ old('m10_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M11 &bull; November</label>
                                <input type="number" step="0.01" min="0" name="m11_target" value="{{ old('m11_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M12 &bull; December</label>
                                <input type="number" step="0.01" min="0" name="m12_target" value="{{ old('m12_target', 0) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Encoding Remarks / Milestone Basis (Optional)</label>
                    <textarea name="remarks" rows="2" placeholder="e.g. Target calibrated against CY approved operational budget and historical accomplishment trends."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('bed2-plans.index', ['year' => $year]) }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save as Draft Target Plan
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Add Program (PAP) Modal -->
    <div id="quickPapModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 text-xs animate-in fade-in zoom-in duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Quick Add Program / PAP</h3>
                    <p class="text-xs text-slate-500">Register a new program line item without leaving this form.</p>
                </div>
                <button type="button" onclick="closeQuickPapModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <div id="quickPapErrors" class="hidden mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl"></div>

            <form id="quickPapForm" onsubmit="submitQuickPap(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program Code <span class="text-rose-500">*</span></label>
                        <input type="text" id="quick_pap_code" required placeholder="e.g. PAP-05"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-bold uppercase transition">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                        <select id="quick_pap_category" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program Title / Description <span class="text-rose-500">*</span></label>
                    <input type="text" id="quick_pap_name" required placeholder="e.g. National Plant Quarantine & Quality Protection"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Brief Mandate / Notes</label>
                    <textarea id="quick_pap_description" rows="2" placeholder="Optional background or objectives..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeQuickPapModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" id="quickPapSubmitBtn"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-sm flex items-center gap-1.5">
                        <span>Save & Select Program</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Add Indicator Modal -->
    <div id="quickIndicatorModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 text-xs animate-in fade-in zoom-in duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Quick Add Performance Indicator</h3>
                    <p class="text-xs text-slate-500">Register a new indicator under the selected program.</p>
                </div>
                <button type="button" onclick="closeQuickIndicatorModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <div id="quickIndErrors" class="hidden mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl"></div>

            <div class="mb-4 p-3 bg-slate-50 border border-slate-200/70 rounded-xl">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Target Program</p>
                <p id="quickIndPapTitle" class="font-black text-slate-800 text-xs mt-0.5"></p>
            </div>

            <form id="quickIndicatorForm" onsubmit="submitQuickIndicator(event)" class="space-y-4">
                <input type="hidden" id="quick_ind_pap_id">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Indicator Code <span class="text-rose-500">*</span></label>
                        <input type="text" id="quick_ind_code" required placeholder="e.g. PI-02 or OO-1"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-bold uppercase transition">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Type <span class="text-rose-500">*</span></label>
                        <select id="quick_ind_type" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                            <option value="output">Output Indicator</option>
                            <option value="outcome">Outcome Indicator</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unit of Measure <span class="text-rose-500">*</span></label>
                    <input type="text" id="quick_ind_unit" required placeholder="e.g. Permits issued, % samples passed"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Indicator Statement / Title <span class="text-rose-500">*</span></label>
                    <input type="text" id="quick_ind_name" required placeholder="e.g. Number of phytosanitary inspection certificates processed within standard time"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeQuickIndicatorModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" id="quickIndSubmitBtn"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-sm flex items-center gap-1.5">
                        <span>Save & Select Indicator</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Dynamic Indicator Filtering, Modals, and Live Math -->
    <script>
        let papsData = @json($paps);
        const oldIndicatorId = @json(old('performance_indicator_id'));

        function onPapChange() {
            const papId = document.getElementById('papSelect').value;
            const indicatorSelect = document.getElementById('indicatorSelect');
            indicatorSelect.innerHTML = '<option value="">-- Select Performance Indicator --</option>';

            const unitBadge = document.getElementById('unitBadge');
            unitBadge.classList.add('hidden');

            if (!papId) return;

            const selectedPap = papsData.find(p => p.id == papId);
            if (selectedPap && selectedPap.performance_indicators) {
                selectedPap.performance_indicators.forEach(ind => {
                    const opt = document.createElement('option');
                    opt.value = ind.id;
                    opt.textContent = `${ind.code} - ${ind.name} (${ind.unit_of_measure})`;
                    opt.dataset.unit = ind.unit_of_measure;
                    if (oldIndicatorId && oldIndicatorId == ind.id) {
                        opt.selected = true;
                    }
                    indicatorSelect.appendChild(opt);
                });
            }

            onIndicatorChange();
        }

        function onIndicatorChange() {
            const select = document.getElementById('indicatorSelect');
            const unitBadge = document.getElementById('unitBadge');
            const unitText = document.getElementById('unitText');
            const selectedOpt = select.options[select.selectedIndex];

            if (selectedOpt && selectedOpt.dataset.unit) {
                unitText.textContent = selectedOpt.dataset.unit;
                unitBadge.classList.remove('hidden');
            } else {
                unitBadge.classList.add('hidden');
            }
        }

        /* --- Quick Add Program (PAP) Logic --- */
        function openQuickPapModal() {
            document.getElementById('quickPapErrors').classList.add('hidden');
            document.getElementById('quick_pap_code').value = '';
            document.getElementById('quick_pap_name').value = '';
            document.getElementById('quick_pap_description').value = '';
            document.getElementById('quickPapModal').classList.remove('hidden');
        }

        function closeQuickPapModal() {
            document.getElementById('quickPapModal').classList.add('hidden');
        }

        async function submitQuickPap(e) {
            e.preventDefault();
            const btn = document.getElementById('quickPapSubmitBtn');
            const errBox = document.getElementById('quickPapErrors');
            errBox.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            const payload = {
                code: document.getElementById('quick_pap_code').value.trim(),
                category: document.getElementById('quick_pap_category').value,
                name: document.getElementById('quick_pap_name').value.trim(),
                description: document.getElementById('quick_pap_description').value.trim(),
            };

            try {
                const res = await fetch('{{ route('paps.quick-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok) {
                    let msg = data.message || 'Error creating program.';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join('<br>');
                    }
                    errBox.innerHTML = msg;
                    errBox.classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = 'Save & Select Program';
                    return;
                }

                // Append new PAP to papsData
                const newPap = { ...data.pap, performance_indicators: [] };
                papsData.push(newPap);

                // Add to select
                const papSelect = document.getElementById('papSelect');
                const opt = document.createElement('option');
                opt.value = newPap.id;
                opt.textContent = `${newPap.code} • ${newPap.name}`;
                papSelect.appendChild(opt);
                papSelect.value = newPap.id;

                closeQuickPapModal();
                onPapChange();
            } catch (err) {
                errBox.textContent = 'Network or server error occurred. Please try again.';
                errBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Save & Select Program';
            }
        }

        /* --- Quick Add Indicator Logic --- */
        function openQuickIndicatorModal() {
            const papSelect = document.getElementById('papSelect');
            if (!papSelect.value) {
                alert('Please select or add a Program / PAP first before creating an indicator.');
                return;
            }

            const selectedPap = papsData.find(p => p.id == papSelect.value);
            document.getElementById('quick_ind_pap_id').value = papSelect.value;
            document.getElementById('quickIndPapTitle').textContent = selectedPap ? `${selectedPap.code} - ${selectedPap.name}` : 'Selected Program';

            document.getElementById('quickIndErrors').classList.add('hidden');
            document.getElementById('quick_ind_code').value = '';
            document.getElementById('quick_ind_name').value = '';
            document.getElementById('quick_ind_unit').value = '';
            document.getElementById('quickIndicatorModal').classList.remove('hidden');
        }

        function closeQuickIndicatorModal() {
            document.getElementById('quickIndicatorModal').classList.add('hidden');
        }

        async function submitQuickIndicator(e) {
            e.preventDefault();
            const btn = document.getElementById('quickIndSubmitBtn');
            const errBox = document.getElementById('quickIndErrors');
            errBox.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            const payload = {
                pap_id: document.getElementById('quick_ind_pap_id').value,
                code: document.getElementById('quick_ind_code').value.trim(),
                type: document.getElementById('quick_ind_type').value,
                unit_of_measure: document.getElementById('quick_ind_unit').value.trim(),
                name: document.getElementById('quick_ind_name').value.trim(),
            };

            try {
                const res = await fetch('{{ route('indicators.quick-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok) {
                    let msg = data.message || 'Error creating indicator.';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join('<br>');
                    }
                    errBox.innerHTML = msg;
                    errBox.classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = 'Save & Select Indicator';
                    return;
                }

                // Add to PAP's indicators in memory
                const pap = papsData.find(p => p.id == payload.pap_id);
                if (pap) {
                    pap.performance_indicators = pap.performance_indicators || [];
                    pap.performance_indicators.push(data.indicator);
                }

                // Re-render indicator select and choose the new one
                onPapChange();
                const indicatorSelect = document.getElementById('indicatorSelect');
                indicatorSelect.value = data.indicator.id;
                onIndicatorChange();

                closeQuickIndicatorModal();
            } catch (err) {
                errBox.textContent = 'Network or server error occurred. Please try again.';
                errBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Save & Select Indicator';
            }
        }

        function getMonthVal(m) {
            const el = document.querySelector(`input[name="m${m}_target"]`);
            return el ? (parseFloat(el.value) || 0) : 0;
        }

        function calculateTargets() {
            const m1 = getMonthVal(1), m2 = getMonthVal(2), m3 = getMonthVal(3);
            const m4 = getMonthVal(4), m5 = getMonthVal(5), m6 = getMonthVal(6);
            const m7 = getMonthVal(7), m8 = getMonthVal(8), m9 = getMonthVal(9);
            const m10 = getMonthVal(10), m11 = getMonthVal(11), m12 = getMonthVal(12);

            const q1 = m1 + m2 + m3;
            const q2 = m4 + m5 + m6;
            const q3 = m7 + m8 + m9;
            const q4 = m10 + m11 + m12;
            const total = q1 + q2 + q3 + q4;

            document.getElementById('q1Subtotal').textContent = q1.toFixed(2);
            document.getElementById('q2Subtotal').textContent = q2.toFixed(2);
            document.getElementById('q3Subtotal').textContent = q3.toFixed(2);
            document.getElementById('q4Subtotal').textContent = q4.toFixed(2);

            document.getElementById('monthlySumText').textContent = total.toFixed(2);

            const annualInput = document.getElementById('annualTargetInput');
            const annual = parseFloat(annualInput.value) || 0;
            const diff = Math.abs(annual - total);
            const balancePill = document.getElementById('balancePill');

            if (diff < 0.01 && annual > 0) {
                balancePill.className = 'px-3 py-1.5 rounded-xl text-xs font-extrabold flex items-center gap-1.5 bg-emerald-100 text-emerald-800';
                balancePill.textContent = '✓ Balanced (100% Allocated)';
            } else if (annual === 0 && total === 0) {
                balancePill.className = 'px-3 py-1.5 rounded-xl text-xs font-extrabold flex items-center gap-1.5 bg-slate-100 text-slate-600';
                balancePill.textContent = 'Enter Target';
            } else {
                const remainder = (annual - total).toFixed(2);
                balancePill.className = 'px-3 py-1.5 rounded-xl text-xs font-extrabold flex items-center gap-1.5 bg-amber-100 text-amber-900';
                balancePill.textContent = `⚠️ Unbalanced (${remainder > 0 ? '+' : ''}${remainder})`;
            }
        }

        function distributeEvenly(parts) {
            const annual = parseFloat(document.getElementById('annualTargetInput').value) || 0;
            if (annual <= 0) {
                alert('Please enter an Annual Physical Target first.');
                return;
            }

            if (parts === 12) {
                const perMonth = Math.floor((annual / 12) * 100) / 100;
                let sum = perMonth * 12;
                let remainder = Math.round((annual - sum) * 100) / 100;

                for (let i = 1; i <= 12; i++) {
                    const el = document.querySelector(`input[name="m${i}_target"]`);
                    if (el) {
                        let val = perMonth;
                        if (i === 12) {
                            val = Math.round((val + remainder) * 100) / 100;
                        }
                        el.value = val.toFixed(2);
                    }
                }
            } else if (parts === 4) {
                const perQuarter = Math.floor((annual / 4) * 100) / 100;
                let remainder = Math.round((annual - (perQuarter * 4)) * 100) / 100;

                // Clear all
                for (let i = 1; i <= 12; i++) {
                    const el = document.querySelector(`input[name="m${i}_target"]`);
                    if (el) el.value = '0.00';
                }

                // Allocate at end of each quarter (M3, M6, M9, M12)
                const quarterMonths = [3, 6, 9, 12];
                quarterMonths.forEach((m, idx) => {
                    const el = document.querySelector(`input[name="m${m}_target"]`);
                    let val = perQuarter;
                    if (idx === 3) val = Math.round((val + remainder) * 100) / 100;
                    if (el) el.value = val.toFixed(2);
                });
            }

            calculateTargets();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            onPapChange();
            calculateTargets();
        });
    </script>
@endsection
