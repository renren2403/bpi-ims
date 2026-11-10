@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Modify Personnel: {{ $user->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Update user credentials, role allocation, approval state, and access permissions.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition">&larr; Back to Directory</a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Designation / Title</label>
                <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Agriculturist" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
            </div>

            @php
                $pmoUnit = $operatingUnits->firstWhere('code', 'PMO') ?? $operatingUnits->first(fn($u) => stripos($u->name, 'Planning') !== false);
                $pmoId = $pmoUnit?->id ?? '';
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Left: Role -->
                <div>
                    <label for="edit_role_select" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Role <span class="text-rose-500">*</span></label>
                    <select name="role" id="edit_role_select" onchange="handleAdminEditRoleChange()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" {{ (old('role') == $r->name || $user->hasRole($r->name)) ? 'selected' : '' }}>
                                {{ strtoupper($r->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Right: Operating Unit -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="edit_ou_select" class="block font-bold text-slate-700 uppercase tracking-wider">
                            Operating Unit
                        </label>
                        <!-- Sleek Tooltip Icon & Bubble -->
                        <div class="relative group inline-flex items-center">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-200 hover:bg-emerald-600 text-slate-600 hover:text-white transition cursor-help text-[10px] font-bold">
                                i
                            </span>
                            <div id="edit_ou_tooltip_bubble"
                                class="absolute right-0 bottom-full mb-1.5 hidden group-hover:block w-64 p-2.5 bg-slate-900 text-white text-[11px] font-medium rounded-lg shadow-xl z-30 pointer-events-none leading-snug border border-slate-700">
                                Select operating unit assignment.
                            </div>
                        </div>
                    </div>
                    <select name="operating_unit_id" id="edit_ou_select" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                        <option value="" id="edit_ou_default">-- Central / None --</option>
                        @foreach ($operatingUnits as $ou)
                            <option value="{{ $ou->id }}" {{ old('operating_unit_id', $user->operating_unit_id) == $ou->id ? 'selected' : '' }}>
                                {{ $ou->name }} ({{ $ou->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Approval Status <span class="text-rose-500">*</span></label>
                <select name="approval_status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
                    <option value="pending" {{ old('approval_status', $user->approval_status) === 'pending' ? 'selected' : '' }}>⏳ Pending Approval</option>
                    <option value="approved" {{ old('approval_status', $user->approval_status) === 'approved' ? 'selected' : '' }}>✓ Approved</option>
                    <option value="rejected" {{ old('approval_status', $user->approval_status) === 'rejected' ? 'selected' : '' }}>✕ Declined</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300" {{ $user->is_active ? 'checked' : '' }}>
                <label for="is_active" class="font-bold text-slate-700 cursor-pointer">Account is Active (Can Login to Portal)</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-md shadow-emerald-600/20 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Admin Password Reset Box -->
    <div class="bg-white p-7 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
        <div>
            <h3 class="font-extrabold text-sm text-slate-900">Administrative Password Reset</h3>
            <p class="text-xs text-slate-400">Directly set a new password for this user if they are locked out.</p>
        </div>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="flex flex-col sm:flex-row gap-3 text-xs pt-1">
            @csrf
            <input type="password" name="new_password" placeholder="Enter new password (min. 8 characters)" required 
                class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-xs font-semibold transition">
            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-sm transition shrink-0">
                Reset Password
            </button>
        </form>
    </div>
</div>

<script>
    const pmoId = "{{ $pmoId }}";

    function handleAdminEditRoleChange() {
        const role = document.getElementById('edit_role_select').value;
        const ouSelect = document.getElementById('edit_ou_select');
        const ouDefault = document.getElementById('edit_ou_default');
        const ouTooltipBubble = document.getElementById('edit_ou_tooltip_bubble');

        let tooltipText = 'Select operating unit assignment.';

        if (role === 'higher-up' || role === 'superadmin') {
            ouSelect.value = '';
            ouSelect.disabled = true;
            ouSelect.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            ouSelect.classList.remove('bg-white', 'text-slate-800');
            ouDefault.textContent = '-- Bureau-Wide Oversight (All OUs) --';
            tooltipText = '🏛️ Encompasses all operating units bureau-wide.';
        } else if (role === 'evaluator' || role === 'admin') {
            if (!ouSelect.value && pmoId) ouSelect.value = pmoId;
            ouSelect.disabled = true;
            ouSelect.classList.add('bg-emerald-50/70', 'text-emerald-900', 'cursor-not-allowed');
            ouSelect.classList.remove('bg-white', 'bg-slate-100', 'text-slate-400');
            ouDefault.textContent = '-- Planning and Management Office (PMO) --';
            tooltipText = '📊 Assigned to Planning & Management Office (PMO).';
        } else if (role === 'ou-head' || role === 'encoder') {
            ouSelect.disabled = false;
            ouSelect.classList.remove('bg-slate-100', 'bg-emerald-50/70', 'text-slate-400', 'cursor-not-allowed');
            ouSelect.classList.add('bg-white', 'text-slate-800');
            ouDefault.textContent = '-- Select Operating Unit --';
            tooltipText = 'Tied to a specific operational unit.';
        } else {
            ouSelect.disabled = false;
            ouSelect.classList.remove('bg-slate-100', 'bg-emerald-50/70', 'text-slate-400', 'cursor-not-allowed');
            ouSelect.classList.add('bg-white', 'text-slate-800');
            ouDefault.textContent = '-- Central / None --';
            tooltipText = 'Select operating unit assignment.';
        }

        if (ouTooltipBubble) {
            ouTooltipBubble.textContent = tooltipText;
        }
        ouSelect.title = tooltipText;
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleAdminEditRoleChange();
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                const role = document.getElementById('edit_role_select').value;
                const ouSelect = document.getElementById('edit_ou_select');
                if (role === 'evaluator' || role === 'admin') {
                    ouSelect.disabled = false;
                }
            });
        }
    });
</script>
@endsection
