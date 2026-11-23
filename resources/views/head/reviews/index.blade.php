@extends('layouts.app')
@section('title', 'Division Review & Endorsements')

@section('content')
    <div class="space-y-6">

        <!-- HERO HEADER -->
        <div
            class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between relative z-10">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                    <span>🏛️</span> Division Chief Action Center &bull; {{ $operatingUnit?->name ?? 'Operating Unit' }}
                </div>
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                    <span class="text-slate-300">Fiscal Year:</span>
                    <span class="font-bold text-emerald-400">FY {{ $selectedYear }}</span>
                    @if ($activeCycle)
                        <span class="text-slate-400">&bull;</span>
                        <span class="text-white font-medium">{{ $activeCycle->name }}</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">Division Submissions for Review</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Verify annual physical targets and monthly milestone distributions submitted by your division encoders.
                        Endorsed plans are forwarded directly to the PMO for official baseline approval.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Awaiting Endorsement</p>
                        <p class="text-base font-extrabold text-amber-400">{{ $metrics['pending'] }} Indicators</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Endorsed to PMO</p>
                        <p class="text-base font-extrabold text-emerald-400">{{ $metrics['endorsed'] }} Plans</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS TABS & METRIC PILLS -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 pb-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('head.reviews.index', array_merge(request()->query(), ['status' => 'submitted_to_head'])) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $statusTab === 'submitted_to_head' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Pending My Review</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $statusTab === 'submitted_to_head' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                        {{ $metrics['pending'] }}
                    </span>
                </a>

                <a href="{{ route('head.reviews.index', array_merge(request()->query(), ['status' => 'head_approved'])) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $statusTab === 'head_approved' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Endorsed to PMO</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $statusTab === 'head_approved' ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                        {{ $metrics['endorsed'] }}
                    </span>
                </a>

                <a href="{{ route('head.reviews.index', array_merge(request()->query(), ['status' => 'approved'])) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $statusTab === 'approved' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Official Baseline</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $statusTab === 'approved' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                        {{ $metrics['approved'] }}
                    </span>
                </a>

                <a href="{{ route('head.reviews.index', array_merge(request()->query(), ['status' => 'returned'])) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $statusTab === 'returned' ? 'bg-rose-600 text-white shadow-sm shadow-rose-600/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Returned for Revision</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $statusTab === 'returned' ? 'bg-rose-700 text-white' : 'bg-slate-100 text-slate-700' }}">
                        {{ $metrics['returned'] }}
                    </span>
                </a>

                <a href="{{ route('head.reviews.index', array_merge(request()->query(), ['status' => 'all'])) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $statusTab === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>All Plans</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $statusTab === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }}">
                        {{ $metrics['total'] }}
                    </span>
                </a>
            </div>

            <!-- YEAR FILTER -->
            <form action="{{ route('head.reviews.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusTab }}">
                <label for="year" class="text-xs font-bold text-slate-500">Year:</label>
                <select name="year" id="year" onchange="this.form.submit()"
                    class="rounded-xl border-slate-200 text-xs font-bold py-1.5 pl-3 pr-8 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $yr === $selectedYear ? 'selected' : '' }}>FY {{ $yr }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- BATCH ACTIONS & SEARCH CONSOLE -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5">
                @if ($statusTab === 'submitted_to_head' && $plans->total() > 0)
                    <button type="button" id="btn-batch-endorse"
                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Endorse Selected (<span class="selected-count">0</span>)</span>
                    </button>

                    <button type="button" id="btn-batch-return"
                        class="px-3.5 py-2 rounded-xl bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-extrabold text-xs transition flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Return Selected (<span class="selected-count">0</span>)</span>
                    </button>
                @endif

                <span class="text-xs text-slate-500 font-medium">
                    Showing <strong>{{ $plans->count() }}</strong> of <strong>{{ $plans->total() }}</strong> records
                </span>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('head.reviews.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $statusTab }}">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <select name="pap_id" onchange="this.form.submit()"
                        class="rounded-xl border-slate-200 text-xs font-semibold py-1.5 pl-3 pr-8 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                        <option value="">All Programs / PAPs</option>
                        @foreach ($paps as $pap)
                            <option value="{{ $pap->id }}" {{ request('pap_id') == $pap->id ? 'selected' : '' }}>
                                {{ $pap->code }} &bull; {{ $pap->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- PLANS REVIEW TABLE -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                        <tr>
                            @if ($statusTab === 'submitted_to_head')
                                <th class="p-4 w-10 text-center">
                                    <input type="checkbox" id="select-all-plans"
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                </th>
                            @endif
                            <th class="p-4">PAP & Performance Indicator</th>
                            <th class="p-4 text-center">Annual Target</th>
                            <th class="p-4 text-center">Q1</th>
                            <th class="p-4 text-center">Q2</th>
                            <th class="p-4 text-center">Q3</th>
                            <th class="p-4 text-center">Q4</th>
                            <th class="p-4">Submission Info</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($plans as $plan)
                            <tr class="hover:bg-slate-50/60 transition {{ $plan->status === 'returned' ? 'bg-rose-50/20' : '' }}">
                                @if ($statusTab === 'submitted_to_head')
                                    <td class="p-4 text-center">
                                        <input type="checkbox" name="selected_plans[]" value="{{ $plan->id }}"
                                            class="plan-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    </td>
                                @endif
                                <td class="p-4 max-w-sm">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                            {{ $plan->pap?->code }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-semibold truncate">{{ $plan->pap?->name }}</span>
                                    </div>
                                    <p class="font-bold text-slate-800 mt-1 line-clamp-2">
                                        {{ $plan->performanceIndicator?->name }}
                                    </p>
                                    @if ($plan->remarks)
                                        <div class="mt-1 p-1.5 rounded-lg bg-rose-50 border border-rose-200/60 text-[11px] text-rose-700 italic">
                                            Remarks: {{ $plan->remarks }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4 text-center font-extrabold text-slate-900 text-sm">
                                    {{ number_format($plan->annual_target, 2) }}
                                </td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($plan->q1_target, 2) }}</td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($plan->q2_target, 2) }}</td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($plan->q3_target, 2) }}</td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($plan->q4_target, 2) }}</td>
                                <td class="p-4 text-slate-600">
                                    <div class="font-bold text-slate-800">{{ $plan->submittedBy?->name ?? 'Staff Encoder' }}</div>
                                    <span class="text-[10px] text-slate-400">{{ $plan->updated_at->format('M d, Y g:i A') }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $plan->statusBadgeClass() }}">
                                        {{ $plan->statusLabel() }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if ($plan->status === 'submitted_to_head')
                                            @if ($plan->isBalanced())
                                                <!-- Endorse to PMO Form -->
                                                <form action="{{ route('bed2-plans.head-approve', $plan) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition"
                                                        title="Endorse to PMO">
                                                        Endorse
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" disabled
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 font-bold text-xs cursor-not-allowed"
                                                    title="Target allocation is unbalanced. Please return to encoder for revision.">
                                                    Unbalanced
                                                </button>
                                            @endif

                                            <!-- Return Button -->
                                            <button type="button"
                                                onclick="openReturnModal({{ $plan->id }}, '{{ addslashes($plan->performanceIndicator?->name ?? 'Indicator') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-xs transition"
                                                title="Return for revision">
                                                Return
                                            </button>
                                        @endif

                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition"
                                            title="Edit target figures">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <h4 class="font-extrabold text-sm text-slate-800">All Caught Up!</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        There are currently no target baseline submissions in this stage awaiting your action.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($plans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $plans->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- RETURN SINGLE PLAN MODAL -->
    <div id="return-modal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <h3 class="font-extrabold text-slate-900 text-sm">Return Target for Revision</h3>
                </div>
                <button type="button" onclick="closeReturnModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="return-single-form" method="POST">
                @csrf
                <div class="p-5 space-y-3">
                    <p class="text-xs text-slate-600">
                        Indicator: <strong id="return-indicator-name" class="text-slate-900"></strong>
                    </p>
                    <div>
                        <label for="return-remarks" class="block text-xs font-bold text-slate-700 mb-1">
                            Revision Remarks / Justification <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="return-remarks" name="remarks" rows="3" required
                            placeholder="Specify adjustments required for this indicator..."
                            class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeReturnModal()"
                        class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-sm transition">
                        Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- BATCH ENDORSE FORM (HIDDEN) -->
    <form id="batch-endorse-form" action="{{ route('bed2-plans.batch-head-approve') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="year" value="{{ $selectedYear }}">
        <div id="batch-endorse-inputs"></div>
    </form>

    <!-- BATCH RETURN MODAL -->
    <div id="batch-return-modal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <h3 class="font-extrabold text-slate-900 text-sm">Return Selected Plans</h3>
                </div>
                <button type="button" onclick="closeBatchReturnModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="batch-return-form" action="{{ route('bed2-plans.batch-return') }}" method="POST">
                @csrf
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <input type="hidden" name="operating_unit_id" value="{{ $operatingUnit?->id }}">
                <div id="batch-return-inputs"></div>
                <div class="p-5 space-y-3">
                    <p class="text-xs text-slate-600">
                        Returning <strong class="selected-count">0</strong> selected indicator plan(s) to division staff.
                    </p>
                    <div>
                        <label for="batch-return-remarks" class="block text-xs font-bold text-slate-700 mb-1">
                            Common Revision Remarks <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="batch-return-remarks" name="remarks" rows="3" required
                            placeholder="Specify general corrections or instructions for division encoders..."
                            class="w-full rounded-xl border-slate-200 text-xs focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeBatchReturnModal()"
                        class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-sm transition">
                        Return All Selected
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT FOR SELECTION & MODALS -->
    <script>
        const selectAllCheckbox = document.getElementById('select-all-plans');
        const planCheckboxes = document.querySelectorAll('.plan-checkbox');
        const btnBatchEndorse = document.getElementById('btn-batch-endorse');
        const btnBatchReturn = document.getElementById('btn-batch-return');
        const selectedCountSpans = document.querySelectorAll('.selected-count');

        function updateSelectionState() {
            const selected = Array.from(planCheckboxes).filter(cb => cb.checked);
            const count = selected.length;
            selectedCountSpans.forEach(el => el.textContent = count);

            if (btnBatchEndorse) btnBatchEndorse.disabled = count === 0;
            if (btnBatchReturn) btnBatchReturn.disabled = count === 0;

            if (selectAllCheckbox && planCheckboxes.length > 0) {
                selectAllCheckbox.checked = count === planCheckboxes.length;
                selectAllCheckbox.indeterminate = count > 0 && count < planCheckboxes.length;
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                planCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSelectionState();
            });
        }

        planCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectionState));

        // Batch Endorse Click
        if (btnBatchEndorse) {
            btnBatchEndorse.addEventListener('click', function () {
                const selected = Array.from(planCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                if (selected.length === 0) return;

                if (confirm(`Are you sure you want to endorse ${selected.length} target plan(s) to the PMO?`)) {
                    const container = document.getElementById('batch-endorse-inputs');
                    container.innerHTML = '';
                    selected.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'plan_ids[]';
                        input.value = id;
                        container.appendChild(input);
                    });
                    document.getElementById('batch-endorse-form').submit();
                }
            });
        }

        // Batch Return Modal
        if (btnBatchReturn) {
            btnBatchReturn.addEventListener('click', function () {
                const selected = Array.from(planCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                if (selected.length === 0) return;

                const container = document.getElementById('batch-return-inputs');
                container.innerHTML = '';
                selected.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'plan_ids[]';
                    input.value = id;
                    container.appendChild(input);
                });

                const modal = document.getElementById('batch-return-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        }

        function closeBatchReturnModal() {
            const modal = document.getElementById('batch-return-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Single Return Modal
        function openReturnModal(planId, indicatorName) {
            const modal = document.getElementById('return-modal');
            const form = document.getElementById('return-single-form');
            document.getElementById('return-indicator-name').textContent = indicatorName;
            form.action = `/bed2-plans/${planId}/return`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeReturnModal() {
            const modal = document.getElementById('return-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection
