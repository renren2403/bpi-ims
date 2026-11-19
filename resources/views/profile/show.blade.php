<!--show.blade.php profile-->
@extends('layouts.app')
@section('title', 'My Profile & Credentials')

@section('content')
    <div class="max-w-4xl space-y-7">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Account Profile & Security</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manage your personal information, profile photo, verified agency credentials, and account password.</p>
            </div>
            @if ($user->last_login_at)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Last Login: {{ $user->last_login_at->diffForHumans() }} ({{ $user->last_login_ip ?? 'N/A' }})</span>
                </div>
            @endif
        </div>

        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 space-y-1 shadow-sm">
                <p class="font-extrabold">Please correct the following:</p>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Overview Card -->
        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-b border-slate-100 pb-6">
                <div class="flex items-center gap-4">
                    @if ($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                            class="w-16 h-16 rounded-2xl object-cover shadow-md shadow-emerald-500/10 border-2 border-emerald-500/20 shrink-0">
                    @else
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-botanic-800 text-white font-extrabold text-2xl flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                            {{ $user->initials() }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-extrabold text-slate-900">{{ $user->name }}</h3>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                                {{ $user->roleLabel() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            {{ $user->designation ?? 'Bureau Personnel' }} &bull; {{ $user->email }}
                        </p>
                        @if ($user->phone_number)
                            <p class="text-xs text-emerald-800 font-semibold mt-0.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $user->phone_number }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="text-left sm:text-right text-xs">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Account Status</p>
                    <p class="font-extrabold text-emerald-700 mt-0.5 flex sm:justify-end items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Active & Verified
                    </p>
                </div>
            </div>

            <!-- Edit Personal Details Form -->
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Personal Information & Photo</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Update your contact details and display photo visible across the IPMS portal.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Designation / Position</label>
                        <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Agriculturist II, Division Chief"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Official Phone / Contact No.</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="e.g. +63 917 123 4567"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 space-y-3 text-xs">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Profile Photo</label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 file:cursor-pointer transition">
                        @if ($user->avatar)
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-rose-600 hover:text-rose-700 shrink-0">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span>Remove current photo</span>
                            </label>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-400">Accepted formats: JPEG, PNG, JPG, WEBP (Max: 2MB).</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Verified Agency Assignment (Read-Only) -->
        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Verified Agency Credentials</h3>
                <p class="text-xs text-slate-400 mt-0.5">These parameters are governed by the Planning and Management Office (PMO) Administrator and cannot be self-edited.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Official Email Address</p>
                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $user->email }}</p>
                    <p class="text-[10px] text-slate-400 mt-1">Used for authentication and system alerts.</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">Assigned Operating Unit</p>
                    <p class="font-bold text-slate-800 mt-1 text-sm">
                        {{ $user->operatingUnitLabel() }}
                    </p>
                    <p class="text-[10px] text-slate-400 mt-1">Bound reporting and accomplishment scope.</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">System Role & Permissions</p>
                    <div class="mt-1 flex flex-wrap gap-1.5">
                        @foreach ($user->roles as $role)
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5">Determines workspace routing and approval authority.</p>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Change Account Password</h3>
                <p class="text-xs text-slate-400 mt-0.5">Ensure your password is at least 8 characters long to maintain security compliance.</p>
            </div>

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
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm New Password</label>
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