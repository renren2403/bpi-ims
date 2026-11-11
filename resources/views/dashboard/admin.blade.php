@extends('layouts.app')
@section('title', 'Bureau Physical Performance Command Center')

@section('content')
@php
    $adminActiveCycle = \App\Models\ReportingPeriod::where('is_active', true)->first();
    $cycleNow = \Illuminate\Support\Carbon::now();
    $cycleStart = $adminActiveCycle ? \Illuminate\Support\Carbon::parse($adminActiveCycle->window_start) : null;
    $cycleEnd = $adminActiveCycle ? \Illuminate\Support\Carbon::parse($adminActiveCycle->window_end) : null;
@endphp
<div class="space-y-5">
    
    <!-- HERO BRIEFING BAR (Content Anchored to All 4 Corners) -->
    <div class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Hero Top Row: Left Tag & Right Status Pill -->
        <div class="flex items-center justify-between relative z-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                <span>🌱</span> Republic of the Philippines &bull; Bureau of Plant Industry
            </div>
            @if ($adminActiveCycle)
                <a href="{{ route('admin.reporting-periods.index') }}" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-slate-300">Reporting Cycle:</span>
                    <span class="font-bold text-emerald-400">{{ $adminActiveCycle->name }}</span>
                    <span class="text-slate-400">&bull;</span>
                    @if ($cycleNow->lt($cycleStart))
                        <span class="text-sky-300 font-medium">Opens in {{ $cycleNow->diffForHumans($cycleStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                    @elseif ($cycleNow->between($cycleStart, $cycleEnd))
                        <span class="text-white font-medium">Closes in {{ $cycleNow->diffForHumans($cycleEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                    @else
                        <span class="text-rose-300 font-medium">Closed</span>
                    @endif
                </a>
            @else
                <a href="{{ route('admin.reporting-periods.index') }}" class="flex items-center gap-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                    <span>⚠️ No Active Cycle Set</span>
                    <span class="text-white font-medium underline ml-1">Configure &rarr;</span>
                </a>
            @endif
        </div>

        <!-- Hero Bottom Row: Left Headline & Right Metrics Badges -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight">Executive Performance Command Center</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Consolidated tracking of BED-2 target milestones, division physical deliveries, and audit-compliant Means of Verification for FY {{ date('Y') }}.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Bureau Delivery Health</p>
                    <p class="text-base font-extrabold text-emerald-400">91.4% Target Met</p>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Certifications</p>
                    <p class="text-base font-extrabold text-white">10 / {{ \App\Models\OperatingUnit::count() ?: 12 }} OUs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 1: 4 KPI METRIC TILES (Content Anchored to All 4 Tile Corners) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Tile 1: Delivery Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Overall Delivery</span>
                <div class="relative w-11 h-11 flex items-center justify-center">
                    <svg class="w-11 h-11 transform -rotate-90">
                        <circle cx="22" cy="22" r="18" stroke="currentColor" stroke-width="4" class="text-slate-100" fill="transparent"/>
                        <circle cx="22" cy="22" r="18" stroke="currentColor" stroke-width="4" class="text-emerald-500" stroke-linecap="round" fill="transparent" stroke-dasharray="113" stroke-dashoffset="9.7"/>
                    </svg>
                    <span class="absolute text-[10px] font-extrabold text-emerald-700">91%</span>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">91.4%</h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">+2.3% MoM</span>
                    <span class="text-slate-400 font-medium">Goal: 90% Threshold</span>
                </div>
            </div>
        </div>

        <!-- Tile 2: Division Submissions -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Division Endorsement</span>
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">10 <span class="text-lg font-semibold text-slate-400">/ {{ \App\Models\OperatingUnit::count() ?: 12 }}</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">83.3% Complete</span>
                    <span class="text-slate-400 font-medium">2 OUs Pending</span>
                </div>
            </div>
        </div>

        <!-- Tile 3: Critical Slippage -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Critical Slippage</span>
                <div class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-rose-600 tracking-tight">3 <span class="text-sm font-semibold text-slate-400">PAPs</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-lg">&ge; 15% Lag</span>
                    <span class="text-slate-400 font-medium">RCA Required</span>
                </div>
            </div>
        </div>

        <!-- Tile 4: Pending Unlock Requests -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Unlock Inquiries</span>
                <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">2 <span class="text-sm font-semibold text-slate-400">Units</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-amber-800 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Chief Endorsed</span>
                    <a href="#unlock-panel" class="font-bold text-emerald-700 hover:underline">Review &rarr;</a>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2: FULL-WIDTH CORNER-BALANCED SPLIT GRID (8 COLS LEFT / 4 COLS RIGHT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- LEFT SECTION (8 COLS) -> ANCHORS LOWER-LEFT QUADRANT -->
        <div class="lg:col-span-8 space-y-5">
            
            <!-- DIVISION TRAJECTORY STUDIO -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Operating Units Performance Trajectory</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Physical delivery rate versus approved annual BED-2 target milestones.</p>
                    </div>

                    <!-- Actions & Category Pills Anchored Top-Right of Card -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5 text-xs">
                            <button class="px-2.5 py-1 rounded-xl font-bold bg-slate-900 text-white">All ({{ \App\Models\OperatingUnit::count() ?: 12 }})</button>
                            <button class="px-2.5 py-1 rounded-xl font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">On-Track (9)</button>
                            <button class="px-2.5 py-1 rounded-xl font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 transition">Lagging (3)</button>
                        </div>
                        <div class="hidden sm:block w-px h-5 bg-slate-200"></div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.operating-units.create') }}" class="px-2.5 py-1 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-700 text-white text-xs shadow-sm shadow-emerald-600/20 transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Unit</span>
                            </a>
                            <a href="{{ route('admin.operating-units.index') }}" class="px-2.5 py-1 rounded-xl font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs transition">
                                Manage &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    
                    <!-- Division 1: CPMD -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-300 hover:shadow-sm transition flex flex-col justify-between space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">CPMD</span>
                                <h4 class="font-extrabold text-xs text-slate-900 mt-1">Crop Pest Management</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">87.1%</span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex justify-between text-[11px] text-slate-500 font-medium">
                                <span>Target: 140.0</span>
                                <span>Actual: 122.0</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: 87.1%"></div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[10px]">
                            <span class="text-emerald-700 font-bold">✓ Chief Certified</span>
                            <span class="text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded">1 Lagging PAP</span>
                        </div>
                    </div>

                    <!-- Division 2: PPD -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-300 hover:shadow-sm transition flex flex-col justify-between space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">PPD</span>
                                <h4 class="font-extrabold text-xs text-slate-900 mt-1">Plant Product Dev.</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">97.8%</span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex justify-between text-[11px] text-slate-500 font-medium">
                                <span>Target: 90.0</span>
                                <span>Actual: 88.0</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: 97.8%"></div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[10px]">
                            <span class="text-emerald-700 font-bold">✓ PMO Validated</span>
                            <span class="text-slate-400">On-Track</span>
                        </div>
                    </div>

                    <!-- Division 3: NSQCS -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-300 hover:shadow-sm transition flex flex-col justify-between space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">NSQCS</span>
                                <h4 class="font-extrabold text-xs text-slate-900 mt-1">Natl. Seed Quality</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">94.2%</span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex justify-between text-[11px] text-slate-500 font-medium">
                                <span>Target: 320.0</span>
                                <span>Actual: 301.5</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: 94.2%"></div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[10px]">
                            <span class="text-emerald-700 font-bold">✓ Chief Certified</span>
                            <span class="text-slate-400">On-Track</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- CRITICAL SLIPPAGE & RCA WATCHLIST (Anchoring Lower-Left Corner) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-rose-50/60 to-white">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-pulse"></span>
                        <h3 class="text-sm font-extrabold text-slate-900">Critical Slippage & Root Cause Mitigation Watchlist</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 self-start">
                        3 Interventions Required (&ge; 15% Lag)
                    </span>
                </div>

                <div class="p-4 divide-y divide-slate-100">
                    <!-- Slippage Item 1 -->
                    <div class="pb-4 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">CPMD</span>
                                    <span class="text-[11px] text-slate-400">Activity a.2 &bull; Pest validation</span>
                                </div>
                                <h4 class="font-extrabold text-xs text-slate-900 mt-0.5">Number of infested sites validated</h4>
                            </div>
                            
                            <div class="flex items-center gap-4 bg-slate-50 border border-slate-200/70 px-3 py-1.5 rounded-xl shrink-0 text-xs">
                                <span>Target: <strong>9.0</strong></span>
                                <span>Actual: <strong class="text-rose-600">6.0</strong></span>
                                <span class="text-xs font-extrabold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-lg">-33.3%</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 bg-slate-50/80 p-3 rounded-xl border border-slate-200/70 text-[11px]">
                            <div>
                                <p class="font-bold text-slate-700">Root Cause:</p>
                                <p class="text-slate-500 mt-0.5">Typhoon disruptions in Region 7 sites.</p>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700">Immediate Correction:</p>
                                <p class="text-slate-500 mt-0.5">Dispatched 2 regional teams on Oct 19.</p>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700">Preventative Action:</p>
                                <p class="text-slate-500 mt-0.5">Digital geo-tagging protocol with LGUs.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Slippage Item 2 -->
                    <div class="pt-4 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">CPMD</span>
                                    <span class="text-[11px] text-slate-400">Activity c.1 &bull; Workshop</span>
                                </div>
                                <h4 class="font-extrabold text-xs text-slate-900 mt-0.5">Number of planning workshops conducted</h4>
                            </div>
                            
                            <div class="flex items-center gap-4 bg-slate-50 border border-slate-200/70 px-3 py-1.5 rounded-xl shrink-0 text-xs">
                                <span>Target: <strong>1.0</strong></span>
                                <span>Actual: <strong class="text-rose-600">0.0</strong></span>
                                <span class="text-xs font-extrabold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-lg">-100.0%</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 bg-slate-50/80 p-3 rounded-xl border border-slate-200/70 text-[11px]">
                            <div>
                                <p class="font-bold text-slate-700">Root Cause:</p>
                                <p class="text-slate-500 mt-0.5">Bidding failure for training venue.</p>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700">Immediate Correction:</p>
                                <p class="text-slate-500 mt-0.5">Rescheduled Nov 14 at central hall.</p>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700">Preventative Action:</p>
                                <p class="text-slate-500 mt-0.5">Pre-approved govt facility MOAs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT SECTION (4 COLS) -> ANCHORS UPPER-RIGHT & LOWER-RIGHT QUADRANTS -->
        <div class="lg:col-span-4 space-y-5" id="unlock-panel">
            
            <!-- UPPER-RIGHT CARD: MONTHLY CYCLE SCHEDULE & COUNTDOWN -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Reporting Cycle Window</h3>
                        <p class="text-[11px] text-slate-400">Active monthly submission lock</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($adminActiveCycle)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping" title="Cycle Active"></span>
                        @endif
                        <a href="{{ route('admin.reporting-periods.index') }}" 
                           class="text-[11px] font-extrabold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 transition flex items-center gap-1">
                            <span>Manage</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Mini Cycle Gauge -->
                @if ($adminActiveCycle)
                    <div class="flex items-center gap-4 bg-emerald-50/60 border border-emerald-100 p-3.5 rounded-xl">
                        <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex flex-col items-center justify-center font-extrabold text-sm shrink-0 shadow-sm">
                            <span>{{ $cycleEnd->format('d') }}</span>
                            <span class="text-[9px] uppercase font-bold tracking-wider -mt-1">{{ $cycleEnd->format('M') }}</span>
                        </div>
                        <div class="text-xs">
                            <p class="font-extrabold text-emerald-950">Lock Date: {{ $cycleEnd->format('M d, g:i A') }}</p>
                            <p class="text-emerald-700 text-[11px] mt-0.5">
                                @if ($cycleNow->lt($cycleStart))
                                    Opens in {{ $cycleNow->diffForHumans($cycleStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                @elseif ($cycleNow->between($cycleStart, $cycleEnd))
                                    {{ $cycleNow->diffForHumans($cycleEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} remaining
                                @else
                                    Window closed (Lock enforced)
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between gap-3 text-xs">
                        <div>
                            <p class="font-extrabold text-rose-950 flex items-center gap-1">
                                <span>⚠️ No Active Cycle</span>
                            </p>
                            <p class="text-[11px] text-rose-700 mt-0.5">Submissions locked bureau-wide</p>
                        </div>
                        <a href="{{ route('admin.reporting-periods.index') }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-[11px] shadow transition shrink-0">
                            Set Period &rarr;
                        </a>
                    </div>
                @endif

                <!-- Quick Division Submission Checklist -->
                <div class="space-y-2 text-xs">
                    <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Division Endorsement Status</p>
                    
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100 transition">
                            <span class="font-bold text-slate-800">Crop Research Division</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Certified</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100 transition">
                            <span class="font-bold text-slate-800">Plant Quarantine (NPQSD)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Certified</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100 transition">
                            <span class="font-bold text-slate-800">Seed Quality Control</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Certified</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-amber-50/70 border border-amber-200/50">
                            <span class="font-bold text-amber-900">Crop Pest Management</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-200 text-amber-900">Pending</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-amber-50/70 border border-amber-200/50">
                            <span class="font-bold text-amber-900">Agricultural Engineering</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-200 text-amber-900">Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOWER-RIGHT CORNER CARD: UNLOCK REQUESTS & AUDIT LOGS -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Pending Unlock Inquiries</h3>
                        <p class="text-[11px] text-slate-400">Formal Chief authorization queue</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">2 Active</span>
                </div>

                <!-- Unlock Card 1 -->
                <div class="p-3 rounded-xl border border-amber-200/80 bg-amber-50/40 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">Crop Pest Management (CPMD)</span>
                        <span class="text-[10px] text-slate-400">1h ago</span>
                    </div>
                    <p class="text-[11px] text-slate-600">Requesting to encode late MOV lab batch certifications for October indicators.</p>
                    <div class="pt-1 flex items-center justify-end gap-2">
                        <button class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 font-bold text-[11px] text-slate-700 transition">View Details</button>
                        <button class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-sm transition">Authorize (24h)</button>
                    </div>
                </div>

                <!-- Unlock Card 2 -->
                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">Plant Product Dev. (PPD)</span>
                        <span class="text-[10px] text-slate-400">4h ago</span>
                    </div>
                    <p class="text-[11px] text-slate-600">Correction of encoded actuals for Activity 3.b due to laboratory recalculation.</p>
                    <div class="pt-1 flex items-center justify-end gap-2">
                        <button class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 font-bold text-[11px] text-slate-700 transition">View Details</button>
                        <button class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-sm transition">Authorize (24h)</button>
                    </div>
                </div>

                <!-- Mini Security Audit Footer In Bottom-Right Corner -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>COA Audit Trail: Enabled</span>
                    <a href="#" class="font-bold text-emerald-700 hover:underline">Full Log &rarr;</a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
