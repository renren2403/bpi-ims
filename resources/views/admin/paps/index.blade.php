<!--index.blade.php Programs & PAPs-->
@extends('layouts.app')
@section('title', 'Programs & Performance Indicators')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Programs, Activities & Projects (PAPs)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manage official budget program line-items and standardized performance indicators for physical monitoring.</p>
            </div>
            <a href="{{ route('admin.paps.create') }}"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Register New Program / PAP
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

        <!-- Metric Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Programs (PAPs)</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($metrics['total_paps']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-base">
                    📁
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Active Programs</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($metrics['active_paps']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-extrabold text-base">
                    ✓
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Indicators</p>
                    <p class="text-2xl font-black text-sky-600 mt-1">{{ number_format($metrics['total_indicators']) }}</p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center font-extrabold text-base">
                    🎯
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Output / Outcome Ratio</p>
                    <p class="text-base font-black text-slate-800 mt-1">
                        <span class="text-sky-600">{{ $metrics['output_indicators'] }} Output</span> &bull; 
                        <span class="text-purple-600">{{ $metrics['outcome_indicators'] }} Outcome</span>
                    </p>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center font-extrabold text-base">
                    📊
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.paps.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3.5 text-xs">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search code, title, or description..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                </div>

                <select name="category"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>

                <select name="is_active"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>✓ Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>✕ Deactivated</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-sm transition">Filter</button>
                    <a href="{{ route('admin.paps.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl border border-slate-200 font-bold text-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- Programs Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 uppercase tracking-wider font-extrabold">
                        <tr>
                            <th class="p-4">Program / PAP Code</th>
                            <th class="p-4">Program Title & Description</th>
                            <th class="p-4">Category</th>
                            <th class="p-4 text-center">Indicators</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($paps as $pap)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $pap->code }}
                                    </span>
                                </td>

                                <td class="p-4 max-w-md">
                                    <p class="font-bold text-slate-900 text-sm">{{ $pap->name }}</p>
                                    @if ($pap->description)
                                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $pap->description }}</p>
                                    @endif
                                </td>

                                <td class="p-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        {{ $pap->category ?? 'Operations' }}
                                    </span>
                                </td>

                                <td class="p-4 text-center whitespace-nowrap">
                                    <a href="{{ route('admin.paps.edit', $pap) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 text-sky-800 border border-sky-200 font-extrabold text-[11px] hover:bg-sky-100 transition"
                                        title="Click to view and manage indicators">
                                        <span>🎯</span>
                                        <span>{{ $pap->performance_indicators_count }} Indicator(s)</span>
                                    </a>
                                </td>

                                <td class="p-4 text-center whitespace-nowrap">
                                    @if ($pap->is_active)
                                        <span class="px-2.5 py-0.5 rounded-xl text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-xl text-[10px] font-extrabold bg-slate-100 text-slate-500">Deactivated</span>
                                    @endif
                                </td>

                                <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('admin.paps.edit', $pap) }}"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition inline-block">
                                        Manage & Indicators
                                    </a>

                                    <form method="POST" action="{{ route('admin.paps.toggle-status', $pap) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-xl {{ $pap->is_active ? 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }} font-bold text-[11px] transition">
                                            {{ $pap->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.paps.destroy', $pap) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Permanently remove this program and its indicators? This cannot be undone.');"
                                            class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-[11px] transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    No programs or PAPs found matching current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $paps->links() }}
            </div>
        </div>
    </div>
@endsection
