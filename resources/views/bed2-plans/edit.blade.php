<!--edit.blade.php BED-2 Target Plan-->
@extends('layouts.app')
@section('title', 'Revise BED-2 Target Plan')

@section('content')
    <div class="max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Revise BED-2 Target Plan</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border uppercase tracking-wider {{ $bed2Plan->statusBadgeClass() }}">
                        {{ $bed2Plan->statusLabel() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Update annual physical indicator commitments and adjust monthly/quarterly milestone breakdown.
                </p>
            </div>
            <a href="{{ route('bed2-plans.show', $bed2Plan) }}"
                class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">
                &larr; Back to Dossier
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

        @if ($bed2Plan->isReturned() && $bed2Plan->remarks)
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs space-y-1 shadow-sm">
                <p class="font-extrabold flex items-center gap-1.5 text-rose-900">
                    <span>⚠️ Returned for Revision:</span>
                </p>
                <p class="font-medium text-rose-700 pl-4">{{ $bed2Plan->remarks }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('bed2-plans.update', $bed2Plan) }}" id="bed2Form" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- Section 1: Scope & Program Identification (Locked) -->
            <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Target Scope & Assignment</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Locked parameters for this established target plan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Operating Unit</p>
                        <p class="font-bold text-slate-900 mt-1 text-sm">{{ $bed2Plan->operatingUnit?->name }} ({{ $bed2Plan->operatingUnit?->code }})</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Fiscal Year</p>
                        <p class="font-bold text-slate-900 mt-1 text-sm">FY {{ $bed2Plan->year }}</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Unit of Measure</p>
                        <p class="font-bold text-emerald-800 mt-1 text-sm">{{ $bed2Plan->performanceIndicator?->unit_of_measure }}</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-200 text-slate-800">{{ $bed2Plan->pap?->code }}</span>
                        <span class="font-extrabold text-slate-800">{{ $bed2Plan->pap?->name }}</span>
                    </div>
                    <p class="font-bold text-slate-900 text-sm mt-1">
                        {{ $bed2Plan->performanceIndicator?->code }} &bull; {{ $bed2Plan->performanceIndicator?->name }}
                    </p>
                </div>
            </div>

            <!-- Section 2: Physical Target Allocations -->
            <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Physical Target Breakdown</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Adjust annual commitment and monthly milestones.</p>
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
                                value="{{ old('annual_target', $bed2Plan->annual_target) }}" oninput="calculateTargets()" required
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
                                <input type="number" step="0.01" min="0" name="m1_target" value="{{ old('m1_target', $bed2Plan->m1_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M2 &bull; February</label>
                                <input type="number" step="0.01" min="0" name="m2_target" value="{{ old('m2_target', $bed2Plan->m2_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M3 &bull; March</label>
                                <input type="number" step="0.01" min="0" name="m3_target" value="{{ old('m3_target', $bed2Plan->m3_target) }}"
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
                                <input type="number" step="0.01" min="0" name="m4_target" value="{{ old('m4_target', $bed2Plan->m4_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M5 &bull; May</label>
                                <input type="number" step="0.01" min="0" name="m5_target" value="{{ old('m5_target', $bed2Plan->m5_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M6 &bull; June</label>
                                <input type="number" step="0.01" min="0" name="m6_target" value="{{ old('m6_target', $bed2Plan->m6_target) }}"
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
                                <input type="number" step="0.01" min="0" name="m7_target" value="{{ old('m7_target', $bed2Plan->m7_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M8 &bull; August</label>
                                <input type="number" step="0.01" min="0" name="m8_target" value="{{ old('m8_target', $bed2Plan->m8_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M9 &bull; September</label>
                                <input type="number" step="0.01" min="0" name="m9_target" value="{{ old('m9_target', $bed2Plan->m9_target) }}"
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
                                <input type="number" step="0.01" min="0" name="m10_target" value="{{ old('m10_target', $bed2Plan->m10_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M11 &bull; November</label>
                                <input type="number" step="0.01" min="0" name="m11_target" value="{{ old('m11_target', $bed2Plan->m11_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">M12 &bull; December</label>
                                <input type="number" step="0.01" min="0" name="m12_target" value="{{ old('m12_target', $bed2Plan->m12_target) }}"
                                    class="month-input w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold" oninput="calculateTargets()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Revision Justification / Notes</label>
                    <textarea name="remarks" rows="2" placeholder="Document the reason for adjustments made to this target baseline."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">{{ old('remarks', $bed2Plan->remarks) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('bed2-plans.show', $bed2Plan) }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Target Plan Updates
                </button>
            </div>
        </form>
    </div>

    <!-- JavaScript for Live Math -->
    <script>
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

                for (let i = 1; i <= 12; i++) {
                    const el = document.querySelector(`input[name="m${i}_target"]`);
                    if (el) el.value = '0.00';
                }

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

        document.addEventListener('DOMContentLoaded', () => {
            calculateTargets();
        });
    </script>
@endsection
