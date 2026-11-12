<!--index.blade.php Reporting Periods-->
@extends('layouts.app')
@section('title', 'Reporting Cycle Rules & Period Governance')

@section('content')
    <div class="space-y-6">

        <!-- Top Bar: Header & Actions -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition">Dashboard</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-400">Governance</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800">Reporting Cycle Rules</span>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Reporting Cycle Rules & Window Governance</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Configure monthly physical accomplishment submission schedules, automated lock dates, and active cycle
                    availability.
                </p>
            </div>

            <button type="button" onclick="openCreateModal()"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Create Reporting Period</span>
            </button>
        </div>

        <!-- Active Reporting Cycle Spotlight Banner -->
        @if ($activePeriod)
            @php
                $now = \Illuminate\Support\Carbon::now();
                $start = \Illuminate\Support\Carbon::parse($activePeriod->window_start);
                $end = \Illuminate\Support\Carbon::parse($activePeriod->window_end);
                $isOpenNow = $now->between($start, $end);
            @endphp
            <div
                class="p-5 bg-gradient-to-r from-emerald-900 via-dagreen-950 to-emerald-950 rounded-2xl text-white shadow-md border border-emerald-800/60 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div
                    class="absolute -right-6 -bottom-6 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none">
                </div>

                <div class="space-y-2 relative z-10">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span
                            class="px-2.5 py-0.5 rounded-full bg-emerald-500/30 border border-emerald-400/40 text-emerald-300 text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            Official Active Cycle
                        </span>
                        <span class="text-slate-400 text-xs">&bull;</span>
                        <span class="text-xs text-slate-300 font-semibold">FY {{ $activePeriod->year }} &bull; Month
                            {{ $activePeriod->month }} ({{ $start->format('F') }})</span>
                    </div>
                    <h3 class="text-xl font-black text-white tracking-tight">
                        {{ $activePeriod->name }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-emerald-200/90 font-medium pt-0.5">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Window: <strong>{{ $start->format('M d, Y g:i A') }}</strong> &rarr;
                                <strong>{{ $end->format('M d, Y g:i A') }}</strong></span>
                        </div>
                        <span class="text-emerald-500">&bull;</span>
                        <div>
                            @if ($now->lt($start))
                                <span class="text-sky-300 font-bold">Opens in
                                    {{ $now->diffForHumans($start, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                            @elseif ($isOpenNow)
                                <span class="text-amber-300 font-bold">Closes in
                                    {{ $now->diffForHumans($end, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                            @else
                                <span class="text-rose-300 font-bold">Window passed deadline (Closed on
                                    {{ $end->format('M d') }})</span>
                            @endif
                        </div>
                    </div>
                    @if ($activePeriod->remarks)
                        <p class="text-[11px] text-slate-300/80 italic pt-1">
                            &ldquo;{{ $activePeriod->remarks }}&rdquo;
                        </p>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0 relative z-10">
                    <button type="button" onclick="openEditModal({{ json_encode($activePeriod) }})"
                        class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition border border-white/10 flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Edit Dates</span>
                    </button>

                    <form method="POST" action="{{ route('admin.reporting-periods.toggle-active', $activePeriod) }}"
                        class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            onclick="return confirm('Deactivate this reporting cycle? Monthly physical accomplishment submissions will be locked on the public portal and intranet.');"
                            class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Deactivate & Lock</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- ALERT BANNER: NO ACTIVE CYCLE SET -->
            <div class="p-5 bg-rose-50 border-2 border-rose-300 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm"
                role="alert">
                <div class="flex items-center gap-3.5">
                    <span
                        class="w-12 h-12 rounded-xl bg-rose-600 text-white font-black text-xl flex items-center justify-center shadow-sm shrink-0">
                        ⚠️
                    </span>
                    <div>
                        <h4 class="text-sm font-black text-rose-950 flex items-center gap-2">
                            <span>No Active Reporting Cycle Configured</span>
                            <span
                                class="px-2 py-0.5 rounded bg-rose-200 text-rose-900 text-[10px] font-extrabold uppercase tracking-wider">Submissions
                                Locked</span>
                        </h4>
                        <p class="text-xs text-rose-800 mt-1">
                            The public portal and division encoders currently see an <strong>official advisory
                                error</strong>. Accomplishment submission is locked until you create or activate a reporting
                            cycle below.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="openCreateModal()"
                    class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition shrink-0 self-start sm:self-auto">
                    + Configure & Activate Cycle Now
                </button>
            </div>
        @endif

        <!-- Mini KPI Stat Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Cycles</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalCount }}</p>
                <span class="text-[10px] text-slate-400 mt-0.5">Recorded periods</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">Open Window</span>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ $openCount }}</p>
                <span class="text-[10px] text-emerald-600 font-semibold mt-0.5">Submissions accepting</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-sky-600">Upcoming Cycles</span>
                <p class="text-2xl font-black text-sky-700 mt-1">{{ $upcomingCount }}</p>
                <span class="text-[10px] text-sky-600 font-semibold mt-0.5">Scheduled ahead</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Archived / Closed</span>
                <p class="text-2xl font-black text-slate-700 mt-1">{{ $closedCount }}</p>
                <span class="text-[10px] text-slate-400 mt-0.5">Locked & audited</span>
            </div>
        </div>

        <!-- Filters & Table Section -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

            <!-- Table Toolbar -->
            <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Configured Reporting Periods</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Physical accomplishment reporting windows per BED-2 guidelines.
                    </p>
                </div>

                <!-- Filters Form -->
                <form method="GET" action="{{ route('admin.reporting-periods.index') }}"
                    class="flex flex-wrap items-center gap-3 text-xs">
                    <select name="status" onchange="this.form.submit()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/50 font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>

                    <select name="year" onchange="this.form.submit()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/50 font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">All Fiscal Years</option>
                        @foreach ($availableYears as $yr)
                            <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>FY
                                {{ $yr }}</option>
                        @endforeach
                    </select>

                    @if (request()->hasAny(['status', 'year']))
                        <a href="{{ route('admin.reporting-periods.index') }}"
                            class="text-xs text-slate-500 hover:text-slate-800 underline font-semibold px-2">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Periods Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead
                        class="bg-slate-50/70 border-b border-slate-100 text-[10px] uppercase font-extrabold tracking-wider text-slate-400">
                        <tr>
                            <th class="p-4">Fiscal Period & Name</th>
                            <th class="p-4">Submission Window (PST)</th>
                            <th class="p-4">Workflow Status</th>
                            <th class="p-4">Official Cycle</th>
                            <th class="p-4">Linked Reports</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($periods as $p)
                            @php
                                $start = \Illuminate\Support\Carbon::parse($p->window_start);
                                $end = \Illuminate\Support\Carbon::parse($p->window_end);
                                $isRowActive = $p->is_active;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition {{ $isRowActive ? 'bg-emerald-50/40' : '' }}">
                                <td class="p-4">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-9 h-9 rounded-xl {{ $isRowActive ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }} flex flex-col items-center justify-center font-black text-xs shrink-0 shadow-sm">
                                            <span>{{ str_pad($p->month, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span>{{ $p->name }}</span>
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                FY {{ $p->year }} &bull; Month {{ $p->month }}
                                                ({{ date('F', mktime(0, 0, 0, $p->month, 10)) }})
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4">
                                    <div class="font-semibold text-slate-800">
                                        <span>{{ $start->format('M d, Y') }}</span>
                                        <span class="text-slate-400 font-normal">({{ $start->format('g:i A') }})</span>
                                        <span class="text-slate-400 mx-1">&rarr;</span>
                                        <span>{{ $end->format('M d, Y') }}</span>
                                        <span class="text-slate-400 font-normal">({{ $end->format('g:i A') }})</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Duration: {{ $start->diffInDays($end) + 1 }} calendar days
                                    </p>
                                </td>

                                <td class="p-4">
                                    @if ($p->status === 'open')
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            OPEN
                                        </span>
                                    @elseif ($p->status === 'upcoming')
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-sky-100 text-sky-800 inline-flex items-center gap-1">
                                            UPCOMING
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-slate-100 text-slate-600 inline-flex items-center gap-1">
                                            CLOSED
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4">
                                    @if ($isRowActive)
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-500 text-white shadow-sm inline-flex items-center gap-1">
                                            ✓ ACTIVE OFFICIAL
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-xl text-[10px] font-bold bg-slate-100 text-slate-500">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4 font-semibold text-slate-800">
                                    <span class="px-2 py-0.5 bg-slate-100 rounded-md text-[11px]">
                                        {{ $p->monthly_reports_count ?? 0 }} Submitted
                                    </span>
                                </td>

                                <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                    <!-- One-click Activate / Deactivate Toggle -->
                                    <form method="POST"
                                        action="{{ route('admin.reporting-periods.toggle-active', $p) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if ($isRowActive)
                                            <button type="submit"
                                                onclick="return confirm('Deactivate this reporting cycle? Submissions will be locked.');"
                                                class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200 font-extrabold text-[11px] transition">
                                                Lock / Deactivate
                                            </button>
                                        @else
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 font-extrabold text-[11px] transition">
                                                Make Active &rarr;
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Edit Modal Trigger -->
                                    <button type="button" onclick="openEditModal({{ json_encode($p) }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                        Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form method="POST" action="{{ route('admin.reporting-periods.destroy', $p) }}"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Permanently delete this reporting cycle?');"
                                            class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-[11px] transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">No reporting periods found in the
                                        system.</p>
                                    <p class="text-xs text-slate-400 mt-1">Click the button below to initialize the
                                        official reporting cycle.</p>
                                    <button type="button" onclick="openCreateModal()"
                                        class="mt-4 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition inline-flex items-center gap-1.5">
                                        + Create First Reporting Cycle
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $periods->links() }}
            </div>
        </div>

    </div>

    <!-- MODAL: CREATE REPORTING PERIOD -->
    <div id="createModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="p-5 bg-dagreen-950 text-white flex items-center justify-between border-b-2 border-emerald-600">
                <div>
                    <h3 class="text-base font-black">Create New Reporting Period</h3>
                    <p class="text-xs text-slate-300 mt-0.5">Configure submission calendar and lock deadlines.</p>
                </div>
                <button type="button" onclick="closeCreateModal()"
                    class="text-slate-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.reporting-periods.store') }}" class="p-6 space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Fiscal Year</label>
                        <input type="number" name="year" id="create_year" value="{{ date('Y') }}" required
                            min="2020" max="2050"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Calendar Month</label>
                        <select name="month" id="create_month" onchange="autoFillCreateName()" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }} (Month {{ $m }})
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Period Cycle Name</label>
                    <input type="text" name="name" id="create_name" required
                        value="{{ date('F Y') }} Monthly Physical Accomplishment"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Window Start
                            (PST)</label>
                        <input type="datetime-local" name="window_start" id="create_start" required
                            value="{{ date('Y-m-21\T00:00') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Window End (Lock
                            Deadline)</label>
                        <input type="datetime-local" name="window_end" id="create_end" required
                            value="{{ date('Y-m-26\T23:59') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Workflow Status</label>
                    <select name="status" id="create_status" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="open">Open (Submissions Allowed)</option>
                        <option value="upcoming">Upcoming (Scheduled Ahead)</option>
                        <option value="closed">Closed (Submissions Locked)</option>
                    </select>
                </div>

                <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-start gap-2.5">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked
                        class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <label for="create_is_active" class="cursor-pointer font-bold text-emerald-950">
                        Set as the official ACTIVE submission cycle now
                        <span class="block text-[11px] font-normal text-emerald-800">
                            This immediately displays this period on the homepage portal advisory bulletin and activates
                            physical submission.
                        </span>
                    </label>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Remarks / Advisory
                        Notes</label>
                    <textarea name="remarks" id="create_remarks" rows="2"
                        placeholder="e.g. Regular BED-2 submission window for division actuals and MOVs."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-normal focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow transition">
                        Save & Activate Period
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT REPORTING PERIOD -->
    <div id="editModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="p-5 bg-dagreen-950 text-white flex items-center justify-between border-b-2 border-emerald-600">
                <div>
                    <h3 class="text-base font-black">Edit Reporting Period</h3>
                    <p class="text-xs text-slate-300 mt-0.5">Modify calendar window, status, and lock settings.</p>
                </div>
                <button type="button" onclick="closeEditModal()"
                    class="text-slate-400 hover:text-white text-xl font-bold leading-none">&times;</button>
            </div>

            <form id="editForm" method="POST" action="" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Fiscal Year</label>
                        <input type="number" name="year" id="edit_year" required min="2020" max="2050"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Calendar Month</label>
                        <select name="month" id="edit_month" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }} (Month {{ $m }})
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Period Cycle Name</label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Window Start
                            (PST)</label>
                        <input type="datetime-local" name="window_start" id="edit_start" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Window End (Lock
                            Deadline)</label>
                        <input type="datetime-local" name="window_end" id="edit_end" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Workflow Status</label>
                    <select name="status" id="edit_status" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="open">Open (Submissions Allowed)</option>
                        <option value="upcoming">Upcoming (Scheduled Ahead)</option>
                        <option value="closed">Closed (Submissions Locked)</option>
                    </select>
                </div>

                <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-start gap-2.5">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                        class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <label for="edit_is_active" class="cursor-pointer font-bold text-emerald-950">
                        Set as the official ACTIVE submission cycle
                        <span class="block text-[11px] font-normal text-emerald-800">
                            Check to make this period active on the bureau portal.
                        </span>
                    </label>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold uppercase tracking-wider mb-1">Remarks / Advisory
                        Notes</label>
                    <textarea name="remarks" id="edit_remarks" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-normal focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow transition">
                        Update Reporting Cycle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const monthNames = [
            "", "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function autoFillCreateName() {
            const m = document.getElementById('create_month').value;
            const y = document.getElementById('create_year').value;
            if (monthNames[m]) {
                document.getElementById('create_name').value = monthNames[m] + ' ' + y + ' Monthly Physical Accomplishment';

                // Auto update start and end dates with padding
                const paddedM = String(m).padStart(2, '0');
                document.getElementById('create_start').value = `${y}-${paddedM}-21T00:00`;
                document.getElementById('create_end').value = `${y}-${paddedM}-26T23:59`;
            }
        }

        function formatForDatetimeLocal(isoStr) {
            if (!isoStr) return '';
            const d = new Date(isoStr);
            if (isNaN(d)) return isoStr.substring(0, 16);
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const hh = String(d.getHours()).padStart(2, '0');
            const min = String(d.getMinutes()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
        }

        function openEditModal(period) {
            document.getElementById('editForm').action = '/admin/reporting-periods/' + period.id;
            document.getElementById('edit_year').value = period.year;
            document.getElementById('edit_month').value = period.month;
            document.getElementById('edit_name').value = period.name;
            document.getElementById('edit_start').value = formatForDatetimeLocal(period.window_start);
            document.getElementById('edit_end').value = formatForDatetimeLocal(period.window_end);
            document.getElementById('edit_status').value = period.status;
            document.getElementById('edit_is_active').checked = !!period.is_active;
            document.getElementById('edit_remarks').value = period.remarks || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
            }
        });
    </script>
@endsection
