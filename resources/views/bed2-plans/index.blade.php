<!--index.blade.php BED-2 Target Plans (Compact & Sleek Professional)-->
@extends('layouts.app')
@section('title', 'BED-2 Physical Target Plans')

@section('content')
    <div class="space-y-3.5">
        @php
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $isPmo = $user->can('bed2.manage_all_units') || $user->hasAnyRole(['superadmin', 'admin', 'evaluator']);
            $isEncoder = !$isPmo && ($user->hasRole('encoder') || $user->can('bed2.submit_head'));
            $isHead = !$isPmo && ($user->hasRole('ou-head') || $user->can('bed2.head_endorse'));
            $selectedOuId = request('operating_unit_id') ?: ($isPmo ? null : $user->operating_unit_id);
            $selectedOu = $selectedOuId ? $operatingUnits->firstWhere('id', $selectedOuId) : null;
            $currentStatus = request('status', '');
            $showOuColumn = ! $selectedOu;
            $tableColspan = $showOuColumn ? 10 : 9;
        @endphp

        <!-- 1. Compact Header & Action Controls -->
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-3">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">BED-2 Physical Target Plans</h2>
                    <span class="px-2 py-0.5 rounded-full text-[9.5px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide border border-emerald-200/60">
                        FY {{ $year }}
                    </span>
                    @if ($selectedOu)
                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-slate-100 text-slate-700 uppercase tracking-wide border border-slate-200 flex items-center gap-1">
                            <span>🏛️</span>
                            <span>{{ $selectedOu->name }} ({{ $selectedOu->code }})</span>
                        </span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Annual physical performance commitments, milestone allocations, and baseline locks across bureau operating units.
                </p>
            </div>

            <!-- Contextual Workflow Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap self-start lg:self-auto">
                <!-- OU Encoder Batch Submit -->
                @if ($isEncoder && ($metrics['draft'] > 0 || $metrics['returned'] > 0))
                    <form method="POST" action="{{ route('bed2-plans.batch-submit-head') }}" class="inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="operating_unit_id" value="{{ $user->operating_unit_id }}">
                        <button type="submit"
                            onclick="return confirm('Submit all {{ $metrics['draft'] + $metrics['returned'] }} draft targets to Division Chief for review?');"
                            class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Submit to Chief ({{ $metrics['draft'] + $metrics['returned'] }})</span>
                        </button>
                    </form>
                @endif

                <!-- Division Chief Batch Endorse -->
                @if ($isHead && $metrics['pending_head'] > 0)
                    <form method="POST" action="{{ route('bed2-plans.batch-head-approve') }}" class="inline">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="operating_unit_id" value="{{ $user->operating_unit_id }}">
                        <button type="submit"
                            onclick="return confirm('Endorse all {{ $metrics['pending_head'] }} target plans to PMO for final validation?');"
                            class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Endorse to PMO ({{ $metrics['pending_head'] }})</span>
                        </button>
                    </form>

                    <button type="button" onclick="openReturnModal('{{ $user->operating_unit_id }}')"
                        class="px-3 py-1.5 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-xs rounded-xl transition shadow-2xs">
                        ↩ Return to Encoder
                    </button>
                @endif

                <!-- PMO Approving & Governance Actions -->
                @if ($isPmo)
                    @if ($selectedOuId && ($metrics['pending_pmo'] > 0 || $metrics['pending_head'] > 0))
                        <form method="POST" action="{{ route('bed2-plans.batch-pmo-approve') }}" class="inline">
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="operating_unit_id" value="{{ $selectedOuId }}">
                            <button type="submit"
                                onclick="return confirm('Officially approve and lock annual baseline targets for {{ $selectedOu?->name }} (FY {{ $year }})?');"
                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Lock &amp; Approve</span>
                            </button>
                        </form>

                        <button type="button" onclick="openReturnModal('{{ $selectedOuId }}')"
                            class="px-3 py-1.5 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-xs rounded-xl transition shadow-2xs">
                            ↩ Return
                        </button>
                    @endif
                @endif

                <!-- Official Government DBM BED-2 Print & CSV Export Actions -->
                <div class="inline-flex rounded-xl shadow-2xs border border-slate-200 bg-white overflow-hidden text-xs">
                    <a href="{{ route('bed2-plans.export.print', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                        target="_blank"
                        class="px-3 py-1.5 hover:bg-slate-50 text-slate-700 font-bold transition flex items-center gap-1 border-r border-slate-100"
                        title="Print Official DBM BED Form No. 2">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Print</span>
                    </a>
                    <a href="{{ route('bed2-plans.export.csv', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                        class="px-2.5 py-1.5 hover:bg-slate-50 text-emerald-700 font-bold transition flex items-center gap-1"
                        title="Download CSV / Excel">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>CSV</span>
                    </a>
                </div>

                {{-- Rollover: PMO Only --}}
                @if ($isPmo)
                    <button type="button" onclick="openRolloverModal()"
                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition flex items-center gap-1"
                        title="Initialize indicators for FY {{ $year }} from previous year or active catalog">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Initialize</span>
                    </button>
                @endif

                <!-- Encode Target Plan Primary Button -->
                @can('bed2.create')
                    <a href="{{ route('bed2-plans.create', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                        class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs shadow-emerald-600/20 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Encode Target Plan</span>
                    </a>
                @else
                    @hasanyrole('superadmin|admin|evaluator|encoder')
                        <a href="{{ route('bed2-plans.create', array_filter(['year' => $year, 'operating_unit_id' => $selectedOuId])) }}"
                            class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs shadow-emerald-600/20 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Encode Target Plan</span>
                        </a>
                    @endhasanyrole
                @endcan
            </div>
        </div>

        @if (session('status'))
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. Sleek Compact KPI Toolbar Strip -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Year Selector -->
            <div class="inline-flex items-center p-0.5 rounded-xl bg-slate-100 border border-slate-200/80 shadow-2xs">
                <span class="px-2.5 text-[9.5px] font-black text-slate-400 uppercase tracking-wider">FY:</span>
                @foreach ($availableYears as $y)
                    <a href="{{ request()->fullUrlWithQuery(['year' => $y]) }}"
                        class="px-2.5 py-1 rounded-lg text-xs font-extrabold transition {{ $year === $y ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        {{ $y }}
                    </a>
                @endforeach
            </div>

            <!-- Compact Clickable KPI Cards -->
            <div class="flex items-center gap-2 flex-wrap flex-1">
                <!-- All / Total -->
                <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
                    class="px-3 py-1.5 rounded-xl border transition flex items-center gap-2 group {{ empty($currentStatus) ? 'bg-white border-emerald-500 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                    <span class="w-2 h-2 rounded-full bg-slate-400 group-hover:bg-slate-600"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total</span>
                    <span class="text-xs font-black text-slate-900 font-mono">{{ $metrics['total'] }}</span>
                </a>

                <!-- Approved & Locked -->
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
                    class="px-3 py-1.5 rounded-xl border transition flex items-center gap-2 group {{ $currentStatus === 'approved' ? 'bg-emerald-50/70 border-emerald-500 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-white border-slate-200/80 hover:border-emerald-200' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Approved</span>
                    <span class="text-xs font-black text-emerald-800 font-mono">{{ $metrics['approved'] }}</span>
                </a>

                <!-- Pending PMO Review -->
                <a href="{{ request()->fullUrlWithQuery(['status' => 'head_approved']) }}"
                    class="px-3 py-1.5 rounded-xl border transition flex items-center gap-2 group {{ $currentStatus === 'head_approved' ? 'bg-indigo-50/70 border-indigo-500 ring-2 ring-indigo-500/20 shadow-2xs' : 'bg-white border-slate-200/80 hover:border-indigo-200' }}">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-800">In Validation</span>
                    <span class="text-xs font-black text-indigo-800 font-mono">{{ $metrics['pending_pmo'] }}</span>
                </a>

                <!-- Pending Division Chief Review -->
                <a href="{{ request()->fullUrlWithQuery(['status' => 'submitted_to_head']) }}"
                    class="px-3 py-1.5 rounded-xl border transition flex items-center gap-2 group {{ $currentStatus === 'submitted_to_head' ? 'bg-amber-50/70 border-amber-500 ring-2 ring-amber-500/20 shadow-2xs' : 'bg-white border-slate-200/80 hover:border-amber-200' }}">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800">For Chief</span>
                    <span class="text-xs font-black text-amber-800 font-mono">{{ $metrics['pending_head'] }}</span>
                </a>

                <!-- Draft Plans -->
                <a href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}"
                    class="px-3 py-1.5 rounded-xl border transition flex items-center gap-2 group {{ $currentStatus === 'draft' ? 'bg-sky-50/70 border-sky-500 ring-2 ring-sky-500/20 shadow-2xs' : 'bg-white border-slate-200/80 hover:border-sky-200' }}">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Drafts</span>
                    <span class="text-xs font-black text-slate-800 font-mono">{{ $metrics['draft'] }}</span>
                </a>

                <!-- Needs Revision -->
                @if ($metrics['returned'] > 0)
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'returned']) }}"
                        class="px-3 py-1.5 rounded-xl border transition flex items-center gap-2 group {{ $currentStatus === 'returned' ? 'bg-rose-50/80 border-rose-500 ring-2 ring-rose-500/20 shadow-2xs' : 'bg-white border-rose-200 hover:border-rose-300' }}">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Needs Revision</span>
                        <span class="text-xs font-black text-rose-700 font-mono">{{ $metrics['returned'] }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- 3. Sleek Compact Filter Bar -->
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-2xs">
            <form method="GET" action="{{ route('bed2-plans.index') }}"
                class="grid grid-cols-1 md:grid-cols-12 gap-2 text-xs">
                <input type="hidden" name="year" value="{{ $year }}">
                @if ($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif

                <!-- Search Input -->
                <div class="md:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search indicator, task, or code..."
                        class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium text-xs">
                </div>

                <!-- Operating Unit Filter (if PMO) -->
                @if ($isPmo)
                    <div class="md:col-span-3">
                        <select name="operating_unit_id" onchange="this.form.submit()"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium text-xs text-slate-700">
                            <option value="">🏢 All Operating Units</option>
                            @foreach ($operatingUnits as $ou)
                                <option value="{{ $ou->id }}" {{ request('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Program / PAP Filter -->
                <div class="{{ $isPmo ? 'md:col-span-2' : 'md:col-span-5' }}">
                    <select name="pap_id"
                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition font-medium text-xs text-slate-700">
                        <option value="">All Programs / PAPs</option>
                        @foreach ($paps as $p)
                            <option value="{{ $p->id }}" {{ request('pap_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->code }} - {{ Str::limit($p->name, 35) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="md:col-span-2 flex gap-1.5">
                    <button type="submit"
                        class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-2xs transition flex items-center justify-center gap-1 text-xs">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('bed2-plans.index', ['year' => $year]) }}"
                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg border border-slate-200 font-bold text-center transition text-xs"
                        title="Clear filters">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Compact & Sleek BED-2 Plans Matrix Table -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto sheet-scroll-container">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[10px] select-none">
                        <tr class="border-b border-slate-200">
                            @if ($showOuColumn)
                                <th rowspan="2" class="py-2 px-3 border-r border-slate-200 min-w-[90px] w-[90px] align-middle">Operating Unit</th>
                            @endif
                            <th rowspan="2" class="py-2 px-3 border-r border-slate-200 min-w-[340px] align-middle">PAP &amp; Performance Indicator</th>
                            <th rowspan="2" class="py-2 px-2 text-center border-r border-slate-200 min-w-[70px] w-[70px] align-middle">Unit</th>
                            <th rowspan="2" class="py-2 px-3 text-right border-r border-slate-300 min-w-[105px] w-[105px] bg-amber-50/40 text-amber-950 align-middle">Annual Target</th>
                            <th colspan="4" class="py-1 px-2 text-center border-r border-slate-300 bg-slate-100/70 text-slate-700 text-[9.5px] tracking-widest font-black">
                                Quarterly Breakdown
                            </th>
                            <th rowspan="2" class="py-2 px-2.5 text-center border-r border-slate-200 min-w-[115px] w-[115px] align-middle">Review State</th>
                            <th rowspan="2" class="py-2 px-3 text-right min-w-[160px] w-[160px] align-middle">Actions</th>
                        </tr>
                        <tr class="bg-slate-50 text-[9.5px] font-black border-b border-slate-200">
                            <th class="py-1 px-2 text-center border-r border-slate-200 bg-emerald-50/60 text-emerald-900 min-w-[65px] w-[65px]">Q1</th>
                            <th class="py-1 px-2 text-center border-r border-slate-200 bg-sky-50/60 text-sky-900 min-w-[65px] w-[65px]">Q2</th>
                            <th class="py-1 px-2 text-center border-r border-slate-200 bg-amber-50/60 text-amber-900 min-w-[65px] w-[65px]">Q3</th>
                            <th class="py-1 px-2 text-center border-r border-slate-300 bg-purple-50/60 text-purple-900 min-w-[65px] w-[65px]">Q4</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @php
                            $currentCategory = null;
                            $currentActivity = null;
                            $currentSubProgram = null;
                        @endphp

                        @forelse ($plans as $plan)
                            @php
                                $pap = $plan->pap;
                                $categoryTitle = !empty(trim($pap?->category ?? ''))
                                    ? strtoupper(trim($pap->category))
                                    : (!empty(trim($pap?->name ?? '')) ? strtoupper(trim($pap->name)) : 'OPERATIONS');

                                $activity = trim($plan->activity ?? $plan->performanceIndicator?->activity ?? '');
                                $subProgram = trim($plan->sub_program ?? $plan->performanceIndicator?->sub_program ?? '');
                                $subActivity = trim($plan->sub_activity ?? $plan->performanceIndicator?->sub_activity ?? '');
                                $indicatorFullName = $plan->performanceIndicator?->name ?? 'Indicator';
                                $displayName = $indicatorFullName;

                                if (empty($activity) && empty($subProgram) && str_contains($indicatorFullName, ':')) {
                                    [$prefix, $rest] = explode(':', $indicatorFullName, 2);
                                    $activity = trim($prefix);
                                    $displayName = trim($rest);
                                }
                            @endphp

                            {{-- 1. Program / PAP Hierarchy Row (Individual cells with column borders throughout) --}}
                            @if ($currentCategory !== $categoryTitle)
                                @php
                                    $currentCategory = $categoryTitle;
                                    $currentActivity = null;
                                    $currentSubProgram = null;
                                @endphp
                                <tr class="bg-slate-100/90 border-t-2 border-b border-slate-300 font-bold select-none">
                                    @if ($showOuColumn)
                                        <!-- Col 1: Operating Unit (Empty cell with border) -->
                                        <td class="py-1 px-3 border-r border-slate-200 bg-slate-100/70 border-l-[3px] border-l-purple-600"></td>
                                    @endif

                                    <!-- Col 2: Program Title -->
                                    <td class="py-1.5 px-3 border-r border-slate-200 {{ $showOuColumn ? '' : 'border-l-[3px] border-l-purple-600' }} align-middle">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 shrink-0"></span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-purple-100 text-purple-800 border border-purple-200/80 uppercase tracking-wider">
                                                PROGRAM
                                            </span>
                                            <span class="font-black text-slate-900 text-xs tracking-wide uppercase">
                                                {{ $categoryTitle }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Col 3: Unit (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-slate-100/60"></td>
                                    <!-- Col 4: Annual Target (Empty cell with border) -->
                                    <td class="py-1 px-3 border-r border-slate-300 bg-amber-50/20"></td>
                                    <!-- Col 5: Q1 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-emerald-50/25"></td>
                                    <!-- Col 6: Q2 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-sky-50/25"></td>
                                    <!-- Col 7: Q3 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-amber-50/25"></td>
                                    <!-- Col 8: Q4 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-300 bg-purple-50/25"></td>
                                    <!-- Col 9: Review State (Empty cell with border) -->
                                    <td class="py-1 px-2.5 border-r border-slate-200 bg-slate-100/60"></td>
                                    <!-- Col 10: Actions (Empty cell) -->
                                    <td class="py-1 px-3 bg-slate-100/60"></td>
                                </tr>
                            @endif

                            {{-- 2. Activity Hierarchy Row (Individual cells with column borders throughout) --}}
                            @if (!empty($activity) && $currentActivity !== $activity)
                                @php
                                    $currentActivity = $activity;
                                    $currentSubProgram = null;
                                @endphp
                                <tr class="bg-indigo-50/30 border-b border-indigo-100/80 select-none">
                                    @if ($showOuColumn)
                                        <!-- Col 1: Operating Unit (Empty cell with border) -->
                                        <td class="py-1 px-3 border-r border-slate-200 bg-indigo-50/30 border-l-[3px] border-l-indigo-500"></td>
                                    @endif

                                    <!-- Col 2: Activity Title -->
                                    <td class="py-1.5 px-3 border-r border-slate-200 {{ $showOuColumn ? '' : 'border-l-[3px] border-l-indigo-500' }} align-middle">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200/80 uppercase tracking-wider">
                                                ACTIVITY
                                            </span>
                                            <span class="font-extrabold text-slate-900 text-[11px] tracking-wide">
                                                {{ $activity }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Col 3: Unit (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-indigo-50/20"></td>
                                    <!-- Col 4: Annual Target (Empty cell with border) -->
                                    <td class="py-1 px-3 border-r border-slate-300 bg-amber-50/15"></td>
                                    <!-- Col 5: Q1 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-emerald-50/20"></td>
                                    <!-- Col 6: Q2 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-sky-50/20"></td>
                                    <!-- Col 7: Q3 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-amber-50/20"></td>
                                    <!-- Col 8: Q4 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-300 bg-purple-50/20"></td>
                                    <!-- Col 9: Review State (Empty cell with border) -->
                                    <td class="py-1 px-2.5 border-r border-slate-200 bg-indigo-50/20"></td>
                                    <!-- Col 10: Actions (Empty cell) -->
                                    <td class="py-1 px-3 bg-indigo-50/20"></td>
                                </tr>
                            @endif

                            {{-- 3. Sub-Activity Hierarchy Row (Individual cells with column borders throughout) --}}
                            @if (!empty($subProgram) && $currentSubProgram !== $subProgram)
                                @php
                                    $currentSubProgram = $subProgram;
                                @endphp
                                <tr class="bg-amber-50/25 border-b border-amber-100/70 select-none">
                                    @if ($showOuColumn)
                                        <!-- Col 1: Operating Unit (Empty cell with border) -->
                                        <td class="py-1 px-3 border-r border-slate-200 bg-amber-50/25 border-l-[3px] border-l-amber-500"></td>
                                    @endif

                                    <!-- Col 2: Sub-activity Title -->
                                    <td class="py-1 px-3 border-r border-slate-200 {{ $showOuColumn ? '' : 'border-l-[3px] border-l-amber-500' }} align-middle">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-100 text-amber-800 border border-amber-200/80 uppercase tracking-wider">
                                                SUB-ACTIVITY
                                            </span>
                                            <span class="font-bold text-slate-800 text-[11px] tracking-wide">
                                                {{ $subProgram }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Col 3: Unit (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-amber-50/15"></td>
                                    <!-- Col 4: Annual Target (Empty cell with border) -->
                                    <td class="py-1 px-3 border-r border-slate-300 bg-amber-50/10"></td>
                                    <!-- Col 5: Q1 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-emerald-50/15"></td>
                                    <!-- Col 6: Q2 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-sky-50/15"></td>
                                    <!-- Col 7: Q3 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-200 bg-amber-50/15"></td>
                                    <!-- Col 8: Q4 (Empty cell with border) -->
                                    <td class="py-1 px-2 border-r border-slate-300 bg-purple-50/15"></td>
                                    <!-- Col 9: Review State (Empty cell with border) -->
                                    <td class="py-1 px-2.5 border-r border-slate-200 bg-amber-50/15"></td>
                                    <!-- Col 10: Actions (Empty cell) -->
                                    <td class="py-1 px-3 bg-amber-50/15"></td>
                                </tr>
                            @endif

                            {{-- 4. Target Plan Data Row (Compact, Sleek, Professional) --}}
                            <tr class="hover:bg-slate-50/75 transition group border-b border-slate-100">
                                @if ($showOuColumn)
                                    <!-- Col 1: Operating Unit (Only when Bureau-Wide) -->
                                    <td class="py-2.5 px-3 align-middle whitespace-nowrap border-r border-slate-200">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-black bg-emerald-50 text-emerald-800 border border-emerald-200/60 shadow-2xs">
                                            {{ $plan->operatingUnit?->code }}
                                        </span>
                                    </td>
                                @endif

                                <!-- Col 2: PAP & Performance Indicator -->
                                <td class="py-2.5 px-3 max-w-sm align-middle border-r border-slate-200">
                                    <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                        <span class="px-1.5 py-0.2 rounded text-[8.5px] font-black uppercase tracking-wider {{ $plan->performanceIndicator?->type === 'outcome' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-sky-100 text-sky-800 border border-sky-200' }}">
                                            {{ $plan->performanceIndicator?->type ?? 'Output' }}
                                        </span>
                                        @if ($plan->performanceIndicator?->code)
                                            <span class="font-mono text-[9.5px] text-slate-400 font-semibold">
                                                {{ $plan->performanceIndicator?->code }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($subActivity)
                                        <p class="font-bold text-slate-900 text-xs leading-snug">
                                            {{ $subActivity }}
                                        </p>
                                        <p class="text-[11px] text-slate-600 leading-snug mt-0.5">
                                            <span class="font-bold text-slate-400 text-[9.5px] uppercase">Indicator:</span>
                                            <span class="font-medium text-slate-700">{{ $displayName }}</span>
                                        </p>
                                    @else
                                        <p class="font-bold text-slate-900 text-xs leading-snug">
                                            {{ $displayName }}
                                        </p>
                                    @endif

                                    @if ($plan->remarks)
                                        <div class="text-[10px] text-rose-700 bg-rose-50/80 px-2 py-1 rounded-lg border border-rose-200/70 mt-1 flex items-start gap-1">
                                            <span class="shrink-0">💬</span>
                                            <span class="italic font-medium">{{ $plan->remarks }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Col 3: Unit -->
                                <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-slate-200">
                                    <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-700 border border-slate-200/70 inline-block">
                                        {{ $plan->performanceIndicator?->unit_of_measure ?? $plan->unit_of_measure ?? 'Units' }}
                                    </span>
                                </td>

                                <!-- Col 4: Annual Target -->
                                <td class="py-2.5 px-3 text-right align-middle whitespace-nowrap border-r border-slate-200 font-mono font-black text-slate-900 bg-amber-50/30 text-xs">
                                    {{ number_format($plan->annual_target, 2) }}
                                </td>

                                <!-- Col 5: Q1 Target -->
                                <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-slate-200 font-mono font-semibold text-slate-800 bg-emerald-50/15 text-xs">
                                    {{ number_format($plan->q1_target, 0) }}
                                </td>

                                <!-- Col 6: Q2 Target -->
                                <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-slate-200 font-mono font-semibold text-slate-800 bg-sky-50/15 text-xs">
                                    {{ number_format($plan->q2_target, 0) }}
                                </td>

                                <!-- Col 7: Q3 Target -->
                                <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-slate-200 font-mono font-semibold text-slate-800 bg-amber-50/15 text-xs">
                                    {{ number_format($plan->q3_target, 0) }}
                                </td>

                                <!-- Col 8: Q4 Target -->
                                <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-slate-300 font-mono font-semibold text-slate-800 bg-purple-50/15 text-xs">
                                    {{ number_format($plan->q4_target, 0) }}
                                </td>

                                <!-- Col 9: Review State -->
                                <td class="py-2.5 px-2.5 text-center align-middle whitespace-nowrap border-r border-slate-200 space-y-0.5">
                                    <div>
                                        <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-extrabold border uppercase tracking-wider {{ $plan->statusBadgeClass() }}">
                                            {{ $plan->statusLabel() }}
                                        </span>
                                    </div>
                                    @if (!$plan->isBalanced())
                                        <div>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300"
                                                title="Monthly milestones do not sum to annual target">
                                                ⚠️ Unbalanced
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Col 10: Actions -->
                                <td class="py-2.5 px-3 text-right align-middle space-x-1 whitespace-nowrap">
                                    <a href="{{ route('bed2-plans.show', $plan) }}"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10.5px] transition inline-block border border-slate-200/60 shadow-2xs">
                                        View
                                    </a>

                                    <!-- PMO Edit -->
                                    @if ($isPmo)
                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-bold text-[10.5px] border border-indigo-200 transition inline-block shadow-2xs">
                                            Edit
                                        </a>
                                    <!-- Encoder Edit -->
                                    @elseif ($plan->isEditable() && $plan->operating_unit_id === auth()->user()->operating_unit_id)
                                        <a href="{{ route('bed2-plans.edit', $plan) }}"
                                            class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[10.5px] border border-emerald-200 transition inline-block shadow-2xs">
                                            Edit
                                        </a>

                                        @if ($plan->isBalanced())
                                            <form method="POST" action="{{ route('bed2-plans.submit-head', $plan) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Submit this plan to Division Chief for review?');"
                                                    class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[10.5px] border border-amber-200 transition shadow-2xs">
                                                    Submit
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('bed2-plans.edit', $plan) }}"
                                                class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-[10.5px] border border-amber-300 transition shadow-2xs"
                                                title="Monthly milestones must balance with annual target before submission">
                                                Balance
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Delete Plan -->
                                    @if (in_array($plan->status, ['draft', 'returned']) && ($isPmo || $plan->operating_unit_id === auth()->user()->operating_unit_id))
                                        <form method="POST" action="{{ route('bed2-plans.destroy', $plan) }}" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this target plan row?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 font-bold text-[10.5px] border border-rose-200 transition inline-flex items-center gap-0.5 shadow-2xs"
                                                title="Delete this target plan row">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $tableColspan }}" class="p-8 text-center text-slate-400">
                                    <div class="max-w-xs mx-auto space-y-1.5">
                                        <div class="text-2xl">🎯</div>
                                        <p class="font-extrabold text-slate-700 text-xs">No BED-2 Target Plans Found</p>
                                        <p class="text-[11px] text-slate-400">
                                            No physical target plans match the selected year and filters. Click "Encode Target Plan" to start.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Compact Pagination Bar -->
            <div class="px-3.5 py-2.5 border-t border-slate-100 bg-slate-50/40 text-xs">
                {{ $plans->links() }}
            </div>
        </div>
    </div>

    <!-- Return Modal for Revision Remarks -->
    <div id="returnModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-slate-100 space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Return Plans for Revision</h3>
                <button type="button" onclick="closeReturnModal()"
                    class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('bed2-plans.batch-return') }}" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="operating_unit_id" id="returnOuId" value="">

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[11px]">Revision Remarks / Instructions <span class="text-rose-500">*</span></label>
                    <textarea name="remarks" required rows="3"
                        placeholder="Explain the required revisions, adjustments in quarterly allocations, etc..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-medium text-xs"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeReturnModal()"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition text-xs">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold transition rounded-xl text-xs">
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
        <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-slate-100 space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔄</span>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Start-of-Year Target Initialization</h3>
                </div>
                <button type="button" onclick="closeRolloverModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('bed2-plans.rollover') }}" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">

                <p class="text-slate-600 leading-relaxed text-xs">
                    This will automatically populate all recurring indicators for the selected operating unit in <span class="font-extrabold text-emerald-800">FY {{ $year }}</span> as fresh draft rows ready for target encoding.
                </p>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[11px]">Operating Unit <span class="text-rose-500">*</span></label>
                    <select name="operating_unit_id" required class="w-full px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50/50 font-semibold text-xs">
                        @foreach ($operatingUnits as $ou)
                            <option value="{{ $ou->id }}" {{ $selectedOuId == $ou->id ? 'selected' : '' }}>{{ $ou->name }} ({{ $ou->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Source Baseline:</span>
                        <span class="font-mono text-emerald-800 font-extrabold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[11px]">
                            FY {{ $year - 1 }} / Bureau Catalog
                        </span>
                    </div>
                    <div class="flex items-center gap-2 pt-1 border-t border-slate-200/60">
                        <input type="checkbox" name="copy_targets" id="copy_targets" value="1"
                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="copy_targets" class="text-slate-700 font-medium text-[11px]">Copy prior annual targets as draft estimates (otherwise starts at 0.00)</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeRolloverModal()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition text-xs">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold transition flex items-center gap-1.5 shadow-xs shadow-emerald-600/20 text-xs rounded-xl">
                        <span>Initialize Targets</span>
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
