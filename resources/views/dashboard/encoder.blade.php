@extends('layouts.app')
@section('title', 'OU Encoder Workspace')

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
                    <span>✍️</span> OU Accomplishment Encoding Desk &bull;
                    {{ $operatingUnit?->name ?? 'Operating Unit' }}
                </div>
                <div
                    class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                    <span class="text-slate-300">Fiscal Period:</span>
                    <span class="font-bold text-emerald-400">FY {{ $currentYear }}</span>
                    <span class="text-slate-400">&bull;</span>
                    @if ($activeCycle && $cycleEnd)
                        <span class="text-white font-medium">Window: Closes {{ $cycleEnd->format('M d') }}</span>
                    @else
                        <span class="text-slate-300 font-medium">Standard Window</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">
                        {{ $operatingUnit?->name ?? 'Operating Unit' }} Physical Inputs</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Encode monthly actual physical accomplishments, establish BED-2 annual target baselines, and
                        submit them for Division Chief endorsement.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Approved Baselines</p>
                        <p class="text-base font-extrabold text-emerald-400">{{ $approvedCount }} / {{ $totalIndicators }} Indicators</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Drafts to Submit</p>
                        <p class="text-base font-extrabold text-amber-400">{{ $draftCount }} Left</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Assigned Indicators</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/60 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalIndicators }} <span
                            class="text-sm font-semibold text-slate-400">PAPs</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-lg">FY {{ $currentYear }}</span>
                        <a href="{{ route('bed2-plans.create') }}" class="text-emerald-700 font-bold hover:underline">+ New Target</a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Draft Plans</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $draftCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Awaiting Chief Submission</span>
                        <span class="text-slate-400 font-medium">Ready to Submit</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Under Review</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-200/60 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $submittedToHeadCount + $endorsedToPmoCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-indigo-700 font-bold bg-indigo-50 px-2 py-0.5 rounded-lg">{{ $submittedToHeadCount }} Chief / {{ $endorsedToPmoCount }} PMO</span>
                        <span class="text-slate-400 font-medium">Locked</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Approved Baselines</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $approvedCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">Official Baseline</span>
                        <span class="text-slate-400 font-medium">Locked</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($returnedCount > 0)
            <!-- Returned Plans Alert Box -->
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-extrabold text-sm text-rose-900">{{ $returnedCount }} Target Plan(s) Returned for Revision</h4>
                    <p class="text-xs text-rose-700 mt-0.5">Please check the reviewer's remarks below, make the necessary corrections, and re-submit to your Division Chief.</p>
                </div>
            </div>
        @endif

        <!-- Active Indicators Checklist -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">{{ $operatingUnit?->name }} BED-2 Target Baselines</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Manage annual physical targets and monthly accomplishment submissions for FY {{ $currentYear }}.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('bed2-plans.create') }}"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>New Target Plan</span>
                    </a>
                </div>
            </div>

            <div class="p-5 space-y-3.5">
                @forelse ($myPlans as $plan)
                    <div
                        class="p-4 rounded-xl border {{ $plan->status === 'returned' ? 'border-rose-200 bg-rose-50/30' : ($plan->status === 'approved' ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50') }} flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                    {{ $plan->pap?->code }}
                                </span>
                                <span class="font-bold text-xs text-slate-900">
                                    {{ $plan->performanceIndicator?->name }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">
                                Annual Target: <strong class="text-slate-800">{{ number_format($plan->annual_target, 2) }}</strong> &bull;
                                Q1: {{ number_format($plan->q1_target, 2) }} &bull;
                                Q2: {{ number_format($plan->q2_target, 2) }} &bull;
                                Q3: {{ number_format($plan->q3_target, 2) }} &bull;
                                Q4: {{ number_format($plan->q4_target, 2) }}
                            </p>
                            @if ($plan->remarks)
                                <p class="text-xs text-rose-700 italic mt-1">Remarks: {{ $plan->remarks }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2.5 shrink-0">
                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $plan->statusBadgeClass() }}">
                                {{ $plan->statusLabel() }}
                            </span>
                            @if ($plan->isEditable())
                                <form action="{{ route('bed2-plans.submit-head', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-sm transition">
                                        Submit to Head
                                    </button>
                                </form>
                                <a href="{{ route('bed2-plans.edit', $plan) }}"
                                    class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                    Edit Target
                                </a>
                            @else
                                <a href="{{ route('bed2-plans.show', $plan) }}"
                                    class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                                    View Baseline
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800">No BED-2 Targets Encoded Yet for {{ $operatingUnit?->name ?? 'Your Operating Unit' }}</h4>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-sm mx-auto">
                            Encode your annual physical target commitments per performance indicator for FY {{ $currentYear }}.
                        </p>
                        <a href="{{ route('bed2-plans.create') }}"
                            class="inline-flex items-center gap-1.5 mt-3 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm">
                            <span>Create First Target Baseline</span> &rarr;
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
