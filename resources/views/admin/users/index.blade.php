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

        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

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
                        Accounts</option>
                    <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>✕ Declined
                        Requests</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-sm transition">Filter</button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl border border-slate-200 font-bold text-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- Standalone HTML5 Form for Bulk Actions (No nested forms) -->
        <form id="bulkActionForm" method="POST" action="{{ route('admin.users.bulk') }}">
            @csrf
            <input type="hidden" name="action" id="bulkActionInput">
            <input type="hidden" name="remarks" id="bulkRemarksInput">
        </form>

        <!-- Floating / Sticky Bulk Action Toolbar -->
        <div id="bulkToolbar"
            class="hidden bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3 border border-slate-800 transition-all">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 font-extrabold text-xs flex items-center justify-center border border-emerald-500/30"
                    id="selectedCountBadge">
                    0
                </span>
                <div>
                    <p class="text-xs font-bold text-white"><span id="selectedCountText">0</span> personnel selected</p>
                    <p class="text-[10px] text-slate-400">Perform collective administrative decisions</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="executeBulk('approve')"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Bulk Approve
                </button>

                <button type="button" onclick="executeBulk('reject')"
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs rounded-xl shadow-md shadow-rose-600/30 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Bulk Decline
                </button>

                <button type="button" onclick="clearBulkSelection()"
                    class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                    Clear
                </button>
            </div>
        </div>

        <!-- User Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/80 border-b border-slate-100 text-slate-400 uppercase tracking-wider font-extrabold">
                        <tr>
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)"
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </th>
                            <th class="p-4">Personnel Information</th>
                            <th class="p-4">Operating Unit</th>
                            <th class="p-4">Assigned Role</th>
                            <th class="p-4">Approval State</th>
                            <th class="p-4">Login Access & Activity</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4 text-center">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                        form="bulkActionForm" onchange="updateBulkToolbar()"
                                        class="user-select-check rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        @if ($user->avatarUrl())
                                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                                                class="w-9 h-9 rounded-xl object-cover shrink-0 shadow-sm border border-emerald-500/20">
                                        @else
                                            <div
                                                class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-botanic-800 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm">
                                                {{ $user->initials() }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                            @if ($user->designation)
                                                <p class="text-[11px] text-emerald-800 font-semibold">
                                                    {{ $user->designation }}</p>
                                            @endif
                                            <p class="text-slate-400 text-[11px]">{{ $user->email }}</p>
                                            @if ($user->phone_number)
                                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">
                                                    📞 {{ $user->phone_number }}
                                                </p>
                                            @endif
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
                                    <div class="space-y-1">
                                        @if ($user->is_active)
                                            <span
                                                class="px-2.5 py-0.5 rounded-xl text-[10px] font-extrabold bg-emerald-100 text-emerald-800 inline-block">Active</span>
                                        @else
                                            <span
                                                class="px-2.5 py-0.5 rounded-xl text-[10px] font-extrabold bg-slate-100 text-slate-500 inline-block">Deactivated</span>
                                        @endif

                                        @if ($user->last_login_at)
                                            <p class="text-[10px] text-slate-500 font-medium" title="IP Address: {{ $user->last_login_ip ?? 'N/A' }}">
                                                Active {{ $user->last_login_at->diffForHumans() }}
                                            </p>
                                        @else
                                            <p class="text-[10px] text-slate-400 italic">Never logged in</p>
                                        @endif
                                    </div>
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
                                <td colspan="7" class="p-8 text-center text-slate-400">No personnel records found
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

    <script>
        function updateBulkToolbar() {
            const checks = document.querySelectorAll('.user-select-check:checked');
            const total = checks.length;
            const toolbar = document.getElementById('bulkToolbar');
            const countBadge = document.getElementById('selectedCountBadge');
            const countText = document.getElementById('selectedCountText');
            const selectAll = document.getElementById('selectAllCheckbox');
            const allChecks = document.querySelectorAll('.user-select-check');

            if (total > 0) {
                toolbar.classList.remove('hidden');
                countBadge.textContent = total;
                countText.textContent = total;
            } else {
                toolbar.classList.add('hidden');
            }

            if (selectAll) {
                selectAll.checked = allChecks.length > 0 && total === allChecks.length;
            }
        }

        function toggleSelectAll(master) {
            const allChecks = document.querySelectorAll('.user-select-check');
            allChecks.forEach(cb => cb.checked = master.checked);
            updateBulkToolbar();
        }

        function clearBulkSelection() {
            const allChecks = document.querySelectorAll('.user-select-check');
            allChecks.forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
            updateBulkToolbar();
        }

        function executeBulk(action) {
            const checks = document.querySelectorAll('.user-select-check:checked');
            if (checks.length === 0) {
                alert('Please select at least one personnel record.');
                return;
            }

            const actionLabel = action === 'approve' ? 'approve and activate' : 'decline';
            let remarks = '';

            if (action === 'reject') {
                remarks = prompt('Please enter remarks/reason for declining these account requests (optional):');
                if (remarks === null) {
                    return; // Cancelled
                }
            } else {
                if (!confirm(`Are you sure you want to ${actionLabel} ${checks.length} selected account request(s)?`)) {
                    return;
                }
            }

            document.getElementById('bulkActionInput').value = action;
            document.getElementById('bulkRemarksInput').value = remarks;
            document.getElementById('bulkActionForm').submit();
        }
    </script>
@endsection