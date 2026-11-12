<!--show.blade.php profile-->
@extends('layouts.app')
@section('title', 'My Profile & Credentials')

@section('content')
    <div class="max-w-4xl space-y-7">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Account Profile & Security</h2>
            <p class="text-xs text-slate-500 mt-0.5">View your agency assignments, verified role credentials, and update your
                account password.</p>
        </div>

        <!-- Account Details -->
        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
            <div class="flex items-center gap-4 border-b border-slate-100 pb-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-botanic-800 text-white font-extrabold text-xl flex items-center justify-center shadow-md shadow-emerald-500/20">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">{{ $user->name }}</h3>
                    <p class="text-xs text-emerald-800 font-semibold">{{ $user->designation ?? 'Bureau Personnel' }} &bull;
                        {{ $user->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Official Email</p>
                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $user->email }}</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Assigned Operating Unit
                    </p>
                    <p class="font-bold text-slate-800 mt-1 text-sm">
                        {{ $user->operatingUnit?->name ?? 'Central Planning Office' }}
                        ({{ $user->operatingUnit?->code ?? 'CENTRAL' }})</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Designation / Plantilla
                        Title</p>
                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $user->designation ?? 'Not specified' }}</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">System Roles & Authority
                    </p>
                    <div class="mt-1 flex flex-wrap gap-1.5">
                        @foreach ($user->roles as $role)
                            <span
                                class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Change Account Password</h3>
                <p class="text-xs text-slate-400 mt-0.5">Ensure your password is at least 8 characters long to maintain
                    security compliance.</p>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 max-w-md text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">New Password</label>
                    <input type="password" name="password" required placeholder="Min. 8 characters"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm New
                        Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Re-enter new password"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-emerald-600/20 transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
