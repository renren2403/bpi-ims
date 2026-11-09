<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bureau of Plant Industry - Integrated Performance Monitoring System (BPI-IMS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        govblue: {
                            800: '#1e3a8a',
                            900: '#0f2454',
                            950: '#081430'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Top Gov Banner -->
    <div class="bg-govblue-950 text-slate-300 text-[11px] px-6 py-2 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="font-bold text-white uppercase tracking-wider">Republic of the Philippines</span>
                <span>&bull;</span>
                <span>Department of Agriculture</span>
                <span>&bull;</span>
                <span class="text-emerald-400 font-semibold">Bureau of Plant Industry</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Planning & Management Office (PMO)</span>
                <span>&bull;</span>
                <span id="pst-time" class="font-medium text-slate-300">Philippine Standard Time</span>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-emerald-50 border-2 border-emerald-500 flex items-center justify-center text-2xl shadow-sm">
                    🌱
                </div>
                <div>
                    <h1 class="font-extrabold text-lg text-govblue-950 tracking-tight leading-tight">BPI - IMS PORTAL</h1>
                    <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Physical Performance Monitoring System</p>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg text-xs font-bold text-slate-700 hover:text-govblue-900 hover:bg-slate-100 transition border border-slate-300">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-lg text-xs font-bold bg-blue-700 hover:bg-blue-800 text-white shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Request Account
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-govblue-950 via-govblue-900 to-govblue-950 text-white py-20 px-6 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="max-w-5xl mx-auto text-center relative z-10 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/20 border border-blue-400/40 text-blue-200 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Executive Physical Performance & BED-2 Monitoring
            </div>

            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight text-white max-w-4xl mx-auto">
                Strengthening Bureau Accountability Through Real-Time Performance Intelligence
            </h2>

            <p class="text-base sm:text-lg text-slate-300 max-w-3xl mx-auto font-normal leading-relaxed">
                The centralized monitoring platform for the Bureau of Plant Industry. Designed to streamline BED-2 target allocations, monthly division accomplishment tracking, critical slippage mitigation, and audit-compliant Means of Verification.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <span>Access Command Portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-extrabold text-sm rounded-xl border border-white/20 transition flex items-center justify-center gap-2">
                    <span>Register New Account</span>
                </a>
            </div>

            <div class="pt-8 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-6 text-slate-400 text-xs max-w-3xl mx-auto">
                <div>
                    <p class="text-xl font-extrabold text-white">7+</p>
                    <p class="mt-0.5 text-slate-400">Operating Divisions</p>
                </div>
                <div>
                    <p class="text-xl font-extrabold text-white">100%</p>
                    <p class="mt-0.5 text-slate-400">BED-2 Tracking</p>
                </div>
                <div>
                    <p class="text-xl font-extrabold text-white">&ge; 15%</p>
                    <p class="mt-0.5 text-slate-400">Slippage Auto-Flag</p>
                </div>
                <div>
                    <p class="text-xl font-extrabold text-white">Role-Based</p>
                    <p class="mt-0.5 text-slate-400">Strict Governance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Capabilities Pillars -->
    <section class="py-16 px-6 bg-slate-50 flex-1">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center max-w-2xl mx-auto">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-700">System Governance Architecture</h3>
                <h4 class="text-2xl font-extrabold text-slate-900 mt-1">End-to-End Performance Tracking Workflow</h4>
                <p class="text-xs text-slate-500 mt-2">Built specifically around the operational procedures of the BPI Planning & Management Office.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pillar 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center font-bold text-lg">
                        📋
                    </div>
                    <h5 class="font-extrabold text-slate-900 text-base">BED-2 Physical Plan Profiling</h5>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pre-configure annual, quarterly, and month-by-month physical targets for every approved Program, Activity, and Project (PAP) and its indicators.
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold text-lg">
                        ⚡
                    </div>
                    <h5 class="font-extrabold text-slate-900 text-base">Monthly Accomplishment Tracking</h5>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Operating unit encoders submit actual physical deliveries during the official monthly cycle window (21st–26th) with multi-level endorsement.
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center font-bold text-lg">
                        🔍
                    </div>
                    <h5 class="font-extrabold text-slate-900 text-base">Root Cause Analysis (RCA)</h5>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Automated flagging of any lagging indicator with &ge;15% negative slippage, mandating structured immediate corrections and preventative actions.
                    </p>
                </div>
            </div>

            <!-- Operating Units Showcase -->
            <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-base">Covered Bureau Operating Units</h4>
                        <p class="text-xs text-slate-500">Divisions and centers actively reporting under the physical monitoring system.</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 self-start">
                        BPI Mandate Coverage
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mt-6 text-xs font-semibold text-slate-700">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Crop Pest Management (CPMD)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Plant Product Development (PPD)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Natl. Seed Quality Control (NSQCS)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Plant Quarantine Services (NPQSD)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Crop Research & Prod. (CRD)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Agricultural Laboratory (ALGD)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span>Planning & Management (PMO)</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>National Crop Centers & Stations</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-govblue-950 text-slate-400 text-xs py-8 px-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-400 flex items-center justify-center text-emerald-400 font-bold">
                    🌱
                </div>
                <div>
                    <p class="font-bold text-white text-xs">Bureau of Plant Industry</p>
                    <p class="text-[10px] text-slate-400">Department of Agriculture &bull; Republic of the Philippines</p>
                </div>
            </div>

            <div class="text-center md:text-right text-[11px] text-slate-400">
                <p>Planning and Management Office &bull; San Andres St., Malate, Manila</p>
                <p class="text-slate-500 mt-0.5">&copy; {{ date('Y') }} Bureau of Plant Industry. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function updatePST() {
            const now = new Date();
            const options = { timeZone: 'Asia/Manila', weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const timeString = new Intl.DateTimeFormat('en-US', options).format(now);
            const pstEl = document.getElementById('pst-time');
            if (pstEl) pstEl.textContent = 'PST: ' + timeString;
        }
        updatePST();
        setInterval(updatePST, 1000);
    </script>
</body>
</html>
