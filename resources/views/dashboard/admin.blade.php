@extends('layouts.app')
@section('title', 'Bureau Physical Performance Command Center')

@section('content')
    @php
        $cycleStart = $activeCycle ? \Illuminate\Support\Carbon::parse($activeCycle->window_start) : null;
        $cycleEnd = $activeCycle ? \Illuminate\Support\Carbon::parse($activeCycle->window_end) : null;
    @endphp

    <div class="space-y-5">
        <!-- HERO BRIEFING BAR -->
        <div
            class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <!-- Hero Top Row: Left Tag & Right Status Pill -->
            <div class="flex items-center justify-between relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                    <span>🌱</span> Republic of the Philippines &bull; Bureau of Plant Industry
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

            <!-- Hero Bottom Row: Left Headline & Right Metrics Badges -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">Executive Performance Command Center</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Consolidated tracking of BED-2 target milestones, division physical deliveries, and audit-compliant
                        Means of Verification for FY {{ $currentYear }}.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Baseline Compliance</p>
                        <p class="text-base font-extrabold text-emerald-400">{{ $complianceRate }}% Approved</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Approved Baselines</p>
                        <p class="text-base font-extrabold text-white">{{ $ousWithApprovedBed2 }} / {{ $totalOUs }} OUs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 1: 4 KPI METRIC TILES -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tile 1: Baseline Delivery Rate -->
            <div
                class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Approved Baselines</span>
                    <div class="relative w-11 h-11 flex items-center justify-center">
                        <svg class="w-11 h-11 transform -rotate-90">
                            <circle cx="22" cy="22" r="18" stroke="currentColor" stroke-width="4"
                                class="text-slate-100" fill="transparent" />
                            <circle cx="22" cy="22" r="18" stroke="currentColor" stroke-width="4"
                                class="text-emerald-500" stroke-linecap="round" fill="transparent" stroke-dasharray="113"
                                stroke-dashoffset="{{ 113 - (113 * min(100, $complianceRate)) / 100 }}" />
                        </svg>
                        <span class="absolute text-[10px] font-extrabold text-emerald-700">{{ round($complianceRate) }}%</span>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $approvedPlans }} <span
                            class="text-base font-medium text-slate-400">/ {{ $totalPlans }} Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $ousWithApprovedBed2 }} OUs Complete</span>
                        <span class="text-slate-400 font-medium">FY {{ $currentYear }} BED-2</span>
                    </div>
                </div>
            </div>

            <!-- Tile 2: Division Submissions -->
            <div
                class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Active Divisions</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $ousWithSubmissions }} <span
                            class="text-lg font-semibold text-slate-400">/ {{ $totalOUs }}</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">{{ $submissionRate }}% Active</span>
                        <span class="text-slate-400 font-medium">{{ max(0, $totalOUs - $ousWithSubmissions) }} OUs Pending</span>
                    </div>
                </div>
            </div>

            <!-- Tile 3: Pending PMO Review -->
            <div
                class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">PMO Validation Queue</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $pendingPmoPlans }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Awaiting Sign-off</span>
                        <a href="{{ route('bed2-plans.index') }}" class="text-emerald-700 font-bold hover:underline">Review &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Tile 4: Unlock Inquiries & Issues -->
            <div
                class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Unlock & Returned</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $pendingUnlockRequests->count() + $returnedPlans }} <span
                            class="text-sm font-semibold text-slate-400">Total</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-lg">{{ $returnedPlans }} Returned</span>
                        <span class="text-slate-400 font-medium">{{ $pendingUnlockRequests->count() }} Inquiries</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: FULL-WIDTH CORNER-BALANCED SPLIT GRID (8 COLS LEFT / 4 COLS RIGHT) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- LEFT SECTION (8 COLS) -->
            <div class="lg:col-span-8 space-y-5">

                <!-- DIVISION TRAJECTORY STUDIO -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Operating Units Performance Trajectory</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Physical delivery commitments and approved annual BED-2 target baselines.</p>
                        </div>

                        <!-- Actions & Links -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('bed2-plans.create') }}"
                                class="px-2.5 py-1 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-700 text-white text-xs shadow-sm shadow-emerald-600/20 transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Encode Target</span>
                            </a>
                            <a href="{{ route('bed2-plans.index') }}"
                                class="px-2.5 py-1 rounded-xl font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs transition">
                                All BED-2 &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                        @forelse ($operatingUnits as $ou)
                            @php
                                $ouStatusBadge = 'bg-slate-100 text-slate-600';
                                $ouStatusText = 'No Targets';
                                if ($ou->approved_plans_count > 0) {
                                    $ouStatusBadge = 'bg-emerald-100 text-emerald-800';
                                    $ouStatusText = 'Approved Baseline';
                                } elseif ($ou->pending_pmo_count > 0) {
                                    $ouStatusBadge = 'bg-amber-100 text-amber-800';
                                    $ouStatusText = 'Pending PMO';
                                } elseif ($ou->pending_head_count > 0) {
                                    $ouStatusBadge = 'bg-indigo-100 text-indigo-800';
                                    $ouStatusText = 'Pending Division';
                                } elseif ($ou->draft_plans_count > 0) {
                                    $ouStatusBadge = 'bg-sky-100 text-sky-800';
                                    $ouStatusText = 'Drafting';
                                }
                            @endphp
                            <div
                                class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-300 hover:shadow-sm transition flex flex-col justify-between space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">{{ $ou->code }}</span>
                                        <h4 class="font-extrabold text-xs text-slate-900 mt-1 line-clamp-1" title="{{ $ou->name }}">{{ $ou->name }}</h4>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $ouStatusBadge }}">{{ $ouStatusText }}</span>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex justify-between text-[11px] text-slate-500 font-medium">
                                        <span>Target Indicators: <strong>{{ $ou->total_plans_count }}</strong></span>
                                        <span>Approved: <strong class="text-emerald-700">{{ $ou->approved_plans_count }}</strong></span>
                                    </div>
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        @php
                                            $ouPct = $ou->total_plans_count > 0 ? min(100, round(($ou->approved_plans_count / $ou->total_plans_count) * 100)) : 0;
                                        @endphp
                                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $ouPct }}%"></div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[10px]">
                                    <span class="text-slate-500">Annual: <strong>{{ number_format((float)$ou->total_annual_target, 2) }}</strong></span>
                                    <a href="{{ route('bed2-plans.index', ['operating_unit_id' => $ou->id]) }}"
                                        class="text-emerald-700 font-bold hover:underline">
                                        Manage &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                <p class="text-xs text-slate-500">No active operating units registered yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- RECENT BED-2 BASELINE TRANSACTIONS & SUBMISSIONS -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div
                        class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-emerald-50/60 to-white">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <h3 class="text-sm font-extrabold text-slate-900">Recent BED-2 Target Baselines & Submissions</h3>
                        </div>
                        <a href="{{ route('bed2-plans.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                            View All ({{ $totalPlans }}) &rarr;
                        </a>
                    </div>

                    <div class="p-4 divide-y divide-slate-100">
                        @forelse ($recentBed2Plans as $plan)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                            {{ $plan->operatingUnit?->code ?? 'OU' }}
                                        </span>
                                        <span class="text-xs font-extrabold text-slate-800">
                                            {{ $plan->pap?->code ?? 'PAP' }}
                                        </span>
                                        <span class="text-[11px] text-slate-500">
                                            FY {{ $plan->year }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-700 mt-1 line-clamp-1">
                                        {{ $plan->performanceIndicator?->name ?? 'Performance Indicator' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-4 shrink-0">
                                    <div class="text-right">
                                        <p class="text-[10px] uppercase font-bold text-slate-400">Annual Target</p>
                                        <p class="text-xs font-extrabold text-slate-900">{{ number_format($plan->annual_target, 2) }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $plan->statusBadgeClass() }}">
                                        {{ $plan->statusLabel() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h4 class="text-xs font-extrabold text-slate-800">No BED-2 Targets Encoded Yet for FY {{ $currentYear }}</h4>
                                <p class="text-[11px] text-slate-500 mt-1 max-w-sm mx-auto">
                                    Get started by encoding official annual targets per Program, Activity, Project (PAP) and Operating Unit.
                                </p>
                                <a href="{{ route('bed2-plans.create') }}"
                                    class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                                    <span>Create First BED-2 Target Baseline</span> &rarr;
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- RIGHT SECTION (4 COLS) -->
            <div class="lg:col-span-4 space-y-5" id="unlock-panel">

                <!-- UPPER-RIGHT CARD: MONTHLY CYCLE SCHEDULE & COUNTDOWN -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Reporting Cycle Window</h3>
                            <p class="text-[11px] text-slate-400">Active monthly submission lock</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($activeCycle)
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"
                                    title="Cycle Active"></span>
                            @endif
                            <a href="{{ route('admin.reporting-periods.index') }}"
                                class="text-[11px] font-extrabold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 transition flex items-center gap-1">
                                <span>Manage</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Mini Cycle Gauge -->
                    @if ($activeCycle && $cycleEnd)
                        <div class="flex items-center gap-4 bg-emerald-50/60 border border-emerald-100 p-3.5 rounded-xl">
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex flex-col items-center justify-center font-extrabold text-sm shrink-0 shadow-sm">
                                <span>{{ $cycleEnd->format('d') }}</span>
                                <span
                                    class="text-[9px] uppercase font-bold tracking-wider -mt-1">{{ $cycleEnd->format('M') }}</span>
                            </div>
                            <div class="text-xs">
                                <p class="font-extrabold text-emerald-950">Lock Date:
                                    {{ $cycleEnd->format('M d, g:i A') }}</p>
                                <p class="text-emerald-700 text-[11px] mt-0.5">
                                    @if ($now->lt($cycleStart))
                                        Opens in
                                        {{ $now->diffForHumans($cycleStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                    @elseif ($now->between($cycleStart, $cycleEnd))
                                        {{ $now->diffForHumans($cycleEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                        remaining
                                    @else
                                        Window closed (Lock enforced)
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div
                            class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between gap-3 text-xs">
                            <div>
                                <p class="font-extrabold text-rose-950 flex items-center gap-1">
                                    <span>⚠️ No Active Cycle</span>
                                </p>
                                <p class="text-[11px] text-rose-700 mt-0.5">Submissions locked bureau-wide</p>
                            </div>
                            <a href="{{ route('admin.reporting-periods.index') }}"
                                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-lg text-[11px] shadow transition shrink-0">
                                Set Period &rarr;
                            </a>
                        </div>
                    @endif

                    <!-- Quick Division Submission Checklist -->
                    <div class="space-y-2 text-xs">
                        <p class="text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Division BED-2 Status</p>

                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            @foreach ($operatingUnits->take(8) as $unit)
                                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100 transition">
                                    <span class="font-bold text-slate-800 line-clamp-1">{{ $unit->name }}</span>
                                    @if ($unit->approved_plans_count > 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 shrink-0">Approved</span>
                                    @elseif ($unit->pending_pmo_count > 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 shrink-0">In Review</span>
                                    @elseif ($unit->total_plans_count > 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 shrink-0">Drafted</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-500 shrink-0">Pending</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- LOWER-RIGHT CORNER CARD: AUDIT LOGS & SYSTEM INTEGRITY -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Recent Audit Activities</h3>
                            <p class="text-[11px] text-slate-400">Live trail of system changes</p>
                        </div>
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-[11px] font-bold text-emerald-700 hover:underline">
                            View All &rarr;
                        </a>
                    </div>

                    <div class="space-y-2 text-xs">
                        @forelse ($recentAuditLogs as $log)
                            <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-slate-800 text-[11px]">{{ $log->action }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[11px] text-slate-600">
                                    By: <strong>{{ $log->user?->name ?? 'System' }}</strong>
                                </p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">No audit events recorded yet.</p>
                        @endforelse
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>COA Audit Trail: Enabled</span>
                        <span class="text-emerald-700 font-bold">Secure</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
