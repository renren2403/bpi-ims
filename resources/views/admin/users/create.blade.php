@extends('layouts.app')
@section('title', 'Add New User')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Add New User</h2>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-600 hover:underline">&larr; Back to Users</a>
    </div>

    @if ($errors->any())
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Designation / Title</label>
                <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Planning Officer" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Initial Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Operating Unit</label>
                <select name="operating_unit_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Central / None --</option>
                    @foreach ($operatingUnits as $ou)
                        <option value="{{ $ou->id }}" {{ old('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                            {{ $ou->name }} ({{ $ou->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Role</label>
                <select name="role" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>
                            {{ strtoupper($r->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg text-xs shadow transition">
                    Create User Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection