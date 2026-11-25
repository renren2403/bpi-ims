<!--index.blade.php BED-2 Target Plans-->
@extends('layouts.app')
@section('title', 'BED-2 Physical Target Plans')

@section('content')
    <div class="space-y-6">
        @php
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $isPmo = $user->can('bed2.manage_all_units') || $user->hasAnyRole(['superadmin', 'admin', 'evaluator']);
            $isEncoder = !$isPmo && ($user->hasRole('encoder') || $user->can('bed2.submit_head'));
            $isHead = !$isPmo && ($user->hasRole('ou-head') || $user->can('bed2.head_endorse'));
            $selectedOuId = request('operating_unit_id') ?: ($isPmo ? null : $user->operating_unit_id);
            $selectedOu = $selectedOuId ? $operatingUnits->firstWhere('id', $selectedOuId) : null;
        @endphp

        <!-- Header & Top Action Controls -->
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">BED-2 Physical Target Plans</h2>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                        FY {{ $year }}
                    </span>
                    @if ($selectedOu)
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 uppercase tracking-wide border border-slate-200">
                            {{ $selectedOu->name }} ({{ $selectedOu->code }})
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Annual physical performance commitments, milestone allocations, and baseline locks across operating
                    units.
                </p>
            </div>

            <!-- Contextual Workflow Action Buttons -->
            <div class="flex items-center gap-2.5 flex-wrap self-start lg:self-auto">
                <!-- 1. OU Encoder Batch Submit -->
                @if ($isEncoder && ($metrics['draft'] > 0 || $metrics['returned'] > 0))
                    <form method="POST" action="{{ route('bed2-plans.batch-submit-head') }}" class="inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="operating_unit_id" value="{{ $user->operating_unit_id }}">
                        <button type="submit"
                            onclick="return confirm('Submit all {{ $metrics['draft'] + $metrics['returned'] }} draft targets to Division Chief for review?');"
                            class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-amber-600/20 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Submit to Division Chief ({{ $metrics['draft'] + $metrics['returned'] }})</span>
                        </button>
                    </form>
                @endif

                <!-- 2. Division Chief Batch Endorse -->
                @if ($isHead && $metrics['pending_head'] > 0)
                    <form method="POST" action="{{ route('bed2-plans.batch-head-approve') }}" class="inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="operating_unit_id" value="{{ $user->operating_unit_id }}">
                        <button type="submit"
                            onclick="return confirm('Endorse all {{ $metrics['pending_head'] }} target plans to PMO for final validation?');"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Endorse to PMO ({{ $metrics['pending_head'] }})</span>
                        </button>
                    </form>

                    <button type="button" onclick="openReturnModal('{{ $user->operating_unit_id }}')"
                        class="px-3.5 py-2.5 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-xs rounded-xl transition">
                        ↩ Return to Encoder
                    </button>
                @endif

                <!-- 3. PMO Approving & Governance Actions -->
                @if ($isPmo)
                    @if ($selectedOuId && ($metrics['pending_pmo'] > 0 || $metrics['pending_head'] > 0))
                        <form method="POST" action="{{ route('bed2-plans.batch-pmo-approve') }}" class="inline">
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="operating_unit_id" value="{{ $selectedOuId }}">
                            <button type="submit"
                                onclick="return confirm('Officially approve and lock annual baseline targets for {{ $selectedOu?->name }} (FY {{ $year }})?');"
                                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Lock &amp; Approve Baseline</span>
                            </button>
                        </form>

                        <button type="button" onclick="openReturnModal('{{ $selectedOuId }}')"
                            class="px-3.5 py-2.5 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-xs rounded-xl transition">
                            ↩ Return for Revision
                        </button>
                    @endif
                @endif

                <!-- Official Government DBM BED-2 Print & CSV Export Actions -->
                <div class="inline-flex rounded-xl shadow-sm">
                    <a href="{{ route('bed2-plans.export.print', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                        target="_blank"
                        class="px-3.5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs rounded-l-xl transition flex items-center gap-1.5"
                        title="Print Official DBM BED Form No. 2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Print DBM BED-2</span>
                    </a>
                    <a href="{{ route('bed2-plans.export.csv', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                        class="px-3 py-2.5 bg-white border-y border-r border-slate-200 hover:bg-slate-50 text-emerald-700 font-extrabold text-xs rounded-r-xl transition flex items-center gap-1"
                        title="Download CSV / Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>CSV</span>
                    </a>
                </div>
                {{-- Rollover / Baseline Initialization: PMO & Administrative Governance Only --}}
                @if ($isPmo)
                    <button type="button" onclick="openRolloverModal()"
                        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5"
                        title="Initialize indicators for FY {{ $year }} from previous year or active catalog">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Initialize FY {{ $year }}</span>
                    </button>
                @endif



                <!-- Encode Target Plan Button (Accessible to both PMO and OU Encoder) -->
                @can('bed2.create')
                    <a href="{{ route('bed2-plans.create', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ $isPmo && $selectedOu ? "Add PAP for {$selectedOu->code}" : 'Encode New Target Plan' }}</span>
                    </a>
                @else
                    @hasanyrole('superadmin|admin|evaluator|encoder')
                        <a href="{{ route('bed2-plans.create', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                            class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Encode New Target Plan</span>
                        </a>
                    @endhasanyrole
                @endcan
            </div>
        </div>

        @if (session('status'))
            <div
                class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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

                @if ($isPmo)
                    <div>
                        <select name="operating_unit_id" onchange="this.form.submit()"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                            <option value="">All Operating Units (Bureau-Wide)</option>
                            @foreach ($operatingUnits as $ou)
                                <option value="{{ $ou->id }}"
                                    {{ request('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div
                        class="px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold border border-slate-200 truncate">
                        🏢 {{ auth()->user()->operatingUnit?->name ?? 'Assigned Operating Unit' }}
                    </div>
                @endif

                <div>
                    <select name="pap_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                        <option value="">All Programs / PAPs</option>
                        @foreach ($paps as $p)
                            <option value="{{ $p->id }}" {{ request('pap_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ Str::limit($p->name, 65) }}
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
            <div class="overflow-x-auto sheet-scroll-container">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 border-b border-slate-100 text-slate-400 uppercase tracking-wider font-extrabold">
                        <tr>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4">PAP &amp; Performance Indicator</th>
                            <th class="p-4 text-center">Unit</th>
                            <th class="p-4 text-right">Annual Target</th>
                            <th class="p-4 text-center">Quarterly Breakdown (Q1–Q4)</th>
                            <th class="p-4 text-center">Review State</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>



                    <tbody class="divide-y divide-slate-100">
                        @php
                            $currentCategory = null;
                            $currentPapId = null;
                            $lastSubHeader = null;
                        @endphp

                        @forelse ($plans as $plan)
                            @php
                                $pap = $plan->pap;
                                $categoryTitle = !empty(trim($pap?->category ?? ''))
                                    ? strtoupper(trim($pap->category))
                                    : 'OPERATIONS';

                                $indicatorFullName = $plan->performanceIndicator?->name ?? 'Indicator';
                                $subProgram = trim($plan->performanceIndicator?->sub_program ?? '');
                                $activity = trim($plan->performanceIndicator?->activity ?? '');
                                $subActivity = trim($plan->performanceIndicator?->sub_activity ?? '');

                                $subHeader = null;
                                $displayName = $indicatorFullName;

                                if (!empty($activity)) {
                                    $subHeader = (!empty($subProgram) ? $subProgram . ' › ' : '') . $activity;
                                } elseif (!empty($subProgram)) {
                                    $subHeader = $subProgram;
                                } elseif (str_contains($indicatorFullName, ':')) {
                                    [$prefix, $rest] = explode(':', $indicatorFullName, 2);
                                    $subHeader = trim($prefix);
                                    $displayName = trim($rest);
                                }
                            @endphp

                            {{-- 1. Category Banner (e.g. II. SUPPORT TO OPERATIONS) --}}
                            @if ($currentCategory !== $categoryTitle)
                                @php
                                    $currentCategory = $categoryTitle;
                                    $currentPapId = null;
                                @endphp
                                <tr class="bg-slate-800 text-white font-black text-xs uppercase tracking-wider">
                                    <td colspan="7" class="p-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span>{{ $categoryTitle }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            {{-- 2. PAP Banner (e.g. CPMD - Crop Pest Management) --}}
                            @if ($currentPapId !== $plan->pap_id)
                                @php
                                    $currentPapId = $plan->pap_id;
                                    $lastSubHeader = null;
                                @endphp
                                <tr class="bg-emerald-50/70 border-t border-b border-emerald-200">
                                    <td colspan="7"
                                        class="p-2.5 px-4 font-bold text-emerald-950 text-xs tracking-wide uppercase">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-mono text-emerald-800 font-extrabold">{{ $pap?->code }}</span>
                                            <span class="text-emerald-400">&mdash;</span>
                                            <span>{{ $pap?->name }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            {{-- 3. Sub-Breakdown Divider Row (e.g. Beneficiaries) --}}
                            @if ($subHeader && $subHeader !== $lastSubHeader)
                                @php $lastSubHeader = $subHeader; @endphp
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-700">
                                    <td colspan="7" class="py-1.5 px-6 font-bold italic text-xs tracking-wide">
                                        {{ $subHeader }}
                                    </td>
                                </tr>
                            @elseif (!$subHeader)
                                @php $lastSubHeader = null; @endphp
                            @endif
                            {{-- 4. Target Plan Data Row (7 columns) --}}
                            <tr class="hover:bg-slate-50/70 transition group">
                                <td class="p-4 font-bold text-slate-800 whitespace-nowrap">
                                    {{ $plan->operatingUnit?->name }}
                                </td>
                                <td class="p-4 max-w-sm">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $plan->performanceIndicator?->type === 'outcome' ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }}">
                                            {{ $plan->performanceIndicator?->type }}
                                        </span>
                                    </div>
                                    @if ($subActivity)
                                        <p class="font-extrabold text-slate-900 text-xs leading-snug">
                                            {{ $subActivity }}
                                        </p>
                                        <p class="text-[11px] text-slate-600 leading-snug mt-1">
                                            <span class="font-bold text-slate-400 text-[10px] uppercase">Key Indicator:</span> {{ $displayName }}
                                        </p>
                                    @else
                                        <p class="font-bold text-slate-900 leading-snug">
                                            {{ $displayName }}
                                        </p>
                                    @endif
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $plan->performanceIndicator?->code }}
                                    </p>
                                    @if ($plan->remarks)
                                        <p
                                            class="text-[10px] text-rose-600 italic mt-1 bg-rose-50/60 p-1.5 rounded-md border border-rose-100">
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
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q1_target, 0) }}
                                            </p>
                                        </div>
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q2</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q2_target, 0) }}
                                            </p>
                                        </div>
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q3</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q3_target, 0) }}
                                            </p>
                                        </div>
                                        <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-200/60">
                                            <p class="text-slate-400 uppercase text-[9px]">Q4</p>
                                            <p class="font-bold text-slate-800">{{ number_format($plan->q4_target, 0) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 text-center whitespace-nowrap space-y-1">
                                    <div>
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold border uppercase tracking-wider {{ $plan->statusBadgeClass() }}">
                                            {{ $plan->statusLabel() }}
                                        </span>
                                    </div>
                                    @if (!$plan->isBalanced())
                                        <div>
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300"
                                                title="Monthly milestones do not sum to annual target">
                                                ⚠️ Unbalanced
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('bed2-plans.show', $plan) }}"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-block">
                                        View
                                    </a>

                                    <!-- PMO Edit (Authorized to adjust OU inputs directly anytime) -->
                                    @if ($isPmo)
                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold text-[11px] border border-indigo-200 transition inline-block">
                                            Edit
                                        </a>
                                        <!-- Encoder Edit (Allowed when draft or returned) -->
                                    @elseif ($plan->isEditable() && $plan->operating_unit_id === auth()->user()->operating_unit_id)
                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[11px] border border-emerald-200 transition inline-block">
                                            Edit
                                        </a>

                                        @if ($plan->isBalanced())
                                            <form method="POST" action="{{ route('bed2-plans.submit-head', $plan) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Submit this plan to Division Chief for review?');"
                                                    class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[11px] border border-amber-200 transition">
                                                    Submit
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('bed2-plans.edit', $plan) }}"
                                                class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[11px] border border-amber-300 transition"
                                                title="Monthly milestones must balance with annual target before submission">
                                                Balance to Submit
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Delete Plan (Draft or Returned) -->
                                    @if (in_array($plan->status, ['draft', 'returned']) && ($isPmo || $plan->operating_unit_id === auth()->user()->operating_unit_id))
                                        <form method="POST" action="{{ route('bed2-plans.destroy', $plan) }}" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this target plan row?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 font-bold text-[11px] border border-rose-200 transition inline-flex items-center gap-1"
                                                title="Delete this target plan row">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Delete</span>
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
                                        <p class="text-xs text-slate-400">No physical target plans matching the selected
                                            year and filters. Click "Encode New Target Plan" to start.</p>
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

    <!-- Return Modal for Revision Remarks -->
    <div id="returnModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Return Plans for Revision</h3>
                <button type="button" onclick="closeReturnModal()"
                    class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('bed2-plans.batch-return') }}" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="operating_unit_id" id="returnOuId" value="">

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Revision Remarks /
                        Instructions <span class="text-rose-500">*</span></label>
                    <textarea name="remarks" required rows="3"
                        placeholder="Explain the required revisions, adjustments in quarterly allocations, etc..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-medium"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeReturnModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold transition">
                        Return to Encoder
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReturnModal(ouId) {
            document.getElementById('returnOuId').value = ouId;
            document.getElementById('returnModal').classList.remove('hidden');
        }

        function closeReturnModal() {
            document.getElementById('returnModal').classList.add('hidden');
        }
    </script>

    @if ($isPmo)
    <!-- Rollover / Start-of-Year Initialization Modal (PMO Only) -->
    <div id="rolloverModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🔄</span>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Start-of-Year Target Initialization</h3>
                </div>
                <button type="button" onclick="closeRolloverModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('bed2-plans.rollover') }}" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">

                <p class="text-slate-600 leading-relaxed">
                    This will automatically populate all recurring indicators for the selected operating unit in <span class="font-extrabold text-emerald-800">FY {{ $year }}</span> as fresh draft rows ready for target encoding.
                </p>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Operating Unit <span class="text-rose-500">*</span></label>
                    <select name="operating_unit_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 font-semibold text-xs">
                        @foreach ($operatingUnits as $ou)
                            <option value="{{ $ou->id }}" {{ $selectedOuId == $ou->id ? 'selected' : '' }}>{{ $ou->name }} ({{ $ou->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Source Baseline:</span>
                        <span class="font-mono text-emerald-800 font-extrabold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            FY {{ $year - 1 }} / Bureau Catalog
                        </span>
                    </div>
                    <div class="flex items-center gap-2 pt-1 border-t border-slate-200/60">
                        <input type="checkbox" name="copy_targets" id="copy_targets" value="1"
                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="copy_targets" class="text-slate-700 font-medium">Copy prior annual targets as draft estimates (otherwise starts at 0.00)</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeRolloverModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <span>Initialize Target Plans</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    <script>
        function openRolloverModal() {
            document.getElementById('rolloverModal').classList.remove('hidden');
        }

        function closeRolloverModal() {
            document.getElementById('rolloverModal').classList.add('hidden');
        }
    </script>
@endsection
