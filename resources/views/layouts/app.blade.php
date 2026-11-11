<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Command Center') &bull; BPI-IPMS Portal</title>
    <link rel="icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <link rel="shortcut icon" type="image/png"
        href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        botanic: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-[#f8fafc] text-slate-800 flex h-screen overflow-hidden antialiased selection:bg-emerald-500 selection:text-white">
    <!-- 1. MODERN SLEEK FLOATING SIDEBAR (Top-Left & Bottom-Left Anchored) -->
    <aside
        class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 z-20 shadow-[1px_0_10px_rgba(0,0,0,0.02)]">
        <div>
            <!-- Top-Left Corner Brand Badge -->
            <a href="{{ route('dashboard') }}"
                class="h-16 flex items-center px-5 gap-3 border-b border-slate-100 hover:bg-slate-50/50 transition">
                <div
                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center shadow-sm shrink-0">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png" alt="BPI Logo"
                        class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-sm tracking-tight text-slate-900">BPI-IPMS</span>
                        <span
                            class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800 tracking-wider">v2.0</span>
                    </div>
                    @php
                        $userRoleTitle = 'SYSTEM';
                        if (Auth::check()) {
                            $currentUser = Auth::user();
                            if ($currentUser->hasAnyRole(['superadmin', 'admin'])) {
                                $userRoleTitle = 'ADMIN';
                            } elseif ($currentUser->hasRole('evaluator')) {
                                $userRoleTitle = 'EVALUATOR';
                            } elseif ($currentUser->hasRole('ou-head')) {
                                $userRoleTitle = 'DIVISION CHIEF';
                            } elseif ($currentUser->hasRole('encoder')) {
                                $userRoleTitle = 'ENCODER';
                            } elseif ($currentUser->hasRole('higher-up')) {
                                $userRoleTitle = 'EXECUTIVE';
                            } else {
                                $primaryRole = $currentUser->roles->first()?->name;
                                $userRoleTitle = $primaryRole ? strtoupper(str_replace(['-', '_'], ' ', $primaryRole)) : 'USER';
                            }
                        }
                    @endphp
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider truncate">
                        {{ $userRoleTitle }} DASHBOARD
                    </p>
                </div>
            </a>

            <!-- Navigation Sections -->
            <div class="p-3.5 space-y-5">
                <!-- Section 1: Executive Space -->
                <div>
                    <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-1.5">Workspace
                    </p>
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('*.dashboard') || request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 shadow-sm border border-emerald-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 {{ request()->routeIs('*.dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Command Center</span>
                            </div>
                            @if (request()->routeIs('*.dashboard'))
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            @endif
                        </a>

                        <a href="#"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold text-xs transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>BED-2 Target Plans</span>
                        </a>

                        <a href="#"
                            class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold text-xs transition">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span>Monthly Reports</span>
                            </div>
                            <span
                                class="px-2 py-0.5 text-[10px] font-extrabold bg-blue-100 text-blue-700 rounded-full">{{ date('M') }}</span>
                        </a>

                        <a href="#"
                            class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold text-xs transition">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                                <span>Unlock Requests</span>
                            </div>
                            <span
                                class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-800 rounded-full">2</span>
                        </a>
                    </nav>
                </div>

                <!-- Section 2: PMO Administration & Governance (Admin & PMO Evaluators) -->
                @hasanyrole('superadmin|admin|evaluator')
                    <div>
                        <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-1.5">PMO
                            Governance</p>
                        <nav class="space-y-1">
                            <!-- Operating Units Link -->
                            <a href="{{ route('admin.operating-units.index') }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.operating-units.*')
                                    ? 'bg-emerald-50 text-emerald-800 shadow-sm border border-emerald-200/50'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 {{ request()->routeIs('admin.operating-units.*') ? 'text-emerald-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Operating Units</span>
                                </div>
                                <span
                                    class="px-2 py-0.5 text-[10px] font-extrabold bg-slate-100 text-slate-700 rounded-full">
                                    {{ \App\Models\OperatingUnit::count() }}
                                </span>
                            </a>

                            <!-- User Management Link (Existing) -->
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-800 shadow-sm border border-emerald-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-emerald-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>User Management</span>
                                </div>
                                @php
                                    $pendingApprovals = 0;
                                    try {
                                        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'approval_status')) {
                                            $pendingApprovals = \App\Models\User::where(
                                                'approval_status',
                                                'pending',
                                            )->count();
                                        }
                                    } catch (\Throwable $e) {
                                        $pendingApprovals = 0;
                                    }
                                @endphp
                                @if ($pendingApprovals > 0)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-500 text-white rounded-full">{{ $pendingApprovals }}</span>
                                @endif
                            </a>

                            <a href="{{ route('admin.reporting-periods.index') }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.reporting-periods.*') ? 'bg-emerald-50 text-emerald-800 shadow-sm border border-emerald-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 {{ request()->routeIs('admin.reporting-periods.*') ? 'text-emerald-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Reporting Cycle Rules</span>
                                </div>
                                @php
                                    $hasActiveCycle = false;
                                    try {
                                        $hasActiveCycle = \App\Models\ReportingPeriod::where(
                                            'is_active',
                                            true,
                                        )->exists();
                                    } catch (\Throwable $e) {
                                        $hasActiveCycle = false;
                                    }
                                @endphp
                                @if ($hasActiveCycle)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                                        title="Active Cycle Open"></span>
                                @else
                                    <span class="px-1.5 py-0.5 text-[9px] font-black bg-rose-100 text-rose-700 rounded-md"
                                        title="No Active Cycle Configured">OFF</span>
                                @endif
                            </a>

                            <a href="#"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-semibold text-xs transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Audit & Security Logs</span>
                            </a>
                        </nav>
                    </div>
                @endhasanyrole

                <!-- Section 3: Account -->
                <div>
                    <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-1.5">My
                        Account</p>
                    <nav class="space-y-1">
                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition {{ request()->routeIs('profile.show') ? 'bg-emerald-50 text-emerald-800 shadow-sm border border-emerald-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('profile.show') ? 'text-emerald-600' : 'text-slate-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>My Profile & Password</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Bottom-Left Corner User Profile Pill & Logout -->
        <div class="p-3.5 border-t border-slate-100 bg-slate-50/70">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-botanic-800 text-white font-extrabold text-xs flex items-center justify-center shadow-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-500 font-medium truncate">
                            {{ Auth::user()->roles->pluck('name')->first() ?? 'User' }} &bull;
                            {{ Auth::user()->operatingUnit?->code ?? 'Central' }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                        class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- 2. MAIN WORKSPACE WITH CORNER-OPTIMIZED TOPBAR & CONTAINER -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Modern Floating Topbar (Anchored to Margins) -->
        <header
            class="h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-5 lg:px-6 flex items-center justify-between shrink-0 z-10">
            <!-- Search & Cycle Indicator (Left Anchor) -->
            <div class="flex items-center gap-4">
                <div class="relative w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Search PAPs, indicators (Ctrl+K)..."
                        class="w-full pl-9 pr-3 py-1.5 bg-slate-100/70 border border-slate-200 rounded-xl text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                </div>

                <div class="h-5 w-px bg-slate-200 hidden md:block"></div>

                <!-- Active Cycle Pill -->
                <div
                    class="hidden lg:flex items-center gap-2 px-3 py-1 bg-slate-100/80 border border-slate-200/80 rounded-xl text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="font-bold text-slate-700">FY {{ date('Y') }} &bull; {{ date('F') }} Cycle
                        (21st–26th)</span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-emerald-700 font-semibold">Active Window</span>
                </div>
            </div>

            <!-- Topbar Actions (Right Anchor) -->
            <div class="flex items-center gap-3">
                <div
                    class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-semibold pr-2 border-r border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span id="app-pst-clock">PST Manila</span>
                </div>

                <span
                    class="font-bold text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl">
                    {{ Auth::user()->operatingUnit?->name ?? 'Central Planning Office' }}
                </span>
            </div>
        </header>

        <!-- Flash Messages & Main Content Workspace (Corner-Optimized Padding) -->
        <main class="flex-1 overflow-y-auto p-5 lg:p-6 space-y-5">
            @if (session('status'))
                <div
                    class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-xl shadow-sm flex items-center gap-2">
                    <span>✓</span> {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div
                    class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-xl shadow-sm flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Live PST Clock Script -->
    <script>
        function updatePST() {
            const options = {
                timeZone: 'Asia/Manila',
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const formatter = new Intl.DateTimeFormat('en-US', options);
            const el = document.getElementById('app-pst-clock');
            if (el) el.textContent = formatter.format(new Date()) + ' PST';
        }
        setInterval(updatePST, 1000);
        updatePST();
    </script>
</body>

</html>
