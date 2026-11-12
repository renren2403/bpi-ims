<!--under-construction.blade.php-->
@extends('layouts.app')
@section('title', $module['title'] . ' — Under Construction')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-400">
                    {{ $module['section'] }}
                </p>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                    {{ $module['title'] }}
                </h1>
            </div>

            <span
                class="inline-flex items-center gap-2 self-start sm:self-auto px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-extrabold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Under Construction</span>
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8 sm:p-10 space-y-7">
            <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                <div
                    class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center shrink-0 shadow-sm">
                    @switch($module['icon'])
                        @case('document')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        @break

                        @case('chart')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        @break

                        @case('lock-open')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        @break

                        @default
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                    @endswitch
                </div>

                <div class="space-y-2">
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        This Page Is Under Construction
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $module['description'] }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-200/70 p-5 space-y-3">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                    Planned Module Capabilities
                </p>
                <ul class="space-y-2.5">
                    @foreach ($module['planned_features'] as $feature)
                        <li class="flex items-start gap-2.5 text-xs font-medium text-slate-700">
                            <span
                                class="w-5 h-5 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 font-extrabold text-[11px]">
                                &bull;
                            </span>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <p class="text-xs text-slate-400 font-medium">
                    BPI-IPMS Module Preview &bull; Active development in progress
                </p>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Return to Command Center</span>
                </a>
            </div>
        </div>
    </div>
@endsection
