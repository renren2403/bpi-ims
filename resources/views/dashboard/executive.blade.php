@extends('layouts.app')
@section('title', 'Executive Briefing')

@section('content')
    <div class="space-y-5">

        <!-- Hero Briefing Bar -->
        <div
            class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex items-center justify-between relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                    <span>📊</span> Executive Leadership Briefing &bull; Bureau of Plant Industry
                </div>
                <div
                    class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                    <span class="text-slate-300">Fiscal Period:</span>
                    <span class="font-bold text-white">FY {{ $currentYear }}</span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-emerald-400 font-semibold">{{ $complianceRate }}% Baseline Coverage</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">Bureau Physical Performance Summary</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Strategic oversight of bureau physical commitments, division accomplishment benchmarks, and COA
                        audit defense readiness for FY {{ $currentYear }}.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Total Committed Target</p>
                        <p class="text-base font-extrabold text-emerald-400">{{ number_format($totalBureauTarget, 2) }} Units</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Division Compliance</p>
                        <p class="text-base font-extrabold text-white">{{ $ousWithApprovedBed2 }} / {{ $totalOUs }} OUs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 1: 4 EXECUTIVE METRIC TILES -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Bureau Baseline Coverage</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $complianceRate }}%</h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $ousWithApprovedBed2 }} OUs Approved</span>
                        <span class="text-slate-400 font-medium">Goal: 100%</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Certified Divisions</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $ousWithApprovedBed2 }} <span
                            class="text-lg font-semibold text-slate-400">/ {{ $totalOUs }}</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">{{ $complianceRate }}% Complete</span>
                        <span class="text-slate-400 font-medium">{{ max(0, $totalOUs - $ousWithApprovedBed2) }} In Process</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Pending Validations</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $pendingApprovalCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Under Review</span>
                        <span class="text-slate-400 font-medium">PMO & Chief</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tracked Indicators</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalIndicatorsTracked }} <span
                            class="text-sm font-semibold text-slate-400">PAPs</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">Active Bureau-wide</span>
                        <span class="text-slate-400 font-medium">DBM Aligned</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Division Performance & BED-2 Progress -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Operating Unit Target Baseline Readiness</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Physical commitment baselines across all {{ $totalOUs }} operating divisions.</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl">FY {{ $currentYear }} Consolidated</span>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse ($operatingUnits as $unit)
                    @php
                        $pct = $unit->total_plans_count > 0 ? min(100, round(($unit->approved_plans_count / $unit->total_plans_count) * 100)) : 0;
                        $colorClass = $pct === 100 && $unit->total_plans_count > 0 ? 'bg-emerald-500' : ($unit->pending_pmo_count > 0 ? 'bg-amber-500' : 'bg-slate-300');
                        $badgeColor = $pct === 100 && $unit->total_plans_count > 0 ? 'bg-emerald-100 text-emerald-800' : ($unit->total_plans_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600');
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-extrabold text-slate-900 line-clamp-1" title="{{ $unit->name }}">{{ $unit->name }}</span>
                                <span class="text-[10px] text-slate-400 font-bold">{{ $unit->code }}</span>
                            </div>
                            <span class="text-xs font-extrabold px-2 py-0.5 rounded-lg {{ $badgeColor }}">
                                {{ $pct }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="{{ $colorClass }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-500 flex items-center justify-between">
                            <span>{{ $unit->approved_plans_count }} / {{ $unit->total_plans_count }} Approved</span>
                            <a href="{{ route('bed2-plans.index', ['operating_unit_id' => $unit->id]) }}" class="text-emerald-700 font-bold hover:underline">Inspect &rarr;</a>
                        </p>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-400 text-xs">
                        No active operating units found.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
