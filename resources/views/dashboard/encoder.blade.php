<!--encoder.blade.php-->
@extends('layouts.app')
@section('title', 'OU Encoder Workspace')

@section('content')
    <div class="space-y-5">

        <!-- Hero Briefing Bar (Corner-Anchored) -->
        <div
            class="bg-gradient-to-r from-slate-900 via-botanic-950 to-slate-900 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden flex flex-col justify-between gap-5">
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex items-center justify-between relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold">
                    <span>✍️</span> OU Accomplishment Encoding Desk &bull;
                    {{ Auth::user()->operatingUnit?->name ?? 'Operating Unit' }}
                </div>
                <div
                    class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                    <span class="text-slate-300">Lock Window:</span>
                    <span class="font-bold text-emerald-400">Open until 26th, 11:59 PM</span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-white font-medium">{{ date('F Y') }}</span>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">
                        {{ Auth::user()->operatingUnit?->name ?? 'Operating Unit' }} Physical Inputs</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Encode monthly actual physical accomplishments, attach required Means of Verification (MOVs), and
                        document variance justifications.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Encoding Progress</p>
                        <p class="text-base font-extrabold text-emerald-400">75% (6 / 8 Done)</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Pending MOVs</p>
                        <p class="text-base font-extrabold text-amber-400">2 Files Left</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Tiles (Corner-Anchored) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Assigned
                        Indicators</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">8 <span
                            class="text-sm font-semibold text-slate-400">PAPs</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">Active BED-2</span>
                        <span class="text-slate-400 font-medium">FY {{ date('Y') }} Allocation</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Input Completed</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">6 <span
                            class="text-sm font-semibold text-slate-400">/ 8</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">75% Complete</span>
                        <span class="text-slate-400 font-medium">2 Items Left</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Uploaded MOVs</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">6 <span
                            class="text-sm font-semibold text-slate-400">Files</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">2 Pending Uploads</span>
                        <span class="text-slate-400 font-medium">PDF &le; 10MB</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Report State</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">Draft</h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-bold bg-slate-100 px-2 py-0.5 rounded-lg">In Encoding</span>
                        <span class="text-slate-400 font-medium">Submit to OU Head</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Indicators Checklist -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">{{ date('F Y') }} Physical Accomplishment
                        Checklist</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Input your actual delivery figures and verify that each item
                        has supporting documentation.</p>
                </div>
                <button
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Forward to Division Chief</span>
                </button>
            </div>

            <div class="p-5 space-y-3.5">
                <div
                    class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">CRD-01</span>
                            <span class="font-bold text-xs text-slate-900">Number of certified seeds inspected and tested
                                for viability</span>
                        </div>
                        <p class="text-xs text-slate-500">Target for {{ date('F') }}: <strong
                                class="text-slate-800">450 units</strong> &bull; Encoded Actual: <strong
                                class="text-emerald-700">465 units</strong> (+3.3% variance)</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-100/70 px-3 py-1 rounded-xl">✓ MOV
                            Attached</span>
                        <button
                            class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Edit
                            Entry</button>
                    </div>
                </div>

                <div
                    class="p-4 rounded-xl border border-amber-200 bg-amber-50/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800">CRD-02</span>
                            <span class="font-bold text-xs text-slate-900">Number of technology demonstrations
                                conducted</span>
                        </div>
                        <p class="text-xs text-slate-500">Target for {{ date('F') }}: <strong
                                class="text-slate-800">25 demos</strong> &bull; Actual: <strong
                                class="text-amber-700">Pending Input</strong></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-extrabold text-white shadow-sm transition">
                            Encode Actuals &rarr;
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
