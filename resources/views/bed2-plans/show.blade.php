<!--show.blade.php BED-2 Target Plan Dossier-->
@extends('layouts.app')
@section('title', 'BED-2 Target Dossier')

@section('content')
    <div class="max-w-5xl space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                        FY {{ $bed2Plan->year }} Plan
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border uppercase tracking-wider {{ $bed2Plan->statusBadgeClass() }}">
                        {{ $bed2Plan->statusLabel() }}
                    </span>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
                    {{ $bed2Plan->performanceIndicator?->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $bed2Plan->operatingUnit?->name }} ({{ $bed2Plan->operatingUnit?->code }}) &bull; {{ $bed2Plan->pap?->code }} - {{ $bed2Plan->pap?->name }}
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <a href="{{ route('bed2-plans.index', ['year' => $bed2Plan->year]) }}"
                    class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">
                    &larr; Back to Matrix
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($bed2Plan->isReturned() && $bed2Plan->remarks)
            <div class="p-5 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1.5 shadow-sm">
                <div class="flex items-center gap-2 text-rose-900 font-extrabold text-sm">
                    <span>⚠️ Returned for Revision</span>
                </div>
                <p class="text-rose-800 font-medium pl-6 leading-relaxed">
                    {{ $bed2Plan->remarks }}
                </p>
            </div>
        @endif

        @if ($bed2Plan->isApproved())
            <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs flex items-center gap-3.5 shadow-sm">
                <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-base shrink-0 shadow-sm">
                    🔒
                </span>
                <div>
                    <h4 class="font-extrabold text-emerald-950 text-sm">Official Physical Baseline Locked</h4>
                    <p class="text-emerald-800 mt-0.5">This plan has been approved by the Planning and Management Office (PMO). Targets are now active and locked as benchmarks for monthly accomplishment reporting.</p>
                </div>
            </div>
        @endif

        <!-- Approval Workflow Stepper -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Baseline Review Lifecycle</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Step 1: Draft -->
                <div class="p-3.5 rounded-xl border {{ in_array($bed2Plan->status, ['draft', 'submitted_to_head', 'head_approved', 'approved', 'returned']) ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-extrabold {{ in_array($bed2Plan->status, ['draft', 'submitted_to_head', 'head_approved', 'approved', 'returned']) ? 'text-emerald-800' : 'text-slate-400' }}">Step 1</span>
                        <span class="text-xs">✍️</span>
                    </div>
                    <p class="font-bold text-slate-900 text-xs mt-1">Target Encoding</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        {{ $bed2Plan->submittedBy ? 'By ' . $bed2Plan->submittedBy->name : 'Draft stage' }}
                    </p>
                </div>

                <!-- Step 2: Division Review -->
                <div class="p-3.5 rounded-xl border {{ in_array($bed2Plan->status, ['head_approved', 'approved']) ? 'bg-emerald-50/70 border-emerald-200' : ($bed2Plan->status === 'submitted_to_head' ? 'bg-amber-50 border-amber-300' : 'bg-slate-50 border-slate-200') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-extrabold {{ in_array($bed2Plan->status, ['head_approved', 'approved']) ? 'text-emerald-800' : ($bed2Plan->status === 'submitted_to_head' ? 'text-amber-800' : 'text-slate-400') }}">Step 2</span>
                        <span class="text-xs">👔</span>
                    </div>
                    <p class="font-bold text-slate-900 text-xs mt-1">Division Review</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        @if ($bed2Plan->headApprovedBy)
                            Endorsed by {{ $bed2Plan->headApprovedBy->name }}
                        @elseif ($bed2Plan->status === 'submitted_to_head')
                            Awaiting Division Chief
                        @else
                            Pending submission
                        @endif
                    </p>
                </div>

                <!-- Step 3: PMO Validation -->
                <div class="p-3.5 rounded-xl border {{ $bed2Plan->status === 'approved' ? 'bg-emerald-50/70 border-emerald-200' : ($bed2Plan->status === 'head_approved' ? 'bg-indigo-50 border-indigo-300' : 'bg-slate-50 border-slate-200') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-extrabold {{ $bed2Plan->status === 'approved' ? 'text-emerald-800' : ($bed2Plan->status === 'head_approved' ? 'text-indigo-800' : 'text-slate-400') }}">Step 3</span>
                        <span class="text-xs">🏛️</span>
                    </div>
                    <p class="font-bold text-slate-900 text-xs mt-1">PMO Validation</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        @if ($bed2Plan->pmoApprovedBy)
                            Approved by {{ $bed2Plan->pmoApprovedBy->name }}
                        @elseif ($bed2Plan->status === 'head_approved')
                            Under PMO review
                        @else
                            Pending endorsement
                        @endif
                    </p>
                </div>

                <!-- Step 4: Approved Baseline -->
                <div class="p-3.5 rounded-xl border {{ $bed2Plan->status === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 border-slate-200' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-extrabold {{ $bed2Plan->status === 'approved' ? 'text-emerald-100' : 'text-slate-400' }}">Step 4</span>
                        <span class="text-xs">🔒</span>
                    </div>
                    <p class="font-bold {{ $bed2Plan->status === 'approved' ? 'text-white' : 'text-slate-900' }} text-xs mt-1">Baseline Locked</p>
                    <p class="text-[10px] {{ $bed2Plan->status === 'approved' ? 'text-emerald-100' : 'text-slate-400' }} mt-0.5">
                        {{ $bed2Plan->status === 'approved' ? 'Ready for reporting' : 'Awaiting approval' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Specifications & Scope Card -->
        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Plan Specifications</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Operating Unit</p>
                    <p class="font-bold text-slate-900 mt-1 text-sm">{{ $bed2Plan->operatingUnit?->name }}</p>
                    <p class="text-[10px] text-slate-400">{{ $bed2Plan->operatingUnit?->type }} &bull; {{ $bed2Plan->operatingUnit?->code }}</p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Program / PAP</p>
                    <p class="font-bold text-slate-900 mt-1 text-sm">{{ $bed2Plan->pap?->code }}</p>
                    <p class="text-[10px] text-slate-500 truncate" title="{{ $bed2Plan->pap?->name }}">{{ $bed2Plan->pap?->name }}</p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Unit of Measure & Type</p>
                    <p class="font-bold text-emerald-800 mt-1 text-sm">{{ $bed2Plan->performanceIndicator?->unit_of_measure }}</p>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Type: {{ $bed2Plan->performanceIndicator?->type }}</p>
                </div>
            </div>

            @if ($bed2Plan->remarks)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Encoding Remarks & Notes</p>
                    <p class="font-medium text-slate-700 mt-1">{{ $bed2Plan->remarks }}</p>
                </div>
            @endif
        </div>

        <!-- Physical Target Breakdown Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Physical Breakdown Matrix</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Annual commitment partitioned across quarterly and monthly targets.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    @if ($bed2Plan->isBalanced())
                        <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold flex items-center gap-1.5 bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span>✓</span> Balanced (100% Allocated)
                        </div>
                    @else
                        @php
                            $variance = $bed2Plan->balanceVariance();
                        @endphp
                        <div class="px-3 py-1.5 rounded-xl text-xs font-extrabold flex items-center gap-1.5 bg-amber-100 text-amber-900 border border-amber-300">
                            <span>⚠️</span> Unbalanced ({{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 2) }})
                        </div>
                    @endif
                    <div class="px-4 py-2 rounded-xl bg-slate-900 text-white flex items-center gap-3">
                        <span class="text-xs text-slate-300">Annual Target:</span>
                        <span class="text-lg font-black text-emerald-400">{{ number_format($bed2Plan->annual_target, 2) }}</span>
                        <span class="text-xs text-slate-300 font-semibold">{{ $bed2Plan->performanceIndicator?->unit_of_measure }}</span>
                    </div>
                </div>
            </div>

            @if (! $bed2Plan->isBalanced() && $bed2Plan->isEditable())
                <div class="p-4 bg-amber-50 border-b border-amber-200/70 flex items-start gap-3">
                    <div class="p-1 rounded-lg bg-amber-100 text-amber-800 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="text-xs">
                        <p class="font-extrabold text-amber-900">Submission Blocked: Target Allocation is Unbalanced</p>
                        <p class="text-amber-800 mt-0.5">
                            The sum of the 12 monthly milestones (<strong>{{ number_format($bed2Plan->monthlySum(), 2) }}</strong>) does not equal the annual target commitment (<strong>{{ number_format($bed2Plan->annual_target, 2) }}</strong>).
                            This plan cannot be submitted to the Division Chief until it is balanced. Click <strong>Edit Targets</strong> to adjust your monthly allocations.
                        </p>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-center text-xs">
                    <!-- Quarter Headers -->
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-700 font-extrabold text-xs">
                        <tr>
                            <th colspan="3" class="p-3 border-r border-slate-200/80 bg-emerald-50/40">
                                Quarter 1 &bull; Subtotal: <span class="font-black text-emerald-800 font-mono">{{ number_format($bed2Plan->q1_target, 2) }}</span>
                            </th>
                            <th colspan="3" class="p-3 border-r border-slate-200/80 bg-sky-50/40">
                                Quarter 2 &bull; Subtotal: <span class="font-black text-sky-800 font-mono">{{ number_format($bed2Plan->q2_target, 2) }}</span>
                            </th>
                            <th colspan="3" class="p-3 border-r border-slate-200/80 bg-amber-50/40">
                                Quarter 3 &bull; Subtotal: <span class="font-black text-amber-800 font-mono">{{ number_format($bed2Plan->q3_target, 2) }}</span>
                            </th>
                            <th colspan="3" class="p-3 bg-purple-50/40">
                                Quarter 4 &bull; Subtotal: <span class="font-black text-purple-800 font-mono">{{ number_format($bed2Plan->q4_target, 2) }}</span>
                            </th>
                        </tr>
                        <tr class="text-[10px] text-slate-400 uppercase font-bold border-t border-slate-200/60">
                            <th class="p-2 border-r border-slate-100">Jan</th>
                            <th class="p-2 border-r border-slate-100">Feb</th>
                            <th class="p-2 border-r border-slate-200">Mar</th>
                            <th class="p-2 border-r border-slate-100">Apr</th>
                            <th class="p-2 border-r border-slate-100">May</th>
                            <th class="p-2 border-r border-slate-200">Jun</th>
                            <th class="p-2 border-r border-slate-100">Jul</th>
                            <th class="p-2 border-r border-slate-100">Aug</th>
                            <th class="p-2 border-r border-slate-200">Sep</th>
                            <th class="p-2 border-r border-slate-100">Oct</th>
                            <th class="p-2 border-r border-slate-100">Nov</th>
                            <th class="p-2">Dec</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="font-mono font-bold text-slate-800 text-xs">
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m1_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m2_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-200 bg-slate-50/30 font-extrabold">{{ number_format($bed2Plan->m3_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m4_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m5_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-200 bg-slate-50/30 font-extrabold">{{ number_format($bed2Plan->m6_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m7_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m8_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-200 bg-slate-50/30 font-extrabold">{{ number_format($bed2Plan->m9_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m10_target, 2) }}</td>
                            <td class="p-3.5 border-r border-slate-100">{{ number_format($bed2Plan->m11_target, 2) }}</td>
                            <td class="p-3.5 bg-slate-50/30 font-extrabold">{{ number_format($bed2Plan->m12_target, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Control Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-slate-800">Current Lifecycle Action Gate</p>
                <p class="text-[11px] text-slate-400">Permitted administrative operations for your role.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- 1. Actions for Encoder / Author -->
                @if ($bed2Plan->isEditable() && ($bed2Plan->operating_unit_id === auth()->user()->operating_unit_id || auth()->user()->hasAnyRole(['superadmin', 'admin'])))
                    <a href="{{ route('bed2-plans.edit', $bed2Plan) }}"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition">
                        Edit Targets
                    </a>

                    @if ($bed2Plan->isBalanced())
                        <form method="POST" action="{{ route('bed2-plans.submit-head', $bed2Plan) }}" class="inline">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Submit this plan to Division Chief for review and endorsement?');"
                                class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-sm transition">
                                Submit to Division Chief &rarr;
                            </button>
                        </form>
                    @else
                        <button type="button" disabled
                            title="Cannot submit: Monthly targets do not balance with the annual target commitment."
                            class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 font-extrabold text-xs cursor-not-allowed flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Unbalanced (Edit to Submit)
                        </button>
                    @endif

                    <form method="POST" action="{{ route('bed2-plans.destroy', $bed2Plan) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Permanently delete this draft target plan?');"
                            class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs border border-rose-200 transition">
                            Delete
                        </button>
                    </form>
                @endif

                <!-- 2. Actions for Division Chief (OU Head) -->
                @if ($bed2Plan->status === 'submitted_to_head' && ((auth()->user()->hasRole('ou-head') && auth()->user()->operating_unit_id === $bed2Plan->operating_unit_id) || auth()->user()->hasAnyRole(['superadmin', 'admin'])))
                    <form method="POST" action="{{ route('bed2-plans.head-approve', $bed2Plan) }}" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Endorse this target plan to the PMO Administrator?');"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition">
                            ✓ Endorse to PMO
                        </button>
                    </form>

                    <button type="button" onclick="promptReturn('{{ route('bed2-plans.return', $bed2Plan) }}')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs border border-rose-200 transition">
                        Return for Revision
                    </button>
                @endif

                <!-- 3. Actions for PMO Admin / Evaluator -->
                @if (in_array($bed2Plan->status, ['head_approved', 'submitted_to_head']) && auth()->user()->hasAnyRole(['superadmin', 'admin', 'evaluator']))
                    <form method="POST" action="{{ route('bed2-plans.pmo-approve', $bed2Plan) }}" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Approve this target plan and lock the official annual baseline?');"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                            🔒 Approve & Lock Baseline
                        </button>
                    </form>

                    <button type="button" onclick="promptReturn('{{ route('bed2-plans.return', $bed2Plan) }}')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs border border-rose-200 transition">
                        Return for Revision
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Hidden Form for Returning with Remarks -->
    <form id="returnForm" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="remarks" id="returnRemarksInput">
    </form>

    <script>
        function promptReturn(url) {
            const remarks = prompt('Please specify the reason/remarks for returning this target plan for revision:');
            if (remarks === null) return; // Cancelled
            if (!remarks.trim()) {
                alert('Return remarks are required.');
                return;
            }

            const form = document.getElementById('returnForm');
            form.action = url;
            document.getElementById('returnRemarksInput').value = remarks;
            form.submit();
        }
    </script>
@endsection
