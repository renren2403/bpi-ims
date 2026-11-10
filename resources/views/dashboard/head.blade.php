@extends('layouts.app')
@section('title', 'Division Chief Workspace')

@section('content')
<div class="space-y-5">
    
    <!-- Hero Briefing Bar (Corner-Anchored) -->
    <div class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
        <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between relative z-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                <span>🏛️</span> Division Chief Command Desk &bull; {{ Auth::user()->operatingUnit?->name ?? 'Operating Unit' }}
            </div>
            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                <span class="text-slate-300">Reporting Cycle:</span>
                <span class="font-bold text-amber-400">Certification Pending</span>
                <span class="text-slate-400">&bull;</span>
                <span class="text-white font-medium">{{ date('F Y') }}</span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight">{{ Auth::user()->operatingUnit?->name ?? 'Operating Unit' }} Accomplishments</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Review encoded monthly actuals, examine variance explanations, and certify reports for PMO submission.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Division Accomplishment</p>
                    <p class="text-base font-extrabold text-emerald-400">93.8% Overall</p>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-right">
                    <p class="text-[10px] uppercase font-bold text-slate-400">Encoded Indicators</p>
                    <p class="text-base font-extrabold text-white">8 / 8 Done</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric Tiles (Corner-Anchored) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Division Progress</span>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">93.8%</h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">On Track</span>
                    <span class="text-slate-400 font-medium">Goal: 90%</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Indicators Encoded</span>
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">8 <span class="text-lg font-semibold text-slate-400">/ 8</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">100% Encoded</span>
                    <span class="text-slate-400 font-medium">Ready for Endorsement</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Slippage Alerts</span>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">0 <span class="text-sm font-semibold text-slate-400">Critical</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">No Critical Lag</span>
                    <span class="text-slate-400 font-medium">Within Threshold</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Unlock Inquiries</span>
                <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">0 <span class="text-sm font-semibold text-slate-400">Active</span></h3>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-bold bg-slate-100 px-2 py-0.5 rounded-lg">Report Unlocked</span>
                    <span class="text-slate-400 font-medium">Open Window</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Indicator Endorsement Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">Division Accomplishment Review & Certification</h3>
                <p class="text-xs text-slate-400 mt-0.5">Verify actual accomplishment figures and attached MOVs before endorsing to PMO.</p>
            </div>
            
            <button class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Certify & Submit to PMO</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                    <tr>
                        <th class="p-4">Indicator Code & Description</th>
                        <th class="p-4 text-center">BED-2 Target</th>
                        <th class="p-4 text-center">Actual Accomplished</th>
                        <th class="p-4 text-center">Variance / Slippage</th>
                        <th class="p-4">MOVs Attached</th>
                        <th class="p-4 text-right">Verification</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $indicators = [
                            ['code' => 'CRD-01', 'title' => 'Number of certified seeds inspected and tested for viability', 'target' => 450, 'actual' => 465, 'variance' => '+3.3%', 'movs' => 'Lab_Cert_Batch10.pdf (2.4 MB)', 'status' => 'Verified'],
                            ['code' => 'CRD-02', 'title' => 'Number of crop management technical advisories disseminated', 'target' => 120, 'actual' => 118, 'variance' => '-1.6%', 'movs' => 'Advisories_Oct2026.pdf (1.1 MB)', 'status' => 'Verified'],
                            ['code' => 'CRD-03', 'title' => 'Number of germplasm accessions regenerated and conserved', 'target' => 80, 'actual' => 82, 'variance' => '+2.5%', 'movs' => 'Germplasm_Log_Q4.pdf (4.2 MB)', 'status' => 'Verified'],
                        ];
                    @endphp

                    @foreach($indicators as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200/50">{{ $item['code'] }}</span>
                                <p class="font-bold text-slate-800 mt-1">{{ $item['title'] }}</p>
                            </td>
                            <td class="p-4 text-center font-extrabold text-slate-700">{{ number_format($item['target']) }}</td>
                            <td class="p-4 text-center font-extrabold text-slate-900">{{ number_format($item['actual']) }}</td>
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60">
                                    {{ $item['variance'] }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 font-medium">
                                <span class="text-blue-600 hover:underline cursor-pointer">📎 {{ $item['movs'] }}</span>
                            </td>
                            <td class="p-4 text-right">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    ✓ {{ $item['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
