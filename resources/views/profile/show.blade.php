@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Account Profile</h2>
        <p class="text-sm text-slate-500">View your current agency role and update your account password.</p>
    </div>

    <!-- Account Details -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-slate-900 border-b pb-3">User Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold">Full Name</p>
                <p class="font-bold text-slate-800 mt-1">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold">Email Address</p>
                <p class="font-bold text-slate-800 mt-1">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold">Operating Unit</p>
                <p class="font-bold text-slate-800 mt-1">{{ $user->operatingUnit?->name ?? 'Central Agency' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold">Assigned Role</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 uppercase">
                    {{ $user->roles->pluck('name')->implode(', ') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 border-b pb-3 mb-4">Change Password</h3>
        
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-rose-50 text-rose-700 text-xs border border-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 max-w-md">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">New Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow transition">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection