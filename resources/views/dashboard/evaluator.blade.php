<!--evaluator.blade.php-->
@extends('layouts.app')
@section('title', 'PMO Evaluator Workspace')
@section('content')
    @php
        $evaluatorActiveCycle = \App\Models\ReportingPeriod::where('is_active', true)->first();
        $cycleNow = \Illuminate\Support\Carbon::now();
        $cycleStart = $evaluatorActiveCycle
            ? \Illuminate\Support\Carbon::parse($evaluatorActiveCycle->window_start)
            : null;
        $cycleEnd = $evaluatorActiveCycle ? \Illuminate\Support\Carbon::parse($evaluatorActiveCycle->window_end) : null;
    @endphp
    <div class="space-y-5">
        <!-- Hero Briefing Bar (Corner-Anchored Content) -->
        <div
            class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex items-center justify-between relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                    <span>🔍</span> PMO Performance Evaluation Desk
                </div>
                @if ($evaluatorActiveCycle)
                    <a href="{{ route('admin.reporting-periods.index') }}"
                        class="flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-slate-300">Reporting Cycle:</span>
                        <span class="font-bold text-emerald-400">{{ $evaluatorActiveCycle->name }}</span>
                        <span class="text-slate-400">&bull;</span>
                        @if ($cycleNow->lt($cycleStart))
                            <span class="text-sky-300 font-medium">Opens in
                                {{ $cycleNow->diffForHumans($cycleStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                        @elseif ($cycleNow->between($cycleStart, $cycleEnd))
                            <span class="text-white font-medium">Closes in
                                {{ $cycleNow->diffForHumans($cycleEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
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
                        Review and certify division monthly physical submissions, verify Means of Verification (MOVs), and
                        analyze slippages.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Validation Queue</p>
                        <p class="text-base font-extrabold text-amber-400">5 Divisions Pending</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Cleared & Approved</p>
                        <p class="text-base font-extrabold text-emerald-400">7 Divisions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Tiles (Corner-Anchored) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Awaiting Review</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">5 <span
                            class="text-sm font-semibold text-slate-400">Divisions</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Pending PMO
                            Sign-off</span>
                        <span class="text-slate-400">Chief Endorsed</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Validated &
                        Approved</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">7 <span
                            class="text-sm font-semibold text-slate-400">Divisions</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">58.3% Cleared</span>
                        <span class="text-slate-400">Ready for BAR-1</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">High Slippage
                        (&ge;15%)</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-rose-600 tracking-tight">3 <span
                            class="text-sm font-semibold text-slate-400">Indicators</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-lg">RCA Required</span>
                        <span class="text-slate-400">Action Mandated</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">MOVs Uploaded</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">42 <span
                            class="text-sm font-semibold text-slate-400">Files</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">100% PDF</span>
                        <span class="text-slate-400">COA Compliant</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Division Evaluation Queue Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Division Submissions Evaluation Queue</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Physical accomplishment reports submitted for PMO review and
                        sign-off.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($evaluatorActiveCycle)
                        <a href="{{ route('admin.reporting-periods.index') }}"
                            class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            <span>Cycle: {{ $evaluatorActiveCycle->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('admin.reporting-periods.index') }}"
                            class="px-3 py-1 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                            <span>⚠️ No Active Cycle (Configure &rarr;)</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                        <tr>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4">Submission Date</th>
                            <th class="p-4">Physical Accomplishment</th>
                            <th class="p-4">Slippage Status</th>
                            <th class="p-4">MOVs Attached</th>
                            <th class="p-4 text-right">Review Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $sampleOUs = [
                                [
                                    'code' => 'CRD',
                                    'name' => 'Crop Research & Development Division',
                                    'date' => 'Oct 22, 2026',
                                    'rate' => '94.2%',
                                    'slippage' => '-1.8%',
                                    'slippage_color' => 'text-slate-700 bg-slate-100',
                                    'movs' => '8 files',
                                    'status' => 'Pending Review',
                                ],
                                [
                                    'code' => 'CPD',
                                    'name' => 'Crop Pest Management Division',
                                    'date' => 'Oct 23, 2026',
                                    'rate' => '78.5%',
                                    'slippage' => '-16.5%',
                                    'slippage_color' => 'text-rose-700 bg-rose-50 border border-rose-200',
                                    'movs' => '5 files',
                                    'status' => 'Critical RCA Needed',
                                ],
                                [
                                    'code' => 'NPQSD',
                                    'name' => 'National Plant Quarantine Services Division',
                                    'date' => 'Oct 21, 2026',
                                    'rate' => '98.1%',
                                    'slippage' => '+3.2%',
                                    'slippage_color' => 'text-emerald-700 bg-emerald-50 border border-emerald-200',
                                    'movs' => '12 files',
                                    'status' => 'Pending Review',
                                ],
                                [
                                    'code' => 'NSQCS',
                                    'name' => 'National Seed Quality Control Services',
                                    'date' => 'Oct 24, 2026',
                                    'rate' => '91.0%',
                                    'slippage' => '-4.0%',
                                    'slippage_color' => 'text-slate-700 bg-slate-100',
                                    'movs' => '9 files',
                                    'status' => 'Pending Review',
                                ],
                            ];
                        @endphp

                        @foreach ($sampleOUs as $ou)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4">
                                    <div class="font-extrabold text-slate-900">{{ $ou['name'] }}</div>
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">{{ $ou['code'] }}</span>
                                </td>
                                <td class="p-4 text-slate-600 font-medium">{{ $ou['date'] }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-slate-900">{{ $ou['rate'] }}</span>
                                        <div class="w-16 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full"
                                                style="width: {{ $ou['rate'] }}"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $ou['slippage_color'] }}">
                                        {{ $ou['slippage'] }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-600 font-medium">{{ $ou['movs'] }}</td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold text-xs transition border border-emerald-200/60">
                                            Validate
                                        </button>
                                        <button
                                            class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                                            View MOVs
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
