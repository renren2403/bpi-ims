@extends('layouts.app')
@section('title', 'Division Chief Workspace')

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
                    <span>🏛️</span> Division Chief Command Desk &bull;
                    {{ $operatingUnit?->name ?? 'Operating Unit' }}
                </div>
                <div
                    class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/10 text-xs">
                    <span class="text-slate-300">Fiscal Year:</span>
                    <span class="font-bold text-emerald-400">FY {{ $currentYear }}</span>
                    <span class="text-slate-400">&bull;</span>
                    @if ($activeCycle)
                        <span class="text-white font-medium">{{ $activeCycle->name }}</span>
                    @else
                        <span class="text-slate-300 font-medium">Standard Window</span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10 pt-1">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight">
                        {{ $operatingUnit?->name ?? 'Operating Unit' }} Oversight</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        Review and endorse encoded annual BED-2 target baselines, track monthly actual accomplishments,
                        and certify reports for PMO validation.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Pending Endorsement</p>
                        <p class="text-base font-extrabold text-amber-400">{{ $pendingHeadCount }} Plans</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Approved Baseline</p>
                        <p class="text-base font-extrabold text-emerald-400">{{ $approvedCount }} / {{ $totalIndicators }} Indicators</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Tiles -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Awaiting Chief Review</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $pendingHeadCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-lg">Encoded by Staff</span>
                        <a href="{{ route('bed2-plans.index') }}" class="text-emerald-700 font-bold hover:underline">Review &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Endorsed to PMO</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-200/60 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $endorsedCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-indigo-700 font-bold bg-indigo-50 px-2 py-0.5 rounded-lg">PMO Action Pending</span>
                        <span class="text-slate-400">Certified by You</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Approved Baseline</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $approvedCount }} <span
                            class="text-sm font-semibold text-slate-400">/ {{ $totalIndicators }}</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-lg">Official Baseline</span>
                        <span class="text-slate-400">Locked by PMO</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Draft / Returned</span>
                    <div
                        class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $draftCount + $returnedCount }} <span
                            class="text-sm font-semibold text-slate-400">Plans</span></h3>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-bold bg-slate-100 px-2 py-0.5 rounded-lg">{{ $draftCount }} Drafts</span>
                        <span class="text-rose-600 font-semibold">{{ $returnedCount }} Returned</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Division Endorsements Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">BED-2 Baseline Plans Awaiting Your Endorsement</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Verify annual target allocation and quarterly distributions submitted by division encoders.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('bed2-plans.index') }}"
                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                        Open Batch Action Console &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-100">
                        <tr>
                            <th class="p-4">PAP & Indicator</th>
                            <th class="p-4 text-center">Annual Target</th>
                            <th class="p-4 text-center">Q1</th>
                            <th class="p-4 text-center">Q2</th>
                            <th class="p-4 text-center">Q3</th>
                            <th class="p-4 text-center">Q4</th>
                            <th class="p-4">Encoded By</th>
                            <th class="p-4 text-right">Review Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pendingHeadPlans as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                        {{ $item->pap?->code }}
                                    </span>
                                    <p class="font-bold text-slate-800 mt-1 line-clamp-1">
                                        {{ $item->performanceIndicator?->name }}
                                    </p>
                                </td>
                                <td class="p-4 text-center font-extrabold text-slate-900">
                                    {{ number_format($item->annual_target, 2) }}
                                </td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($item->q1_target, 2) }}</td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($item->q2_target, 2) }}</td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($item->q3_target, 2) }}</td>
                                <td class="p-4 text-center text-slate-600 font-medium">{{ number_format($item->q4_target, 2) }}</td>
                                <td class="p-4 text-slate-600">
                                    <span class="font-bold text-slate-800">{{ $item->submittedBy?->name ?? 'Staff Encoder' }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('bed2-plans.head-approve', $item) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                                                Endorse to PMO
                                            </button>
                                        </form>
                                        <a href="{{ route('bed2-plans.edit', $item) }}"
                                            class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-500">
                                    <p class="font-extrabold text-xs text-slate-800">No Submissions Currently Awaiting Your Endorsement</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">All staff submissions have been reviewed or are still in draft state.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Division Indicators Summary Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">All {{ $operatingUnit?->name }} Indicators</h3>
                        <p class="text-xs text-slate-400">Total committed targets for FY {{ $currentYear }}</p>
                    </div>
                    <a href="{{ route('bed2-plans.create') }}"
                        class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl transition">
                        + Add Target
                    </a>
                </div>

                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto pr-1">
                    @forelse ($myPlans as $plan)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">{{ $plan->pap?->code }}</span>
                                    <span class="font-bold text-slate-800 line-clamp-1">{{ $plan->performanceIndicator?->name }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">Target: {{ number_format($plan->annual_target, 2) }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border shrink-0 {{ $plan->statusBadgeClass() }}">
                                {{ $plan->statusLabel() }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">No BED-2 targets encoded for this division yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Division Staff Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Division Staff</h3>
                        <p class="text-xs text-slate-400">Authorized encoders in this unit</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                        {{ $staffMembers->count() }} Members
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    @forelse ($staffMembers as $member)
                        <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-800">{{ $member->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $member->email }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Active</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No other staff members registered yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection
