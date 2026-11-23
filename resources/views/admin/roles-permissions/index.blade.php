@extends('layouts.app')
@section('title', 'Dynamic Roles & Permissions Matrix')
@section('content')
    <div class="space-y-6">
        <!-- Top Header & Actions -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition">Dashboard</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-400">PMO Governance</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800">Dynamic Roles & Permissions</span>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>Dynamic Roles &amp; Permissions Matrix</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Toggle granular capabilities across bureau roles in real time, configure operational boundaries, and
                    register new actions on the fly.
                </p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-auto flex-wrap">
                <button type="button" onclick="openCreateRoleModal()"
                    class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>+ Add Role</span>
                </button>
                <button type="button" onclick="openRegisterPermissionModal()"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Register New Action</span>
                </button>
            </div>
        </div>
        @if (session('status'))
            <div
                class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div
                class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-start gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif
        <!-- Governance Stats & Guidance Banner -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Configured Roles</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $roles->count() }}</p>
                </div>
                <div
                    class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-extrabold text-base">
                    👥
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Registered Actions</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ $totalPermissions }}</p>
                </div>
                <div
                    class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-extrabold text-base">
                    ⚡
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Functional Modules</p>
                    <p class="text-2xl font-black text-sky-600 mt-1">{{ count($availableModules) }}</p>
                </div>
                <div
                    class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center font-extrabold text-base">
                    📦
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-amber-50 to-amber-100/60 p-5 rounded-2xl border border-amber-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <span
                        class="text-[10px] font-extrabold text-amber-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Superadmin Shield
                    </span>
                    <p class="text-xs text-amber-900 font-semibold mt-1 leading-snug">
                        Superadmin automatically inherits all system capabilities without limitation.
                    </p>
                </div>
            </div>
        </div>
        <!-- Filter & Search Controls -->
        <div
            class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <form method="GET" action="{{ route('admin.roles-permissions.index') }}"
                class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ $currentSearch }}"
                        placeholder="Search action code, label, or module..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select name="module" onchange="this.form.submit()"
                    class="w-full sm:w-60 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Functional Modules ({{ count($availableModules) }})</option>
                    @foreach ($availableModules as $mod)
                        <option value="{{ $mod }}" {{ $currentModule === $mod ? 'selected' : '' }}>
                            {{ $mod }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition">
                    Filter
                </button>
                @if ($currentSearch || $currentModule)
                    <a href="{{ route('admin.roles-permissions.index') }}"
                        class="text-xs font-bold text-rose-600 hover:text-rose-700 px-2 py-1 transition">
                        Clear
                    </a>
                @endif
            </form>
            <div class="text-[11px] text-slate-400 font-semibold self-end sm:self-auto flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Live Instant Synchronization Active</span>
            </div>
        </div>
        <!-- The Permissions & Roles Matrix Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                            <th class="py-4 px-6 min-w-[280px]">Operational Capability / Action</th>
                            @foreach ($roles as $role)
                                <th class="py-4 px-4 text-center min-w-[130px] border-l border-slate-100">
                                    <div class="flex flex-col items-center">
                                        <span class="text-slate-800 font-extrabold">{{ $role->display_title }}</span>
                                        <span
                                            class="text-[9px] font-semibold text-slate-400 lowercase tracking-normal">{{ $role->name }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($permissionsByModule as $moduleName => $permissions)
                            <!-- Module Group Section Header -->
                            <tr class="bg-slate-100/70 border-t-2 border-b border-slate-200/80">
                                <td colspan="{{ $roles->count() + 1 }}"
                                    class="py-2.5 px-6 font-extrabold text-slate-800 tracking-wide text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm">
                                                @if (str_contains($moduleName, 'PAP'))
                                                    📁
                                                @elseif (str_contains($moduleName, 'BED-2') || str_contains($moduleName, 'Target'))
                                                    🎯
                                                @elseif (str_contains($moduleName, 'Report'))
                                                    📊
                                                @elseif (str_contains($moduleName, 'Unlock'))
                                                    🔓
                                                @else
                                                    ⚙️
                                                @endif
                                            </span>
                                            <span>{{ $moduleName }}</span>
                                        </div>
                                        <span
                                            class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full">
                                            {{ $permissions->count() }} actions
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($permissions as $permission)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-6">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="font-extrabold text-slate-900 text-xs">{{ $permission->display_label }}</span>
                                                <span
                                                    class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $permission->name }}
                                                </span>
                                            </div>
                                            @if ($permission->description)
                                                <p class="text-[11px] text-slate-400 line-clamp-1"
                                                    title="{{ $permission->description }}">
                                                    {{ $permission->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    @foreach ($roles as $role)
                                        @php
                                            $isSuperadmin = $role->name === 'superadmin';
                                            $hasPermission = $isSuperadmin || $role->hasPermissionTo($permission);
                                        @endphp
                                        <td class="py-3.5 px-4 text-center border-l border-slate-100/80">
                                            @if ($isSuperadmin)
                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black tracking-wide"
                                                    title="Superadmin unconditionally retains all permissions.">
                                                    <span>ROOT</span>
                                                </div>
                                            @else
                                                <label
                                                    class="relative inline-flex items-center cursor-pointer select-none">
                                                    <input type="checkbox" class="sr-only peer permission-toggle"
                                                        data-role-id="{{ $role->id }}"
                                                        data-role-name="{{ $role->display_title }}"
                                                        data-permission-id="{{ $permission->id }}"
                                                        data-permission-label="{{ $permission->display_label }}"
                                                        {{ $hasPermission ? 'checked' : '' }}
                                                        onchange="handleTogglePermission(this)">
                                                    <div
                                                        class="w-10 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600">
                                                    </div>
                                                </label>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ $roles->count() + 1 }}"
                                    class="py-12 text-center text-slate-400 font-semibold text-xs">
                                    No actions found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL 1: Register New Action / Permission -->
    <div id="registerPermissionModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-extrabold text-sm">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Register New Action</h3>
                        <p class="text-[11px] text-slate-400">Define a new operational capability to include in the dynamic
                            matrix.</p>
                    </div>
                </div>
                <button type="button" onclick="closeRegisterPermissionModal()"
                    class="text-slate-400 hover:text-slate-600 transition text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.roles-permissions.permissions.store') }}"
                class="space-y-4 text-xs">
                @csrf

                <!-- Module Category -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Functional Module <span
                            class="text-rose-500">*</span></label>
                    <div class="space-y-2">
                        <select id="moduleSelect" onchange="handleModuleSelectChange(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-semibold">
                            <option value="">-- Choose Existing Module or Type New Below --</option>
                            @foreach ($availableModules as $mod)
                                <option value="{{ $mod }}">{{ $mod }}</option>
                            @endforeach
                            <option value="__NEW__">+ Custom / New Module Category</option>
                        </select>
                        <input type="text" name="module" id="customModuleInput" required
                            placeholder="e.g. Monthly Reports or Financial Analytics"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-semibold">
                    </div>
                </div>

                <!-- Action Human Label -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Action Display Label <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="label" required id="permissionLabelInput"
                        oninput="suggestCodeKey(this.value)" placeholder="e.g. Export Accomplishment Reports to PDF"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-semibold">
                </div>

                <!-- Action Code Key (Slug) -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Action Code Key <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="name" required id="permissionCodeInput"
                        placeholder="e.g. reports.export_pdf"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-mono font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">Lowercase letters, dots, underscores, or hyphens (used in
                        code: <code>@@can('...')</code>).</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="2"
                        placeholder="Brief explanation of what capability this permission grants..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-medium"></textarea>
                </div>

                <!-- Initial Pre-Assignments -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Grant Immediately To
                        Roles</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                        @foreach ($roles as $r)
                            @if ($r->name !== 'superadmin')
                                <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assign_to_roles[]" value="{{ $r->id }}"
                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>{{ $r->display_title }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeRegisterPermissionModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-sm transition">
                        Save Action &amp; Add to Matrix
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Create Custom Role -->
    <div id="createRoleModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-extrabold text-sm">
                        👥
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Create Custom Role</h3>
                        <p class="text-[11px] text-slate-400">Add a new operational role tier to the bureau directory.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateRoleModal()"
                    class="text-slate-400 hover:text-slate-600 transition text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.roles-permissions.roles.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Role Slug / Name <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. regional-auditor or data-validator"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white font-mono font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">Lowercase letters, numbers, and hyphens. Will automatically
                        appear as a column on the matrix.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeCreateRoleModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold shadow-sm transition">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Live Toast Notification -->
    <div id="liveToastNotification"
        class="fixed top-5 right-5 z-50 transform transition-all duration-300 ease-out translate-x-32 opacity-0 pointer-events-none max-w-sm">
        <div id="toastInnerContainer" class="p-4 rounded-2xl shadow-xl border flex items-center gap-3 text-xs font-bold">
            <span id="toastIcon" class="text-base shrink-0">✓</span>
            <div id="toastMessage" class="text-slate-900 leading-tight">Notification</div>
        </div>
    </div>

    <!-- Live Toggle & Modal Script -->
    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const TOGGLE_URL = '{{ route('admin.roles-permissions.toggle') }}';

        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('liveToastNotification');
            const inner = document.getElementById('toastInnerContainer');
            const msgEl = document.getElementById('toastMessage');
            const iconEl = document.getElementById('toastIcon');

            msgEl.textContent = message;

            if (isSuccess) {
                inner.className =
                    'p-4 rounded-2xl shadow-xl border border-emerald-200 bg-emerald-50 text-emerald-900 flex items-center gap-3 text-xs font-bold';
                iconEl.textContent = '✓';
                iconEl.className = 'text-emerald-600 font-black shrink-0';
            } else {
                inner.className =
                    'p-4 rounded-2xl shadow-xl border border-rose-200 bg-rose-50 text-rose-900 flex items-center gap-3 text-xs font-bold';
                iconEl.textContent = '⚠';
                iconEl.className = 'text-rose-600 font-black shrink-0';
            }

            toast.classList.remove('translate-x-32', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-x-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-32', 'opacity-0', 'pointer-events-none');
            }, 3500);
        }

        async function handleTogglePermission(checkbox) {
            const roleId = checkbox.dataset.roleId;
            const permissionId = checkbox.dataset.permissionId;
            const roleName = checkbox.dataset.roleName;
            const permissionLabel = checkbox.dataset.permissionLabel;

            checkbox.disabled = true;

            try {
                const response = await fetch(TOGGLE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        role_id: roleId,
                        permission_id: permissionId,
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    checkbox.checked = !checkbox.checked; // rollback
                    showToast(data.message || 'Unable to update permission.', false);
                } else {
                    checkbox.checked = data.attached;
                    showToast(data.message, true);
                }
            } catch (err) {
                checkbox.checked = !checkbox.checked; // rollback
                showToast('Network error while toggling permission.', false);
            } finally {
                checkbox.disabled = false;
            }
        }

        function openRegisterPermissionModal() {
            document.getElementById('registerPermissionModal').classList.remove('hidden');
        }

        function closeRegisterPermissionModal() {
            document.getElementById('registerPermissionModal').classList.add('hidden');
        }

        function openCreateRoleModal() {
            document.getElementById('createRoleModal').classList.remove('hidden');
        }

        function closeCreateRoleModal() {
            document.getElementById('createRoleModal').classList.add('hidden');
        }

        function handleModuleSelectChange(select) {
            const input = document.getElementById('customModuleInput');
            if (select.value === '__NEW__') {
                input.value = '';
                input.focus();
            } else if (select.value) {
                input.value = select.value;
            }
        }

        function suggestCodeKey(label) {
            const codeInput = document.getElementById('permissionCodeInput');
            if (!codeInput.dataset.manuallyEdited) {
                const moduleInput = document.getElementById('customModuleInput').value.trim().toLowerCase();
                let prefix = 'app.';
                if (moduleInput.includes('pap')) prefix = 'paps.';
                else if (moduleInput.includes('bed') || moduleInput.includes('target')) prefix = 'bed2.';
                else if (moduleInput.includes('report')) prefix = 'reports.';
                else if (moduleInput.includes('unlock')) prefix = 'unlock_requests.';
                else if (moduleInput.includes('user')) prefix = 'users.';

                const slug = label.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');

                codeInput.value = prefix + (slug || 'action');
            }
        }

        document.getElementById('permissionCodeInput').addEventListener('input', function() {
            this.dataset.manuallyEdited = 'true';
        });
    </script>
@endsection
