<!--index.blade.php Users-->
@extends('layouts.app')
@section('title', 'User Management & Approvals')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Personnel Directory & Approvals</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manage user credentials, review pending registration requests, and
                    allocate operating units.</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Personnel
            </a>
        </div>

        <!-- Pending Requests Alert Banner -->
        @if ($pendingCount > 0)
            <div
                class="p-5 bg-amber-50/80 border border-amber-200/80 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3.5">
                    <span
                        class="w-10 h-10 rounded-xl bg-amber-500 text-white font-extrabold text-sm flex items-center justify-center shadow-sm shrink-0">
                        {{ $pendingCount }}
                    </span>
                    <div>
                        <h4 class="text-xs font-extrabold text-amber-900">Pending Registration Verification Requests</h4>
                        <p class="text-xs text-amber-700 mt-0.5">There are {{ $pendingCount }} account request(s) awaiting
                            PMO review, role verification, and system authorization.</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.index', ['approval_status' => 'pending']) }}"
                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition shrink-0">
                    Review Pending Requests &rarr;
                </a>
            </div>
        @endif

        <!-- Filters Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-3.5 text-xs">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search name or email..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                </div>

                <select name="operating_unit_id"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Operating Units</option>
                    @foreach ($operatingUnits as $ou)
                        <option value="{{ $ou->id }}" {{ request('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                            {{ $ou->name }}</option>
                    @endforeach
                </select>

                <select name="role"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Roles</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                            {{ strtoupper($r->name) }}</option>
                    @endforeach
                </select>

                <select name="approval_status"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Approval States</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>⏳ Pending
                        Approval</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>✓ Approved
                    </option>
                    <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>✕ Declined
                    </option>
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-sm transition">Filter</button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl border border-slate-200 font-bold text-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- User Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 border-b border-slate-100 text-slate-400 uppercase tracking-wider font-extrabold">
                        <tr>
                            <th class="p-4">Personnel Information</th>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4">Assigned Role</th>
                            <th class="p-4">Approval State</th>
                            <th class="p-4">Login Access</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-botanic-800 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                            @if ($user->designation)
                                                <p class="text-[11px] text-emerald-800 font-semibold">
                                                    {{ $user->designation }}</p>
                                            @endif
                                            <p class="text-slate-400 text-[11px]">{{ $user->email }}</p>
                                            @if ($user->approval_remarks)
                                                <p class="text-[10px] text-slate-400 mt-0.5 italic">
                                                    {{ $user->approval_remarks }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-semibold text-slate-700">
                                    @if ($user->operatingUnit)
                                        <span>{{ $user->operatingUnit->name }}</span>
                                        <span
                                            class="text-xs text-slate-400 font-normal">({{ $user->operatingUnit->code }})</span>
                                    @elseif ($user->hasRole('higher-up'))
                                        <span class="text-dagreen-800 font-extrabold flex items-center gap-1">
                                            <span>🏛️</span>
                                            <span>Bureau-Wide Oversight</span>
                                        </span>
                                    @else
                                        <span class="text-slate-500">Central Planning Office</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200/60 uppercase">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="p-4">
                                    @if ($user->approval_status === 'pending')
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70 inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pending Review
                                        </span>
                                    @elseif ($user->approval_status === 'approved')
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70 inline-flex items-center gap-1">
                                            ✓ Approved
                                        </span>
                                    @elseif ($user->approval_status === 'rejected')
                                        <span
                                            class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200/70 inline-flex items-center gap-1">
                                            ✕ Declined
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if ($user->is_active)
                                        <span
                                            class="px-2.5 py-0.5 rounded-xl text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-xl text-[10px] font-extrabold bg-slate-100 text-slate-500">Deactivated</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                    @if ($user->approval_status === 'pending')
                                        <!-- One-click Approve -->
                                        <form method="POST" action="{{ route('admin.users.approve', $user) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-sm transition">
                                                Approve
                                            </button>
                                        </form>

                                        <!-- One-click Reject -->
                                        <form method="POST" action="{{ route('admin.users.reject', $user) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to decline this account request?');"
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-extrabold text-[11px] transition">
                                                Decline
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-block">Edit</a>

                                    @if ($user->id !== auth()->id() && $user->approval_status === 'approved')
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl {{ $user->is_active ? 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }} font-bold text-[11px] transition">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if ($user->canBeDeletedBy(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm({{ \Illuminate\Support\Js::from("Permanently delete user {$user->name}? This action cannot be undone.") }});"
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-[11px] transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">No personnel records found
                                    matching current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
