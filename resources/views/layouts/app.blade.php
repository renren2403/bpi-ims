<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Bureau of Plant Industry (BPI-IMS)</title>
    
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
<body class="bg-slate-50 text-slate-800 flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-govblue-950 text-white flex flex-col justify-between shrink-0 shadow-xl z-20">
        <div>
            <!-- Bureau Brand -->
            <div class="h-16 flex items-center px-6 gap-3 border-b border-slate-800 bg-govblue-900/60">
                <div class="w-9 h-9 rounded-full bg-emerald-500/20 border border-emerald-400 flex items-center justify-center font-bold text-emerald-400 text-lg">
                    🌱
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-wide text-white leading-tight">BPI - IMS PORTAL</h1>
                    <p class="text-[10px] text-slate-400 font-medium">Planning & Management Office</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Main Menu</p>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('*.dashboard') ? 'bg-blue-600/30 text-blue-300 font-semibold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Command Dashboard</span>
                </a>

                {{-- Admin Only Navigation --}}
                @hasanyrole('superadmin|admin')
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Administration</p>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-600/30 text-blue-300 font-semibold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} text-sm transition">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>User Management</span>
                </a>
                @endhasanyrole

                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-6 mb-2">Account</p>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('profile.show') ? 'bg-blue-600/30 text-blue-300 font-semibold border-l-4 border-blue-500' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} text-sm transition">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>My Profile</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Pill & Logout -->
        <div class="p-4 border-t border-slate-800/80 bg-govblue-900/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-xs border border-blue-400 shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-blue-300 font-medium truncate">
                            {{ Auth::user()->roles->pluck('name')->implode(', ') ?: 'No Role' }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="text-slate-400 hover:text-rose-400 p-1.5 rounded transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN VIEWPORT -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Operating Unit:</span>
                <span class="font-bold text-xs text-govblue-900 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-md">
                    {{ Auth::user()->operatingUnit?->name ?? 'Central Agency' }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500">Role:</span>
                <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300 rounded-full uppercase">
                    {{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}
                </span>
            </div>
        </header>

        <!-- Flash Messages & Page Body -->
        <main class="flex-1 overflow-y-auto p-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-sm rounded-r-lg shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-sm rounded-r-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>