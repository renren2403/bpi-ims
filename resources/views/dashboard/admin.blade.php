@extends('layouts.app')
@section('title', 'Admin Command Center')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Bureau Physical Performance Command Center</h2>
        <p class="text-sm text-slate-500">Welcome, {{ auth()->user()->name }} (Superadmin / PMO Admin).</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">System Status</p>
            <h3 class="text-2xl font-extrabold text-emerald-600 mt-2">Operational</h3>
            <p class="text-xs text-slate-400 mt-1">Spatie RBAC Active</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">User Accounts</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-2">{{ \App\Models\User::count() }}</h3>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 font-bold hover:underline mt-1 inline-block">Manage Users &rarr;</a>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-500 uppercase">Operating Units</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-2">{{ \App\Models\OperatingUnit::count() }}</h3>
            <p class="text-xs text-slate-400 mt-1">Configured in Database</p>
        </div>
    </div>
</div>
@endsection