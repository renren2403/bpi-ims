@extends('layouts.app')
@section('title', 'User Management & Approvals')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">User Management & Approvals</h2>
            <p class="text-sm text-slate-500">Manage user accounts, verify access requests, and allocate operating units.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New User
        </a>
    </div>

    <!-- Pending Requests Alert Banner -->
    @if ($pendingCount > 0)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-amber-500 text-slate-900 font-extrabold text-sm flex items-center justify-center shrink-0">
                    {{ $pendingCount }}
                </span>
                <div>
                    <h4 class="text-xs font-bold text-amber-900">Pending Account Verification Requests</h4>
                    <p class="text-xs text-amber-700">There are {{ $pendingCount }} registration request(s) awaiting your review and authorization.</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['approval_status' => 'pending']) }}" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition shrink-0">
                Review Pending Requests &rarr;
            </a>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 text-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="operating_unit_id" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Operating Units</option>
                @foreach ($operatingUnits as $ou)
                    <option value="{{ $ou->id }}" {{ request('operating_unit_id') == $ou->id ? 'selected' : '' }}>{{ $ou->name }}</option>
                @endforeach
            </select>

            <select name="role" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Roles</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ strtoupper($r->name) }}</option>
                @endforeach
            </select>

            <select name="approval_status" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Approval States</option>
                <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Declined</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg transition">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg border text-center transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold">
                <tr>
                    <th class="p-4">Personnel Details</th>
                    <th class="p-4">Operating Unit</th>
                    <th class="p-4">Requested / Assigned Role</th>
                    <th class="p-4">Approval Status</th>
                    <th class="p-4">Account Status</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="p-4">
                            <p class="font-bold text-slate-900">{{ $user->name }}</p>
                            @if ($user->designation)
                                <p class="text-[11px] text-blue-800 font-medium">{{ $user->designation }}</p>
                            @endif
                            <p class="text-slate-500 text-[11px]">{{ $user->email }}</p>
                            @if ($user->approval_remarks)
                                <p class="text-[10px] text-slate-400 mt-0.5 italic">{{ $user->approval_remarks }}</p>
                            @endif
                        </td>
                        <td class="p-4 font-medium text-slate-700">
                            {{ $user->operatingUnit?->name ?? 'Central Agency' }}
                        </td>
                        <td class="p-4">
                            @foreach ($user->roles as $role)
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 uppercase">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="p-4">
                            @if ($user->approval_status === 'pending')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    ⏳ Pending Approval
                                </span>
                            @elseif ($user->approval_status === 'approved')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Approved
                                </span>
                            @elseif ($user->approval_status === 'rejected')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    ✕ Declined
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if ($user->is_active)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactive</span>
                            @endif
                        </td>
                        <td class="p-4 text-right space-x-2 whitespace-nowrap">
                            @if ($user->approval_status === 'pending')
                                <!-- One-click Approve -->
                                <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm transition">
                                        Approve
                                    </button>
                                </form>

                                <!-- One-click Reject -->
                                <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to decline this request?');" class="px-2.5 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] shadow-sm transition">
                                        Decline
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.users.edit', $user) }}" class="font-bold text-blue-600 hover:underline">Edit</a>
                            
                            @if ($user->id !== auth()->id() && $user->approval_status === 'approved')
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="font-bold {{ $user->is_active ? 'text-rose-600' : 'text-emerald-600' }} hover:underline">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">No users found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
