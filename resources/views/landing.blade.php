<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrated Performance Monitoring System (BPI-IPMS) &bull; Bureau of Plant Industry</title>
    <link rel="icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <link rel="shortcut icon" type="image/png"
        href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dagreen: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        govblue: {
                            800: '#1e3a8a',
                            900: '#0f2454',
                            950: '#081430',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'Segoe UI', 'sans-serif'],
                        serif: ['Georgia', 'Cambria', 'serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-slate-100 text-slate-800 flex flex-col min-h-screen antialiased selection:bg-dagreen-800 selection:text-white">
    @php
        $currentReportingPeriod = $currentReportingPeriod ?? null;
        if ($currentReportingPeriod === null) {
            try {
                $currentReportingPeriod = \App\Models\ReportingPeriod::where('is_active', true)
                    ->orWhere('status', 'open')
                    ->orderByDesc('is_active')
                    ->orderByDesc('year')
                    ->orderByDesc('month')
                    ->first();
            } catch (\Throwable $e) {
                $currentReportingPeriod = null;
            }
        }
        $operatingUnits = $operatingUnits ?? null;
        if ($operatingUnits === null) {
            try {
                $operatingUnits = \App\Models\OperatingUnit::where('is_active', true)->orderBy('name')->get();
                if ($operatingUnits->isEmpty()) {
                    $operatingUnits = \App\Models\OperatingUnit::orderBy('name')->get();
                }
            } catch (\Throwable $e) {
                $operatingUnits = collect();
            }
        }
        $operatingUnitsCount = $operatingUnitsCount ?? $operatingUnits->count();
        $now = \Illuminate\Support\Carbon::now();
        $windowStart = $currentReportingPeriod
            ? \Illuminate\Support\Carbon::parse($currentReportingPeriod->window_start)
            : null;
        $windowEnd = $currentReportingPeriod
            ? \Illuminate\Support\Carbon::parse($currentReportingPeriod->window_end)
            : null;
    @endphp
    <!-- 1. OFFICIAL TOP BAR (FULL WIDTH) -->
    <div class="bg-[#0b1f3a] text-slate-300 text-[11px] px-6 lg:px-12 xl:px-16 py-2 border-b-2 border-amber-500 w-full">
        <div class="w-full flex flex-col md:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2.5">

                <span class="font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png" alt="DA-BPI Logo"
                        class="w-5 h-5 object-contain shrink-0">
                    <span>Bureau of Plant Industry</span>
                </span>
                <span class="text-slate-600">&bull;</span>
                <span class="text-emerald-400 font-bold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Integrated Performance Monitoring System
                </span>
            </div>
            <div class="flex items-center gap-4 text-slate-300">
                <span class="hidden sm:inline">Planning & Management Office (PMO)</span>
                <span class="hidden sm:inline text-slate-600">&bull;</span>
                <span class="text-slate-400">Philippine Standard Time:</span>
                <span id="pst-time" class="font-bold text-amber-400 tracking-wide">PST Manila</span>
            </div>
        </div>
    </div>
    <!-- 2. OFFICIAL AGENCY MASTHEAD (FULL WIDTH) -->
    <header class="bg-white border-b border-slate-300 shadow-sm w-full">
        <div class="w-full px-6 lg:px-12 xl:px-16 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Agency Title and Crests -->
            <div class="flex items-center gap-4">
                <!-- Official BPI Seal -->
                <div
                    class="w-20 h-20 rounded-full bg-white border-2 border-dagreen-800 p-1 flex items-center justify-center shrink-0 shadow-sm">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png"
                        alt="Bureau of Plant Industry Official Seal" class="w-full h-full object-contain">
                </div>
                <div class="border-l border-slate-200 pl-4 space-y-0.5">
                    <p class="text-[11px] uppercase tracking-widest text-slate-500 font-bold">Republic of the
                        Philippines</p>
                    <p class="text-xs uppercase tracking-wider text-slate-700 font-extrabold">Department of Agriculture
                    </p>
                    <h1 class="text-xl sm:text-2xl font-black text-dagreen-950 tracking-tight leading-tight">
                        BUREAU OF PLANT INDUSTRY
                    </h1>
                    <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        Integrated Performance Monitoring System (BPI-IPMS)
                    </p>
                </div>
            </div>
            <!-- Header Action Portal Gateway -->
        </div>
    </header>
    <!-- 3. OFFICIAL ADVISORY BULLETIN STRIP (DYNAMIC REPORTING CYCLE / ERROR STATE) -->
    @if ($currentReportingPeriod)
        <div
            class="bg-amber-50 border-b border-amber-200 px-6 lg:px-12 xl:px-16 py-2.5 text-xs text-amber-950 w-full transition-all">
            <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    <span
                        class="px-2 py-0.5 rounded bg-amber-200 text-amber-900 font-extrabold text-[10px] uppercase tracking-wider">Official
                        Advisory</span>
                    <span class="font-bold text-slate-800">FY {{ $currentReportingPeriod->year }} Monthly Physical
                        Accomplishment Reporting Window:</span>
                    <span class="text-amber-900 font-semibold">
                        {{ $currentReportingPeriod->name }}
                        ({{ $windowStart->format('F d') }}–{{ $windowEnd->format('d, Y') }})
                        &bull;
                        @if ($now->lt($windowStart))
                            <span class="text-blue-800 font-bold">Opens in
                                {{ $now->diffForHumans($windowStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                ({{ $windowStart->format('M d, g:i A') }})</span>
                        @elseif ($now->between($windowStart, $windowEnd))
                            <span class="text-amber-950 font-bold">Closes in
                                {{ $now->diffForHumans($windowEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                ({{ $windowEnd->format('M d, g:i A') }})</span>
                        @else
                            <span class="text-slate-600 font-bold">Closed on
                                {{ $windowEnd->format('M d, Y g:i A') }}</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2 text-[11px] text-amber-900 font-semibold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-ping"></span>
                    <span>BED-2 Physical Performance Monitoring</span>
                </div>
            </div>
        </div>
    @else
        <!-- ERROR STATE: NO ACTIVE REPORTING PERIOD CONFIGURED BY PMO ADMIN -->
        <div class="bg-rose-50 border-b-2 border-rose-300 px-6 lg:px-12 xl:px-16 py-2.5 text-xs text-rose-950 w-full shadow-inner"
            role="alert">
            <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    <span
                        class="px-2.5 py-0.5 rounded bg-rose-600 text-white font-black text-[10px] uppercase tracking-wider flex items-center gap-1 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        System Advisory Error
                    </span>
                    <span class="font-extrabold text-rose-950">No Active Reporting Period Configured:</span>
                    <span class="text-rose-800 font-medium">Physical accomplishment submission is currently locked. The
                        PMO System Administrator has not opened an active reporting cycle for FY
                        {{ date('Y') }}.</span>
                </div>
                <div
                    class="flex items-center gap-2 text-[11px] font-bold text-rose-900 bg-rose-100 border border-rose-300 px-2.5 py-1 rounded-md shrink-0">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="uppercase tracking-wider">Submissions Suspended (0 Active Cycles)</span>
                </div>
            </div>
        </div>
    @endif
    <!-- 4. MAIN INSTITUTIONAL HERO SECTION (FULL SCREEN WIDTH) -->
    <main class="flex-1 py-8 px-6 lg:px-12 xl:px-16 w-full">
        <div class="w-full space-y-7">
            <!-- Government Portal Banner & Dual Action Gateway (Maximized Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <!-- Left: Mandate, System Purpose & Regulatory Framework (8 cols on lg, 9 cols on 2xl) -->
                <div
                    class="lg:col-span-8 2xl:col-span-9 bg-white rounded-xl border border-slate-300 p-7 lg:p-9 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span
                                class="px-3 py-1 rounded bg-dagreen-100 text-dagreen-900 text-xs font-black border border-dagreen-200 uppercase tracking-wider">
                                Official Portal
                            </span>
                            <span class="text-slate-400 text-xs">&bull;</span>
                            <span class="text-slate-600 text-xs font-bold">Text Placeholder</span>
                            <span class="text-slate-400 text-xs">&bull;</span>
                            <span class="text-slate-500 text-xs font-semibold">Text Placeholder</span>
                        </div>
                        <h2
                            class="text-2xl sm:text-3xl xl:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                            Bureau of Plant Industry Monthly Physical Performance Monitoring System
                        </h2>
                        <p class="text-sm xl:text-base text-slate-600 leading-relaxed font-normal">
                            Achilles’ wrath, to Greece the direful spring
                            Of woes unnumber’d, heavenly goddess, sing!
                            That wrath which hurl’d to Pluto’s gloomy reign
                            The souls of mighty chiefs untimely slain;
                            Whose limbs unburied on the naked shore,
                            Devouring dogs and hungry vultures tore.Since great Achilles and Atrides strove,
                            Such was the sovereign doom, and such the will of Jove!
                            Declare, O Muse! in what ill-fated hour
                            Sprung the fierce strife, from what offended power
                            Latona’s son a dire contagion spread
                        </p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-5 border-t border-slate-200">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Covered Units</p>
                            <p class="text-2xl font-black text-dagreen-950 mt-1">{{ $operatingUnitsCount }}<span
                                    class="text-xs font-bold text-slate-600"> Operating Units</span></p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Placeholder</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Submission Window
                            </p>
                            @if ($currentReportingPeriod)
                                <p class="text-2xl font-black text-slate-900 mt-1">
                                    {{ $windowStart->format('jS') }}–{{ $windowEnd->format('jS') }}
                                    <span
                                        class="text-xs font-bold text-slate-600">{{ $windowStart->format('M') }}</span>
                                </p>
                                <p class="text-[11px] text-emerald-700 font-bold mt-0.5">
                                    {{ $currentReportingPeriod->status === 'open' ? 'Active Window Open' : ucfirst($currentReportingPeriod->status) }}
                                </p>
                            @else
                                <p class="text-2xl font-black text-rose-700 mt-1">
                                    Locked
                                    <span class="text-xs font-bold text-rose-600">Inactive</span>
                                </p>
                                <p class="text-[11px] text-rose-600 font-bold mt-0.5">No Active Cycle Set</p>
                            @endif
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Metric Placeholder
                            </p>
                            <p class="text-2xl font-black text-rose-700 mt-1">&ge; 15% <span
                                    class="text-xs font-bold text-slate-600">unit</span></p>
                            <p class="text-[11px] text-rose-600 font-bold mt-0.5">Metric Placeholder Note</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Metric Placeholder
                                #2
                            </p>
                            <p class="text-2xl font-black text-blue-900 mt-1">100% <span
                                    class="text-xs font-bold text-slate-600">unit 2</span></p>
                            <p class="text-[11px] text-blue-700 font-bold mt-0.5">Metric Placeholder Note</p>
                        </div>
                    </div>
                </div>
                <div
                    class="lg:col-span-4 2xl:col-span-3 bg-white rounded-xl border-2 border-dagreen-800 shadow-sm flex flex-col justify-between p-7 space-y-6">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 animate-pulse"></span>
                                <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-900">Personnel
                                    Gateway</h3>
                            </div>
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">Secure</span>
                        </div>
                        <div class="mt-4 space-y-4">
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Access is restricted to authorized Bureau of Plant Industry personnel, division
                                encoders, and designated PMO evaluators.
                            </p>
                            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                <p class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Authorized
                                    Portal Roles:</p>
                                <ul class="list-disc list-inside text-slate-600 space-y-1 text-xs">
                                    <li><strong class="text-slate-800">OU Encoders:</strong> Actuals & MOVs</li>
                                    <li><strong class="text-slate-800">Division Chiefs:</strong> Endorsement</li>
                                    <li><strong class="text-slate-800">PMO Evaluators:</strong> Validation</li>
                                    <li><strong class="text-slate-800">Directorate:</strong> Executive Briefings</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 pt-2">
                        <a href="{{ route('login') }}"
                            class="w-full py-3 bg-dagreen-800 hover:bg-dagreen-900 text-white font-extrabold text-xs rounded-lg shadow transition flex items-center justify-center gap-2">
                            <span>Access Secure Portal</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <div class="text-center pt-1">
                            <span class="text-xs text-slate-500">First time using?</span>
                            <a href="{{ route('register') }}"
                                class="text-xs font-extrabold text-dagreen-800 hover:text-dagreen-950 hover:underline ml-1">
                                Request Personnel Access &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 5. SECTION PLACEHOLDER -->
            <div>
                <div class="border-b-2 border-slate-300 pb-2.5 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Section
                            Placeholder</span>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">Section Title Placeholder</h3>
                    </div>
                    <span
                        class="px-2.5 py-1 rounded bg-slate-100 text-slate-500 font-bold text-xs border border-dashed border-slate-300">
                        Content Placeholder
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    <!-- Placeholder Card 1 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 border-dashed shadow-sm space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm border border-slate-200">
                                01
                            </div>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">
                                Placeholder
                            </span>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-800">Card Title Placeholder 1</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            This card is a placeholder. Relevant section content or operational guidelines will be
                            placed here.
                        </p>
                    </div>
                    <!-- Placeholder Card 2 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 border-dashed shadow-sm space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm border border-slate-200">
                                02
                            </div>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">
                                Placeholder
                            </span>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-800">Card Title Placeholder 2</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            This card is a placeholder. Relevant section content or operational guidelines will be
                            placed here.
                        </p>
                    </div>
                    <!-- Placeholder Card 3 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 border-dashed shadow-sm space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm border border-slate-200">
                                03
                            </div>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">
                                Placeholder
                            </span>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-800">Card Title Placeholder 3</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            This card is a placeholder. Relevant section content or operational guidelines will be
                            placed here.
                        </p>
                    </div>

                    <!-- Placeholder Card 4 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 border-dashed shadow-sm space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm border border-slate-200">
                                04
                            </div>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">
                                Placeholder
                            </span>
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-800">Card Title Placeholder 4</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            This card is a placeholder. Relevant section content or operational guidelines will be
                            placed here.
                        </p>
                    </div>
                </div>
            </div>
            <!-- 6. OPERATING DIVISIONS COVERAGE DIRECTORY -->
            <div class="bg-white p-6 lg:p-7 rounded-xl border border-slate-200 shadow-sm space-y-5">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 pb-3.5 gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">Covered Bureau
                                Operating Units & National Centers</h4>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Central office technical divisions and national
                            seed/quarantine research networks reporting under BPI-IPMS.</p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shrink-0 self-start sm:self-auto">
                        {{ $operatingUnitsCount }} Units Active
                    </span>
                </div>

                @php
                    $nationalCenters = $operatingUnits->filter(function ($u) {
                        return strcasecmp($u->type ?? '', 'Center') === 0 ||
                            stripos($u->code, 'CRDC') !== false ||
                            stripos($u->name, 'Center') !== false;
                    });
                    $centralUnits = $operatingUnits->reject(function ($u) {
                        return strcasecmp($u->type ?? '', 'Center') === 0 ||
                            stripos($u->code, 'CRDC') !== false ||
                            stripos($u->name, 'Center') !== false;
                    });
                @endphp

                @if ($nationalCenters->isNotEmpty() && $centralUnits->isNotEmpty())
                    <!-- Categorized Layout (Accommodates 9 Central Divisions/Offices + 4 National Centers) -->
                    <div class="space-y-6">
                        <!-- Central Technical & Administrative Divisions -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-700">Central
                                    Office Technical Divisions & Offices</span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $centralUnits->count() }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3">
                                @foreach ($centralUnits as $unit)
                                    <div
                                        class="group p-3.5 rounded-xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-dagreen-700 hover:shadow-sm transition-all duration-150 flex flex-col justify-between space-y-2">
                                        <div>
                                            <div class="flex items-center justify-between gap-1.5 mb-1">
                                                <span
                                                    class="font-black text-dagreen-800 text-xs tracking-tight group-hover:text-dagreen-950">
                                                    {{ $unit->code }}
                                                </span>
                                                <span
                                                    class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ strcasecmp($unit->type ?? '', 'Office') === 0 ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-white text-slate-500 border border-slate-200' }}">
                                                    {{ $unit->type ?? 'Division' }}
                                                </span>
                                            </div>
                                            <p
                                                class="text-slate-800 font-bold text-xs leading-snug group-hover:text-slate-900">
                                                {{ $unit->name }}
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center gap-1.5 text-[10px] text-slate-500 group-hover:text-dagreen-800 pt-1.5 border-t border-slate-200/60 mt-auto">
                                            <svg class="w-3 h-3 text-slate-400 group-hover:text-dagreen-700 shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate font-medium">{{ $unit->location }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- National Research & Development Centers -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-black uppercase tracking-wider text-amber-900">National
                                    Crop Research & Development Centers</span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $nationalCenters->count() }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                @foreach ($nationalCenters as $unit)
                                    <div
                                        class="group p-3.5 rounded-xl bg-amber-50/40 hover:bg-white border border-amber-200/70 hover:border-amber-600 hover:shadow-sm transition-all duration-150 flex flex-col justify-between space-y-2">
                                        <div>
                                            <div class="flex items-center justify-between gap-1.5 mb-1">
                                                <span class="font-black text-amber-900 text-xs tracking-tight">
                                                    {{ $unit->code }}
                                                </span>
                                                <span
                                                    class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-white text-amber-800 border border-amber-200">
                                                    National Center
                                                </span>
                                            </div>
                                            <p
                                                class="text-slate-800 font-bold text-xs leading-snug group-hover:text-amber-950">
                                                {{ $unit->name }}
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center gap-1.5 text-[10px] text-amber-800/80 group-hover:text-amber-900 pt-1.5 border-t border-amber-200/50 mt-auto">
                                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate font-medium">{{ $unit->location }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Unified Responsive Grid (Optimized for 7 to 13 units) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 text-xs">
                        @forelse ($operatingUnits as $unit)
                            <div
                                class="group p-3.5 rounded-xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-dagreen-700 hover:shadow-sm transition-all duration-150 flex flex-col justify-between space-y-2">
                                <div>
                                    <div class="flex items-center justify-between gap-1.5 mb-1">
                                        <span
                                            class="font-black text-dagreen-800 text-xs tracking-tight group-hover:text-dagreen-950">
                                            {{ $unit->code }}
                                        </span>
                                        <span
                                            class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $unit->type === 'Center' || str_contains($unit->code, 'CRDC') ? 'bg-amber-100 text-amber-800 border border-amber-200' : ($unit->type === 'Office' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-white text-slate-500 border border-slate-200') }}">
                                            {{ $unit->type ?? 'Division' }}
                                        </span>
                                    </div>
                                    <p
                                        class="text-slate-800 font-bold text-xs leading-snug group-hover:text-slate-900">
                                        {{ $unit->name }}
                                    </p>
                                </div>
                                <div
                                    class="flex items-center gap-1.5 text-[10px] text-slate-500 group-hover:text-dagreen-800 pt-1.5 border-t border-slate-200/60 mt-auto">
                                    <svg class="w-3 h-3 text-slate-400 group-hover:text-dagreen-700 shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate font-medium">{{ $unit->location }}</span>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full p-6 text-center text-slate-500 text-xs bg-slate-50 rounded-xl border border-dashed border-slate-300">
                                No operating units currently configured.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

        </div>
    </main>
    <!-- 7. OFFICIAL STANDARD GOVPH FOOTER (FULL WIDTH) -->
    <footer class="bg-[#0b1f3a] text-slate-300 text-xs mt-10 border-t-4 border-dagreen-800 w-full">
        <div class="w-full px-6 lg:px-12 xl:px-16 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 xl:gap-12">
            <!-- Column 1: Republic Seal & Agency Mandate -->
            <div class="space-y-4">
                <div class="flex items-center gap-3.5">
                    <!-- Official Republic Coat of Arms (Great Seal) -->
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/84/Coat_of_arms_of_the_Philippines.svg"
                        alt="Coat of Arms of the Philippines"
                        class="w-14 h-14 object-contain shrink-0 filter drop-shadow">

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/9/99/Flag_of_the_Philippines.svg"
                                alt="Flag of the Philippines"
                                class="w-6 h-3.5 object-cover rounded-[2px] shadow-sm border border-white/20 shrink-0">
                            <p class="font-black text-white text-xs uppercase tracking-wider leading-snug">
                                Republic of the Philippines
                            </p>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-tight">
                            All content is in the public domain unless otherwise stated.
                        </p>
                    </div>
                </div>

                <!-- Agency Portal Description -->
                <p class="text-[11px] text-slate-400 leading-relaxed border-t border-slate-700/60 pt-3">
                    Official portal of the Bureau of Plant Industry, Department of Agriculture for physical
                    accountability reporting, BED-2 execution, and MOV verification.
                </p>
            </div>
            <!-- Column 2: Government Links -->
            <div class="space-y-2">
                <p class="font-black text-white uppercase tracking-wider text-xs border-b border-slate-700 pb-1.5">
                    Government Links</p>
                <ul class="space-y-1.5 text-[11px] text-slate-300">
                    <li><a href="https://da.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull;
                            Department of Agriculture</a></li>
                    <li><a href="https://buplant.da.gov.ph/" target="_blank"
                            class="hover:text-amber-400 transition">&bull;
                            Bureau of Plant Industry</a></li>
                    <li><a href="https://da.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull;
                            Another link placeholder</a></li>
                    <li><a href="https://da.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull;
                            Another link placeholder</a></li>
                </ul>
            </div>
            <!-- Column 3: Bureau Office Directory -->
            <div class="space-y-2">
                <p class="font-black text-white uppercase tracking-wider text-xs border-b border-slate-700 pb-1.5">
                    Bureau of Plant Industry</p>
                <div class="text-[11px] text-slate-300 space-y-1 leading-relaxed">
                    <p class="font-bold text-white">Central Office:</p>
                    <p>692 San Andres Street, Malate, Manila, 1004 Metro Manila, Philippines</p>
                    <p><span class="text-slate-400">PMO Inquiries:</span> (02) 67-67676</p>
                    <p><span class="text-slate-400">Email:</span> pmo@bpi.gov.ph</p>
                </div>
            </div>
            <!-- Column 4: Compliance & FOI -->
            <div class="space-y-2">
                <p class="font-black text-white uppercase tracking-wider text-xs border-b border-slate-700 pb-1.5">
                    System Governance</p>
                <div class="space-y-2 text-[11px] text-slate-300">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded bg-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-amber-400">🛡️</span>
                        <span>Data Privacy Act of 2012 (RA 10173)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded bg-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-amber-400">⚖️</span>
                        <span>DBM Budget Execution Guidelines</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="w-6 h-6 rounded bg-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-amber-400">📋</span>
                        <span>Freedom of Information (FOI)</span>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="bg-[#071527] py-3.5 px-6 lg:px-12 xl:px-16 text-center text-[10px] text-slate-400 border-t border-slate-800 w-full">
            <p>&copy; {{ date('Y') }} Bureau of Plant Industry &bull; Department of Agriculture. All Rights
                Reserved.</p>
        </div>
    </footer>
    <!-- Official PST Clock Script -->
    <script>
        function updatePST() {
            const options = {
                timeZone: 'Asia/Manila',
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const el = document.getElementById('pst-time');
            if (el) el.textContent = formatter.format(new Date()) + ' PST';
        }
        setInterval(updatePST, 1000);
        updatePST();
    </script>
</body>

</html>
