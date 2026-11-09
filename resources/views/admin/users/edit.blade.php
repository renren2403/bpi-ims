@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit User: {{ $user->name }}</h2>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-600 hover:underline">&larr; Back to Users</a>
    </div>

    @if ($errors->any())
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Designation / Title</label>
                <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Agriculturist" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Operating Unit</label>
                <select name="operating_unit_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Central / None --</option>
                    @foreach ($operatingUnits as $ou)
                        <option value="{{ $ou->id }}" {{ old('operating_unit_id', $user->operating_unit_id) == $ou->id ? 'selected' : '' }}>
                            {{ $ou->name }} ({{ $ou->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Role</label>
                <select name="role" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>
                            {{ strtoupper($r->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Approval Status</label>
                <select name="approval_status" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="pending" {{ old('approval_status', $user->approval_status) === 'pending' ? 'selected' : '' }}>⏳ Pending Approval</option>
                    <option value="approved" {{ old('approval_status', $user->approval_status) === 'approved' ? 'selected' : '' }}>✓ Approved</option>
                    <option value="rejected" {{ old('approval_status', $user->approval_status) === 'rejected' ? 'selected' : '' }}>✕ Declined</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                <label for="is_active" class="font-bold text-slate-700 cursor-pointer">Account is Active (Can Login)</label>
            </div>

            <div class="pt-4 border-t">
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg text-xs shadow transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Admin Password Reset Box -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-sm text-slate-900 border-b pb-2 mb-3">Reset User Password</h3>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="flex gap-3 text-xs">
            @csrf
            <input type="password" name="new_password" placeholder="Enter new password (min 8 chars)" required class="flex-1 px-3 py-2 border rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection