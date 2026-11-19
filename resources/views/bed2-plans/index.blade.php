<!--index.blade.php BED-2 Target Plans-->
@extends('layouts.app')
@section('title', 'BED-2 Physical Target Plans')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">BED-2 Physical Target Plans</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                        FY {{ $year }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Annual physical performance commitments, milestone allocations, and baseline locks across operating units.
                </p>
            </div>

            @hasanyrole('superadmin|admin|evaluator|encoder|ou-head')
                <a href="{{ route('bed2-plans.create', ['year' => $year]) }}"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Encode New Target Plan
                </a>
            @endhasanyrole
        </div>

        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Fiscal Year Pills & Metric Counters -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Year Selector -->
            <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200/80 self-start">
                <span class="px-3 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Fiscal Year:</span>
                @foreach ($availableYears as $y)
                    <a href="{{ request()->fullUrlWithQuery(['year' => $y]) }}"
                        class="px-3 py-1 rounded-lg text-xs font-extrabold transition {{ $year === $y ? 'bg-white text-emerald-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        {{ $y }}
                    </a>
                @endforeach
            </div>

            <!-- Mini Metrics Row -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
                    class="px-3 py-1.5 rounded-xl border transition {{ empty(request('status')) ? 'bg-slate-900 text-white border-slate-900 font-extrabold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 font-bold' }}">
                    Total: <span class="font-extrabold">{{ $metrics['total'] }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
                    class="px-3 py-1.5 rounded-xl border transition {{ request('status') === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 font-extrabold' : 'bg-white text-emerald-800 border-emerald-200 hover:bg-emerald-50 font-bold' }}">
                    ✓ Approved: <span class="font-extrabold">{{ $metrics['approved'] }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'head_approved']) }}"
                    class="px-3 py-1.5 rounded-xl border transition {{ request('status') === 'head_approved' ? 'bg-indigo-600 text-white border-indigo-600 font-extrabold' : 'bg-white text-indigo-800 border-indigo-200 hover:bg-indigo-50 font-bold' }}">
                    Pending PMO: <span class="font-extrabold">{{ $metrics['pending_pmo'] }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'submitted_to_head']) }}"
                    class="px-3 py-1.5 rounded-xl border transition {{ request('status') === 'submitted_to_head' ? 'bg-amber-600 text-white border-amber-600 font-extrabold' : 'bg-white text-amber-800 border-amber-200 hover:bg-amber-50 font-bold' }}">
                    Pending Head: <span class="font-extrabold">{{ $metrics['pending_head'] }}</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}"
                    class="px-3 py-1.5 rounded-xl border transition {{ request('status') === 'draft' ? 'bg-slate-700 text-white border-slate-700 font-extrabold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 font-bold' }}">
                    Drafts: <span class="font-extrabold">{{ $metrics['draft'] }}</span>
                </a>
                @if ($metrics['returned'] > 0)
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'returned']) }}"
                        class="px-3 py-1.5 rounded-xl border transition {{ request('status') === 'returned' ? 'bg-rose-600 text-white border-rose-600 font-extrabold' : 'bg-white text-rose-700 border-rose-200 hover:bg-rose-50 font-bold' }}">
                        Needs Revision: <span class="font-extrabold">{{ $metrics['returned'] }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('bed2-plans.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3.5 text-xs">
                <input type="hidden" name="year" value="{{ $year }}">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search indicator name or code..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                </div>

                @hasanyrole('superadmin|admin|evaluator|higher-up')
                    <div>
                        <select name="operating_unit_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                            <option value="">All Operating Units</option>
                            @foreach ($operatingUnits as $ou)
                                <option value="{{ $ou->id }}" {{ request('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold border border-slate-200 truncate">
                        🏢 {{ auth()->user()->operatingUnit?->name ?? 'Assigned Operating Unit' }}
                    </div>
                @endhasanyrole

                <div>
                    <select name="pap_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                        <option value="">All Programs / PAPs</option>
                        @foreach ($paps as $p)
                            <option value="{{ $p->id }}" {{ request('pap_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ Str::limit($p->name, 35) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-sm transition">Filter</button>
                    <a href="{{ route('bed2-plans.index', ['year' => $year]) }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl border border-slate-200 font-bold text-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- BED-2 Plans Matrix Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 uppercase tracking-wider font-extrabold">
                        <tr>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4">PAP & Performance Indicator</th>
                            <th class="p-4 text-center">Unit</th>
                            <th class="p-4 text-right">Annual Target</th>
                            <th class="p-4 text-center">Quarterly Breakdown (Q1–Q4)</th>
                            <th class="p-4 text-center">Review State</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($plans as $plan)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4 font-bold text-slate-900 whitespace-nowrap">
                                    <p>{{ $plan->operatingUnit?->name }}</p>
                                    <p class="text-[10px] text-emerald-800 font-semibold uppercase">{{ $plan->operatingUnit?->code }}</p>
                                </td>

                                <td class="p-4 max-w-sm">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $plan->pap?->code }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $plan->performanceIndicator?->type === 'outcome' ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }}">
                                            {{ $plan->performanceIndicator?->type }}
                                        </span>
                                    </div>
                                    <p class="font-bold text-slate-900 leading-snug">
                                        {{ $plan->performanceIndicator?->name }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $plan->performanceIndicator?->code }} &bull; {{ $plan->pap?->name }}
                                    </p>
                                    @if ($plan->remarks)
                                        <p class="text-[10px] text-rose-600 italic mt-1 bg-rose-50/60 p-1 rounded-md border border-rose-100">
                                            💬 {{ $plan->remarks }}
                                        </p>
                                    @endif
                                </td>

                                <td class="p-4 text-center font-semibold text-slate-600 whitespace-nowrap">
                                    {{ $plan->performanceIndicator?->unit_of_measure }}
                                </td>

                                <td class="p-4 text-right whitespace-nowrap">
                                    <span class="text-sm font-black text-slate-900">
                                        {{ number_format($plan->annual_target, 2) }}
                                    </span>
                                </td>

                                <td class="p-4">
                                    <div class="grid grid-cols-4 gap-1 text-center font-mono text-[10px] min-w-[200px]">
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q1</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q1_target, 0) }}</p>
                                        </div>
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q2</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q2_target, 0) }}</p>
                                        </div>
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q3</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q3_target, 0) }}</p>
                                        </div>
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q4</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q4_target, 0) }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold border uppercase tracking-wider {{ $plan->statusBadgeClass() }}">
                                        {{ $plan->statusLabel() }}
                                    </span>
                                </td>

                                <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('bed2-plans.show', $plan) }}"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-block">
                                        View
                                    </a>

                                    @if ($plan->isEditable() && ($plan->operating_unit_id === auth()->user()->operating_unit_id || auth()->user()->hasAnyRole(['superadmin', 'admin'])))
                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[11px] border border-emerald-200 transition inline-block">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('bed2-plans.submit-head', $plan) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Submit this plan to Division Chief for review?');"
                                                class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[11px] border border-amber-200 transition">
                                                Submit
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-10 text-center text-slate-400">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <div class="text-3xl">🎯</div>
                                        <p class="font-bold text-slate-700">No BED-2 Target Plans Found</p>
                                        <p class="text-xs text-slate-400">No physical target plans matching the selected year and filters. Click "Encode New Target Plan" to start.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $plans->links() }}
            </div>
        </div>
    </div>
@endsection
