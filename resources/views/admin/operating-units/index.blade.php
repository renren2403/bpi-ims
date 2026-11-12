<!--index.blade.php Operating Units-->
@extends('layouts.app')
@section('title', 'Operating Units Registry')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Bureau Operating Units</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manage divisions, national centers, and research stations
                    responsible for BED-2 milestones.</p>
            </div>
            <a href="{{ route('admin.operating-units.create') }}"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Operating Unit
            </a>
        </div>

        <!-- Status Alerts -->
        @if (session('status'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span>✓</span>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-2xl">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Metric KPI Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Units</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalCount }}</h3>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-sm">
                    🏛️
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active in Submissions</p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $activeCount }}</h3>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-extrabold text-sm">
                    ✓
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Deactivated / Archived</p>
                    <h3 class="text-2xl font-extrabold text-slate-400 mt-0.5">{{ $inactiveCount }}</h3>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 font-extrabold text-sm">
                    ⏸
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.operating-units.index') }}"
                class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                @if ($sort)
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">
                @endif
                <div class="sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name or code (e.g. CPMD, Research)..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                </div>

                <select name="type"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                    <option value="">All Classification Types</option>
                    @foreach ($types as $t)
                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                            {{ $t }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <select name="status"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-semibold transition">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">Filter</button>
                    @if (request()->anyFilled(['search', 'type', 'status', 'sort']))
                        <a href="{{ route('admin.operating-units.index') }}"
                            class="px-3 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition flex items-center justify-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Operating Units Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead
                        class="bg-slate-50/75 border-b border-slate-200 text-slate-500 font-extrabold uppercase tracking-wider">
                        <tr>
                            @php
                                $isSortedByName = $sort === 'name';
                                $nextDirection = $isSortedByName && $direction === 'asc' ? 'desc' : 'asc';
                            @endphp
                            <th class="py-3.5 px-5">
                                <a href="{{ route('admin.operating-units.index', array_merge(request()->except('page'), ['sort' => 'name', 'direction' => $nextDirection])) }}"
                                    class="inline-flex items-center gap-1.5 hover:text-slate-900 transition group">
                                    <span>Unit Name &amp; Code</span>
                                    @if ($isSortedByName)
                                        <span class="text-emerald-600">{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                    @else
                                        <span class="text-slate-300 group-hover:text-slate-500">↕</span>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3.5 px-5">Type</th>
                            <th class="py-3.5 px-5">Station Location</th>
                            <th class="py-3.5 px-5 text-center">Personnel</th>
                            <th class="py-3.5 px-5 text-center">Status</th>
                            <th class="py-3.5 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse ($operatingUnits as $unit)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200/60">
                                            {{ $unit->code }}
                                        </span>
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">{{ $unit->name }}</p>
                                            @if ($unit->description)
                                                <p class="text-[11px] text-slate-400 line-clamp-1 max-w-md">
                                                    {{ $unit->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-5">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ $unit->type ?? 'Unit' }}
                                    </span>
                                </td>

                                <td class="py-4 px-5 text-slate-600 font-semibold">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $unit->location }}
                                    </span>
                                </td>

                                <td class="py-4 px-5 text-center">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $unit->users_count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $unit->users_count }} user(s)
                                    </span>
                                </td>

                                <td class="py-4 px-5 text-center">
                                    <form method="POST"
                                        action="{{ route('admin.operating-units.toggle-status', $unit) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Click to toggle status"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold transition {{ $unit->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $unit->is_active ? 'bg-emerald-600' : 'bg-slate-500' }}"></span>
                                            {{ $unit->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>

                                <td class="py-4 px-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.operating-units.edit', $unit) }}"
                                            class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.operating-units.destroy', $unit) }}"
                                            onsubmit="return confirm('Are you sure you want to delete {{ $unit->code }}? If it has linked records, it will be protected.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    No operating units found matching the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($operatingUnits->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $operatingUnits->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
