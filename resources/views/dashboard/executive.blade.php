@extends('layouts.app')
@section('title', 'Executive Briefing')

@section('content')
<div class="space-y-5">
    
    <!-- Hero Briefing Bar (Corner-Anchored) -->
    <div class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between relative z-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                <span>📊</span> Executive Leadership Briefing &bull; Bureau of Plant Industry
            </div>
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                <span class="text-slate-300">Fiscal Period:</span>
                <span class="font-bold text-white">FY {{ date('Y') }} &bull; Q4</span>
                <span class="text-slate-400">&bull;</span>
                <span class="text-emerald-400 font-semibold">Healthy (91.4%)</span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight">Bureau Physical Performance Summary</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Strategic oversight of bureau physical commitments, division accomplishment benchmarks, and COA audit defense readiness.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Total Delivery</p>
                    <p class="text-base font-extrabold text-emerald-400">91.4% Target</p>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Compliance</p>
                    <p class="text-base font-extrabold text-white">10 / 12 OUs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 1: 4 EXECUTIVE METRIC TILES (Corner-Anchored) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Overall Bureau Delivery</span>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">91.4%</h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">+1.4% Over Threshold</span>
                    <span class="text-slate-400 font-medium">Goal: 90.0%</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Certified Divisions</span>
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">10 <span class="text-lg font-semibold text-slate-400">/ 12</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">83.3% On Time</span>
                    <span class="text-slate-400 font-medium">2 In Final Review</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Major Slippage</span>
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-rose-600 tracking-tight">3 <span class="text-sm font-semibold text-slate-400">PAPs</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-lg">Negative &ge; 15%</span>
                    <span class="text-slate-400 font-medium">RCA Mandated</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Audit Defense Readiness</span>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">100%</h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">Verified MOVs</span>
                    <span class="text-slate-400 font-medium">COA Compliant</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Performance Rankings & Progress -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">Operating Unit Accomplishment Rankings</h3>
                <p class="text-xs text-slate-400 mt-0.5">Physical accomplishment performance sorted across all bureau divisions.</p>
            </div>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl">FY {{ date('Y') }} Consolidated</span>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900">National Plant Quarantine (NPQSD)</span>
                    <span class="text-xs font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-lg">98.1%</span>
                </div>
                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: 98.1%"></div>
                </div>
                <p class="text-[11px] text-slate-500">12 / 12 Key Milestones Achieved &bull; Zero Slippage</p>
            </div>

            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900">Crop Research Division (CRD)</span>
                    <span class="text-xs font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-lg">94.2%</span>
                </div>
                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: 94.2%"></div>
                </div>
                <p class="text-[11px] text-slate-500">8 / 8 Key Milestones Achieved &bull; -1.8% Minor Var</p>
            </div>

            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900">Crop Pest Management (CPMD)</span>
                    <span class="text-xs font-extrabold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-lg">78.5%</span>
                </div>
                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                    <div class="bg-rose-500 h-full rounded-full" style="width: 78.5%"></div>
                </div>
                <p class="text-[11px] text-rose-600 font-semibold">-16.5% Slippage &bull; Severe Weather Delay</p>
            </div>
        </div>
    </div>

</div>
@endsection
