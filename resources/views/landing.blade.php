<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrated Performance Monitoring System (BPI-IPMS) &bull; Bureau of Plant Industry</title>
    <link rel="icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <link rel="shortcut icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 flex flex-col min-h-screen antialiased selection:bg-dagreen-800 selection:text-white">
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
        $now = \Illuminate\Support\Carbon::now();
        $windowStart = $currentReportingPeriod ? \Illuminate\Support\Carbon::parse($currentReportingPeriod->window_start) : null;
        $windowEnd = $currentReportingPeriod ? \Illuminate\Support\Carbon::parse($currentReportingPeriod->window_end) : null;
    @endphp
    <!-- 1. OFFICIAL TOP BAR (FULL WIDTH) -->
    <div class="bg-[#0b1f3a] text-slate-300 text-[11px] px-6 lg:px-12 xl:px-16 py-2 border-b-2 border-amber-500 w-full">
        <div class="w-full flex flex-col md:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2.5">
                <span class="font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png" alt="DA-BPI Logo" class="w-5 h-5 object-contain shrink-0">
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
                <div class="w-20 h-20 rounded-full bg-white border-2 border-dagreen-800 p-1 flex items-center justify-center shrink-0 shadow-sm">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png" alt="Bureau of Plant Industry Official Seal" class="w-full h-full object-contain">
                </div>
                <div class="border-l border-slate-200 pl-4 space-y-0.5">
                    <p class="text-[11px] uppercase tracking-widest text-slate-500 font-bold">Republic of the Philippines</p>
                    <p class="text-xs uppercase tracking-wider text-slate-700 font-extrabold">Department of Agriculture</p>
                    <h1 class="text-xl sm:text-2xl font-black text-dagreen-950 tracking-tight leading-tight">
                        BUREAU OF PLANT INDUSTRY
                    </h1>
                    <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        Integrated Performance Monitoring System (BPI-IPMS)
                    </p>
                </div>
            </div>
            <!-- Header Action Portal Gateway -->
            <div class="flex items-center gap-3 self-end md:self-center">
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition border border-slate-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Personnel Sign In</span>
                </a>
            </div>
        </div>
    </header>
    <!-- 3. OFFICIAL ADVISORY BULLETIN STRIP (DYNAMIC REPORTING CYCLE / ERROR STATE) -->
    @if ($currentReportingPeriod)
        <div class="bg-amber-50 border-b border-amber-200 px-6 lg:px-12 xl:px-16 py-2.5 text-xs text-amber-950 w-full transition-all">
            <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    <span class="px-2 py-0.5 rounded bg-amber-200 text-amber-900 font-extrabold text-[10px] uppercase tracking-wider">Official Advisory</span>
                    <span class="font-bold text-slate-800">FY {{ $currentReportingPeriod->year }} Monthly Physical Accomplishment Reporting Window:</span>
                    <span class="text-amber-900 font-semibold">
                        {{ $currentReportingPeriod->name }} ({{ $windowStart->format('F d') }}–{{ $windowEnd->format('d, Y') }})
                        &bull;
                        @if ($now->lt($windowStart))
                            <span class="text-blue-800 font-bold">Opens in {{ $now->diffForHumans($windowStart, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} ({{ $windowStart->format('M d, g:i A') }})</span>
                        @elseif ($now->between($windowStart, $windowEnd))
                            <span class="text-amber-950 font-bold">Closes in {{ $now->diffForHumans($windowEnd, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} ({{ $windowEnd->format('M d, g:i A') }})</span>
                        @else
                            <span class="text-slate-600 font-bold">Closed on {{ $windowEnd->format('M d, Y g:i A') }}</span>
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
        <div class="bg-rose-50 border-b-2 border-rose-300 px-6 lg:px-12 xl:px-16 py-2.5 text-xs text-rose-950 w-full shadow-inner" role="alert">
            <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    <span class="px-2.5 py-0.5 rounded bg-rose-600 text-white font-black text-[10px] uppercase tracking-wider flex items-center gap-1 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        System Advisory Error
                    </span>
                    <span class="font-extrabold text-rose-950">No Active Reporting Period Configured:</span>
                    <span class="text-rose-800 font-medium">Physical accomplishment submission is currently locked. The PMO System Administrator has not opened an active reporting cycle for FY {{ date('Y') }}.</span>
                </div>
                <div class="flex items-center gap-2 text-[11px] font-bold text-rose-900 bg-rose-100 border border-rose-300 px-2.5 py-1 rounded-md shrink-0">
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
                <div class="lg:col-span-8 2xl:col-span-9 bg-white rounded-xl border border-slate-300 p-7 lg:p-9 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="px-3 py-1 rounded bg-dagreen-100 text-dagreen-900 text-xs font-black border border-dagreen-200 uppercase tracking-wider">
                                Official Intranet Portal
                            </span>
                            <span class="text-slate-400 text-xs">&bull;</span>
                            <span class="text-slate-600 text-xs font-bold">DBM-COA-NEDA Joint Circular No. 2013-1</span>
                            <span class="text-slate-400 text-xs">&bull;</span>
                            <span class="text-slate-500 text-xs font-semibold">National Budget Circular No. 590</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl xl:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                            Bureau of Plant Industry Physical Performance Monitoring & Accountability System
                        </h2>
                        <p class="text-sm xl:text-base text-slate-600 leading-relaxed font-normal">
                            Pursuant to the Department of Budget and Management (DBM) and Commission on Audit (COA) reporting guidelines, the <strong>BPI-IPMS</strong> is the bureau’s centralized information system for tracking <strong>Budget Execution Document No. 2 (BED-2)</strong> physical targets, monthly division accomplishments, root cause analysis of lagging programs, and verified Means of Verification (MOVs).
                        </p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-5 border-t border-slate-200">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Covered Units</p>
                            <p class="text-2xl font-black text-dagreen-950 mt-1">12 <span class="text-xs font-bold text-slate-600">Divisions</span></p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Central & National Centers</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Submission Window</p>
                            @if ($currentReportingPeriod)
                                <p class="text-2xl font-black text-slate-900 mt-1">
                                    {{ $windowStart->format('jS') }}–{{ $windowEnd->format('jS') }}
                                    <span class="text-xs font-bold text-slate-600">{{ $windowStart->format('M') }}</span>
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
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Slippage Threshold</p>
                            <p class="text-2xl font-black text-rose-700 mt-1">&ge; 15% <span class="text-xs font-bold text-slate-600">RCA</span></p>
                            <p class="text-[11px] text-rose-600 font-bold mt-0.5">Mandatory Catch-up Plan</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-[11px] uppercase font-bold text-slate-500 tracking-wider">Audit Compliance</p>
                            <p class="text-2xl font-black text-blue-900 mt-1">100% <span class="text-xs font-bold text-slate-600">COA Defensible</span></p>
                            <p class="text-[11px] text-blue-700 font-bold mt-0.5">Archived MOV Repository</p>
                        </div>
                    </div>
                </div>
                  <div class="lg:col-span-4 2xl:col-span-3 bg-white rounded-xl border-2 border-dagreen-800 shadow-sm flex flex-col justify-between p-7 space-y-6">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-600 animate-pulse"></span>
                                <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-900">Personnel Gateway</h3>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">Secure</span>
                        </div>
                        <div class="mt-4 space-y-4">
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Access is restricted to authorized Bureau of Plant Industry personnel, division encoders, and designated PMO evaluators.
                            </p>
                            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                <p class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Authorized Portal Roles:</p>
                                <ul class="list-disc list-inside text-slate-600 space-y-1 text-xs">
                                    <li><strong class="text-slate-800">OU Encoders:</strong> Actuals & MOVs</li>
                                    <li><strong class="text-slate-800">Division Chiefs:</strong> Endorsement</li>
                                    <li><strong class="text-slate-800">PMO Evaluators:</strong> Validation & BAR-1</li>
                                    <li><strong class="text-slate-800">Directorate:</strong> Executive Briefings</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 pt-2">
                        <a href="{{ route('login') }}" class="w-full py-3 bg-dagreen-800 hover:bg-dagreen-900 text-white font-extrabold text-xs rounded-lg shadow transition flex items-center justify-center gap-2">
                            <span>Access Secure Portal</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <div class="text-center pt-1">
                            <span class="text-xs text-slate-500">First time reporting?</span>
                            <a href="{{ route('register') }}" class="text-xs font-extrabold text-dagreen-800 hover:text-dagreen-950 hover:underline ml-1">
                                Request Personnel Access &rarr;
                            </a>
                        </div>
                        <!-- Security & Data Warning -->
                        <p class="text-[10px] text-slate-500 text-center leading-tight pt-2 border-t border-slate-100">
                            ⚠️ Republic Act No. 10175: Unauthorized access is strictly prohibited and subject to legal prosecution.
                        </p>
                    </div>
                </div>
            </div>
            <!-- 5. FOUR CORE INSTITUTIONAL GOVERNANCE PILLARS (FULL WIDTH GRID) -->
            <div>
                <div class="border-b-2 border-slate-300 pb-2.5 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-dagreen-800 uppercase tracking-widest">Public Accountability Modules</span>
                        <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">Physical Performance Governance Framework</h3>
                    </div>
                    <span class="hidden sm:inline text-xs text-slate-500 font-semibold">DBM Budget Execution Guidelines</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    
                    <!-- Pillar 1 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2.5">
                        <div class="w-11 h-11 rounded-lg bg-emerald-50 text-dagreen-800 flex items-center justify-center font-bold text-xl border border-emerald-200">
                            📑
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900">1. BED-2 Physical Targets</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Official disaggregation of annual GAA commitments into monthly physical milestones for every bureau Program, Activity, and Project (PAP).
                        </p>
                    </div>

                    <!-- Pillar 2 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2.5">
                        <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-800 flex items-center justify-center font-bold text-xl border border-blue-200">
                            ⏱️
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900">2. Synchronized Submission</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Automated 21st–26th monthly encoding cycle. Locking ensures immutable accomplishment records, division sign-off, and COA audit integrity.
                        </p>
                    </div>

                    <!-- Pillar 3 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2.5">
                        <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center font-bold text-xl border border-amber-200">
                            🔍
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900">3. Root Cause Analysis (RCA)</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Automated flagging of physical slippages &ge; 15%. Mandates root cause diagnosis, immediate catch-up actions, and preventative plans.
                        </p>
                    </div>

                    <!-- Pillar 4 -->
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2.5">
                        <div class="w-11 h-11 rounded-lg bg-purple-50 text-purple-800 flex items-center justify-center font-bold text-xl border border-purple-200">
                            📂
                        </div>
                        <h4 class="font-extrabold text-sm text-slate-900">4. Means of Verification (MOV)</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mandatory PDF document repository (training registries, inspection reports, laboratory certificates) indexed per accomplishment figure.
                        </p>
                    </div>
                </div>
            </div>
            <!-- 6. OPERATING DIVISIONS COVERAGE DIRECTORY (FULL WIDTH GRID - ALL 12 DIVISIONS) -->
            <div class="bg-white p-6 lg:p-7 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">Covered Bureau Operating Units & National Centers</h4>
                        <p class="text-xs text-slate-500">Central office technical divisions and national seed/quarantine networks reporting under BPI-IPMS.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-bold text-xs border">12 Units Active</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3.5 text-xs">
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">CPMD</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Crop Pest Management Division</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">CRD</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Crop Research & Dev. Division</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">NPQSD</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Plant Quarantine Services Division</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">NSQCS</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">National Seed Quality Control</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">PPD</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Plant Product Development Div.</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">AED</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Agricultural Engineering Division</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">PMO</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Planning & Management Office</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">ADMIN</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Administrative & Finance Division</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">BNCRDC</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Baguio National Crop Research</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">LNCRDC</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Los Baños Crop Research Center</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">DNCRDC</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Davao National Crop Research</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <span class="font-black text-dagreen-800 text-xs">GNCRDC</span>
                        <p class="text-slate-700 font-semibold text-[11px] leading-tight">Guimaras National Crop Research</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <!-- 7. OFFICIAL STANDARD GOVPH FOOTER (FULL WIDTH) -->
    <footer class="bg-[#0b1f3a] text-slate-300 text-xs mt-10 border-t-4 border-dagreen-800 w-full">
        <div class="w-full px-6 lg:px-12 xl:px-16 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 xl:gap-12">
            <!-- Column 1: Republic Seal -->
            <div class="space-y-3">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🇵🇭</span>
                    <div>
                        <p class="font-black text-white text-xs uppercase tracking-wider">Republic of the Philippines</p>
                        <p class="text-[10px] text-slate-400">All content is in the public domain unless otherwise stated.</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Official portal of the Bureau of Plant Industry, Department of Agriculture for physical accountability reporting, BED-2 execution, and MOV verification.
                </p>
            </div>
            <!-- Column 2: Government Links -->
            <div class="space-y-2">
                <p class="font-black text-white uppercase tracking-wider text-xs border-b border-slate-700 pb-1.5">Government Links</p>
                <ul class="space-y-1.5 text-[11px] text-slate-300">
                    <li><a href="https://www.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull; Office of the President</a></li>
                    <li><a href="https://da.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull; Department of Agriculture</a></li>
                    <li><a href="https://dbm.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull; Dept. of Budget and Management</a></li>
                    <li><a href="https://coa.gov.ph" target="_blank" class="hover:text-amber-400 transition">&bull; Commission on Audit</a></li>
                </ul>
            </div>
            <!-- Column 3: Bureau Office Directory -->
            <div class="space-y-2">
                <p class="font-black text-white uppercase tracking-wider text-xs border-b border-slate-700 pb-1.5">Bureau of Plant Industry</p>
                <div class="text-[11px] text-slate-300 space-y-1 leading-relaxed">
                    <p class="font-bold text-white">Central Office:</p>
                    <p>692 San Andres Street, Malate, Manila, 1004 Metro Manila, Philippines</p>
                    <p><span class="text-slate-400">PMO Inquiries:</span> (02) 8525-7857</p>
                    <p><span class="text-slate-400">Email:</span> pmo@bpi.gov.ph</p>
                </div>
            </div>
            <!-- Column 4: Compliance & FOI -->
            <div class="space-y-2">
                <p class="font-black text-white uppercase tracking-wider text-xs border-b border-slate-700 pb-1.5">System Governance</p>
                <div class="space-y-2 text-[11px] text-slate-300">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-amber-400">🛡️</span>
                        <span>Data Privacy Act of 2012 (RA 10173)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-amber-400">⚖️</span>
                        <span>DBM Budget Execution Guidelines</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-amber-400">📋</span>
                        <span>Freedom of Information (FOI)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-[#071527] py-3.5 px-6 lg:px-12 xl:px-16 text-center text-[10px] text-slate-400 border-t border-slate-800 w-full">
            <p>&copy; {{ date('Y') }} Bureau of Plant Industry &bull; Department of Agriculture. All Rights Reserved.</p>
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
