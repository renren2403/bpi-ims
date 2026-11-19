<!--index.blade.php Audit Logs-->
@extends('layouts.app')
@section('title', 'Audit & Security Trail')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Audit & Security Activity Logs</h2>
                <p class="text-xs text-slate-500 mt-0.5">Chronological immutable trail of user management decisions, authentication events, and administrative actions.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Auditing Active
                </span>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Recorded Events</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($metrics['total']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-lg">
                    📋
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Events Today</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($metrics['today']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-extrabold text-lg">
                    ⚡
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">User Logins Today</p>
                    <p class="text-2xl font-black text-sky-600 mt-1">{{ number_format($metrics['logins']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center font-extrabold text-lg">
                    🔑
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-3.5 text-xs">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search action, IP, user..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                </div>

                <select name="action"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Action Types</option>
                    @foreach ($distinctActions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                            {{ $act }}
                        </option>
                    @endforeach
                </select>

                <select name="user_id"
                    class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition font-semibold">
                    <option value="">All Actors</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" title="Date From"
                        class="w-1/2 px-2.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs font-semibold">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" title="Date To"
                        class="w-1/2 px-2.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white text-xs font-semibold">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl shadow-sm transition">Filter</button>
                    <a href="{{ route('admin.audit-logs.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl border border-slate-200 font-bold text-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- Audit Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-400 uppercase tracking-wider font-extrabold">
                        <tr>
                            <th class="p-4">Timestamp</th>
                            <th class="p-4">Actor</th>
                            <th class="p-4">Action Event</th>
                            <th class="p-4">Target Resource</th>
                            <th class="p-4">Changes & Metadata</th>
                            <th class="p-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            @php
                                $badgeColor = match (true) {
                                    str_contains($log->action, 'approved') => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                    str_contains($log->action, 'declined') || str_contains($log->action, 'rejected') => 'bg-rose-50 text-rose-800 border-rose-200',
                                    str_contains($log->action, 'created') => 'bg-sky-50 text-sky-800 border-sky-200',
                                    str_contains($log->action, 'login') => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                    str_contains($log->action, 'logout') => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4 whitespace-nowrap">
                                    <p class="font-bold text-slate-800">{{ $log->created_at->format('M d, Y h:i:s A') }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                                </td>

                                <td class="p-4">
                                    @if ($log->user)
                                        <div class="flex items-center gap-2.5">
                                            @if ($log->user->avatarUrl())
                                                <img src="{{ $log->user->avatarUrl() }}" alt="{{ $log->user->name }}"
                                                    class="w-7 h-7 rounded-lg object-cover shrink-0">
                                            @else
                                                <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0">
                                                    {{ $log->user->initials() }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $log->user->name }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $log->user->operatingUnit?->code ?? 'PMO' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">System / Anonymous</span>
                                    @endif
                                </td>

                                <td class="p-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold border uppercase tracking-wider {{ $badgeColor }}">
                                        {{ str_replace(['.', '_'], ' ', $log->action) }}
                                    </span>
                                </td>

                                <td class="p-4 font-semibold text-slate-700 whitespace-nowrap">
                                    @if ($log->auditable_type)
                                        <p class="text-slate-800 font-bold">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</p>
                                    @else
                                        <span class="text-slate-400">&mdash;</span>
                                    @endif
                                </td>

                                <td class="p-4 max-w-xs">
                                    @if (!empty($log->new_values) || !empty($log->old_values))
                                        <details class="cursor-pointer group">
                                            <summary class="text-[11px] font-bold text-emerald-800 group-hover:text-emerald-900 select-none">
                                                View Payload Details
                                            </summary>
                                            <div class="mt-1.5 p-2.5 rounded-lg bg-slate-900 text-slate-200 text-[10px] font-mono overflow-x-auto max-h-36">
                                                @if (!empty($log->old_values))
                                                    <p class="text-rose-400 font-bold mb-1">Previous:</p>
                                                    <pre class="whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                @endif
                                                @if (!empty($log->new_values))
                                                    <p class="text-emerald-400 font-bold mt-1.5 mb-1">Updated:</p>
                                                    <pre class="whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                @endif
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-slate-400 text-[11px]">&mdash;</span>
                                    @endif
                                </td>

                                <td class="p-4 whitespace-nowrap">
                                    <p class="font-mono text-[11px] text-slate-700 font-semibold">{{ $log->ip_address ?? '127.0.0.1' }}</p>
                                    @if ($log->user_agent)
                                        <p class="text-[9px] text-slate-400 truncate max-w-[140px]" title="{{ $log->user_agent }}">
                                            {{ Str::limit($log->user_agent, 25) }}
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    No audit log entries recorded matching current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
