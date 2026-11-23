@extends('layouts.app')
@section('title', 'PMO Evaluator Workspace')

@section('content')
    @php
        $cycleStart = $activeCycle ? \Illuminate\Support\Carbon::parse($activeCycle->window_start) : null;
        $cycleEnd = $activeCycle ? \Illuminate\Support\Carbon::parse($activeCycle->window_end) : null;
    @endphp

    <div class="space-y-5">
        <!-- Hero Briefing Bar -->
        <div
            class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex items-center justify-between relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                    <span>🔍</span> PMO Performance Evaluation Desk
                </div>
                @if ($activeCycle)
                    <a href="{{ route('admin.reporting-periods.index') }}"
                        class="flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-slate-300">Reporting Cycle:</span>
                        <span class="font-bold text-emerald-400">{{ $activeCycle->name }}</span>
                        <span class="text-slate-400">&bull;</span>
                        @if ($now->lt($cycleStart))
                            <span class="text-sky-300 font-medium">Opens in
                                {{ $now->diffForHumans($cycleStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                        @elseif ($now->between($cycleStart, $cycleEnd))
                            <span class="text-white font-medium">Closes in
                                {{ $now->diffForHumans($cycleEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                        @else
                            <span class="text-rose-300 font-medium">Closed</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('admin.reporting-periods.index') }}"
                        class="flex items-center gap-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                        <span>⚠️ No Active Cycle Set</span>
                        <span class="text-white font-medium underline ml-1">Configure &rarr;</span>
                    </a>
                @endif
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">Performance Review & Validation Center</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Review and certify division monthly physical submissions, verify BED-2 target baselines, and
                        analyze slippages for FY {{ $currentYear }}.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Validation Queue</p>
                        <p class="text-base font-extrabold text-amber-400">{{ $pendingPmoPlans->count() }} Plans Pending</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Approved Baselines</p>
                        <p class="text-base font-extrabold text-emerald-400">{{ $approvedPlansCount }} Plans</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Awaiting PMO Sign-off</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $pendingPmoPlans->count() }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Chief Endorsed</span>
                        <a href="{{ route('bed2-plans.index') }}" class="text-emerald-700 font-bold hover:underline">Batch Sign-off &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Validated & Approved</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $approvedPlansCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $clearedOUsCount }} / {{ $totalOUs }} OUs</span>
                        <span class="text-slate-400">Approved Baseline</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Returned for Revision</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-rose-600 tracking-tight">{{ $returnedPlansCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-lg">Revision Required</span>
                        <span class="text-slate-400">With Divisions</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Active Divisions</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalOUs }} <span
                            class="text-sm font-semibold text-slate-400">OUs</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">FY {{ $currentYear }}</span>
                        <span class="text-slate-400">{{ $totalPlansCount }} Total Plans</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BED-2 Plans Awaiting PMO Approval Section -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">BED-2 Baselines Awaiting PMO Approval</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Physical target baselines endorsed by Division Heads ready for bureau-level approval.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('bed2-plans.index') }}"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                        Open Batch Approval Console &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4">PAP & Indicator</th>
                            <th class="p-4 text-center">Annual Target</th>
                            <th class="p-4">Endorsed By</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pendingPmoPlans as $plan)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4">
                                    <div class="font-extrabold text-slate-900">{{ $plan->operatingUnit?->name }}</div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                        {{ $plan->operatingUnit?->code }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="font-extrabold text-slate-800">{{ $plan->pap?->code }}</span>
                                    <p class="text-slate-600 line-clamp-1 mt-0.5">{{ $plan->performanceIndicator?->name }}</p>
                                </td>
                                <td class="p-4 text-center font-extrabold text-slate-900">
                                    {{ number_format($plan->annual_target, 2) }}
                                </td>
                                <td class="p-4 text-slate-600">
                                    <div class="font-bold text-slate-800">{{ $plan->headApprovedBy?->name ?? 'Division Chief' }}</div>
                                    <span class="text-[10px] text-slate-400">{{ $plan->updated_at->format('M d, Y') }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('bed2-plans.pmo-approve', $plan) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                                                Approve
                                            </button>
                                        </form>
                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="font-extrabold text-xs text-slate-800">No BED-2 Plans Awaiting PMO Approval</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">All endorsed submissions have been processed or divisions are still preparing drafts.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Operating Units Submission Overview Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Bureau Division Target Status Overview</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tracking BED-2 target baseline coverage across all {{ $totalOUs }} operating units.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4 text-center">Indicators</th>
                            <th class="p-4 text-center">Approved</th>
                            <th class="p-4 text-center">Pending PMO</th>
                            <th class="p-4 text-center">Draft / Other</th>
                            <th class="p-4 text-right">BED-2 Baseline Status</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($operatingUnits as $unit)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4">
                                    <div class="font-extrabold text-slate-900">{{ $unit->name }}</div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">{{ $unit->code }}</span>
                                </td>
                                <td class="p-4 text-center font-bold text-slate-800">{{ $unit->total_plans_count }}</td>
                                <td class="p-4 text-center font-extrabold text-emerald-600">{{ $unit->approved_plans_count }}</td>
                                <td class="p-4 text-center font-extrabold text-amber-600">{{ $unit->pending_pmo_count }}</td>
                                <td class="p-4 text-center font-bold text-slate-500">{{ $unit->draft_plans_count + $unit->returned_plans_count }}</td>
                                <td class="p-4 text-right">
                                    @if ($unit->approved_plans_count > 0 && $unit->approved_plans_count === $unit->total_plans_count)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800">
                                            Fully Approved
                                        </span>
                                    @elseif ($unit->pending_pmo_count > 0)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800">
                                            Endorsed to PMO
                                        </span>
                                    @elseif ($unit->pending_head_count > 0)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-indigo-100 text-indigo-800">
                                            With Division Head
                                        </span>
                                    @elseif ($unit->total_plans_count > 0)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-blue-800">
                                            Drafting
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500">
                                            No Targets Encoded
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('bed2-plans.index', ['operating_unit_id' => $unit->id]) }}"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        Inspect &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
