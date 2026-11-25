<!--create.blade.php BED-2 Target Plan (Interactive Smart Sheet Editor)-->
@extends('layouts.app')
@section('title', 'BED-2 Physical Plan Smart Sheet Editor')

@section('content')
    <style>
        /* Prevent browser number input spinners from taking width and cutting off decimals on zoom */
        .sheet-number-input::-webkit-outer-spin-button,
        .sheet-number-input::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        .sheet-number-input,
        input[type="number"] {
            -moz-appearance: textfield !important;
        }

        .auto-expand-textarea {
            field-sizing: content;
            min-height: 54px;
            height: auto;
            resize: none !important;
            box-sizing: border-box;
            line-height: 1.5;
            padding: 8px 10px 18px 10px !important;
            border: 0 !important;
            outline: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;
            display: block;
            width: 100%;
        }

        .auto-expand-textarea:focus,
        .auto-expand-textarea:focus-visible,
        .auto-expand-textarea:active {
            outline: none !important;
            box-shadow: none !important;
            border: 0 !important;
            border-color: transparent !important;
            background-color: transparent !important;
            --tw-ring-color: transparent !important;
            --tw-ring-shadow: none !important;
            --tw-ring-offset-shadow: none !important;
            --tw-ring-inset: none !important;
        }

        /* High-visibility, prominent vertical and horizontal scrollbars for BED-2 Smart Sheet */
        .sheet-scroll-container::-webkit-scrollbar {
            height: 12px;
            width: 12px;
        }
        .sheet-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }
        .sheet-scroll-container::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 6px;
            border: 3px solid #f1f5f9;
        }
        .sheet-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #059669;
        }
        .sheet-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #f1f5f9;
        }
    </style>
    <div class="space-y-4">
        <!-- Top Toolbar & Status Bar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm">
            <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">BED-2 Physical Plan
                            Worksheet</h2>
                        <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                            FY {{ $year }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openNewCategoryPapModal()"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition flex items-center gap-1.5 shadow-sm shadow-emerald-600/20">
                        <span class="text-white font-extrabold">+</span>
                        <span>Add Program / PAP</span>
                    </button>
                    <a href="{{ route('bed2-plans.index', ['year' => $year]) }}"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-1.5">
                        <span>&larr;</span>
                        <span>Back to Matrix</span>
                    </a>
                </div>
            </div>

            <!-- Jurisdiction Selector Bar -->
            <div
                class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Operating Unit:</span>
                        @if (auth()->user()->can('bed2.manage_all_units') ||
                                auth()->user()->hasAnyRole(['superadmin', 'admin', 'evaluator']))
                            <select id="ouSelector" onchange="changeOuOrYear()"
                                class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 font-bold text-slate-800 text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @foreach ($operatingUnits as $ou)
                                    <option value="{{ $ou->id }}" {{ $defaultOuId == $ou->id ? 'selected' : '' }}>
                                        {{ $ou->name }} ({{ $ou->code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <span
                                class="font-extrabold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                {{ auth()->user()->operatingUnit?->name ?? 'Assigned Unit' }}
                                ({{ auth()->user()->operatingUnit?->code ?? 'N/A' }})
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Fiscal Year:</span>
                        <input type="number" id="yearSelector" value="{{ $year }}" min="2020" max="2050"
                            onchange="changeOuOrYear()"
                            class="w-20 px-2 py-1 rounded-lg border border-slate-200 bg-slate-50 font-bold text-slate-800 text-xs text-center focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Live Matrix Summary Status -->
                <div class="flex items-center gap-2">
                    <span id="matrixSummaryBadge"
                        class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-[11px]">
                        0 Total Target Plans
                    </span>
                    <button type="button" onclick="document.getElementById('smartSheetForm').requestSubmit()"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                        <span>💾</span>
                        <span>Save BED-2 Plan</span>
                    </button>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div
                class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl space-y-1 shadow-sm">
                <p class="font-extrabold">Please review the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- The Smart Spreadsheet Form -->
        <form method="POST" action="{{ route('bed2-plans.store') }}" id="smartSheetForm" class="space-y-4">
            @csrf
            <input type="hidden" name="operating_unit_id" value="{{ $defaultOuId }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <!-- Table Container with Horizontal and Vertical Scrollbars -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div id="tableScrollContainer" class="sheet-scroll-container overflow-x-auto overflow-y-auto max-h-[75vh]">
                    <table class="w-full text-left text-xs border-collapse" id="smartSheetTable">
                        <!-- Clean Executive Sticky Table Header -->
                        <thead
                            class="sticky top-0 z-30 bg-slate-50 text-slate-600 text-[10px] font-extrabold uppercase tracking-wider border-b border-slate-200 shadow-[0_1px_3px_rgba(15,23,42,0.06)] select-none">
                            <tr>
                                <th rowspan="2"
                                    class="p-3 border-r border-slate-200 min-w-[340px] max-w-[440px] w-[380px] text-left bg-slate-50 text-slate-600">
                                    PROGRAM / PROJECTS / ACTIVITIES
                                </th>
                                <th rowspan="2"
                                    class="p-3 border-r border-slate-200 min-w-[320px] max-w-[420px] w-[360px] text-left bg-slate-50 text-slate-600">
                                    KEY INDICATORS
                                </th>
                                <th rowspan="2"
                                    class="p-2 border-r border-slate-200 min-w-[70px] w-[70px] text-center bg-slate-50 text-slate-600">
                                    Unit
                                </th>
                                <th rowspan="2"
                                    class="p-2 border-r border-amber-200/80 min-w-[100px] w-[100px] text-center bg-amber-50/90 text-amber-900 font-black">
                                    Annual Target
                                </th>
                                <th colspan="4"
                                    class="p-1.5 border-r border-slate-200 text-center bg-slate-100/80 font-extrabold text-slate-700">
                                    1st Quarter
                                </th>
                                <th colspan="4"
                                    class="p-1.5 border-r border-slate-200 text-center bg-slate-100/80 font-extrabold text-slate-700">
                                    2nd Quarter
                                </th>
                                <th colspan="4"
                                    class="p-1.5 border-r border-slate-200 text-center bg-slate-100/80 font-extrabold text-slate-700">
                                    3rd Quarter
                                </th>
                                <th colspan="4"
                                    class="p-1.5 border-r border-slate-200 text-center bg-slate-100/80 font-extrabold text-slate-700">
                                    4th Quarter
                                </th>
                                <th rowspan="2"
                                    class="p-2 border-r border-slate-200 min-w-[105px] w-[105px] text-center bg-slate-50 text-slate-600">
                                    Balance
                                </th>
                                <th rowspan="2" class="p-2 text-center min-w-[120px] w-[120px] bg-slate-50 text-slate-600">
                                    ⚡ Presets
                                </th>
                            </tr>
                            <tr class="text-[9px] font-bold text-slate-500 bg-slate-100/60 border-t border-slate-200">
                                <!-- Q1 -->
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Jan</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Feb</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Mar</th>
                                <th
                                    class="p-1 border-r border-slate-200 min-w-[74px] w-[74px] text-center bg-emerald-50/80 font-black text-emerald-800">
                                    Q1</th>
                                <!-- Q2 -->
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Apr</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">May</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Jun</th>
                                <th
                                    class="p-1 border-r border-slate-200 min-w-[74px] w-[74px] text-center bg-emerald-50/80 font-black text-emerald-800">
                                    Q2</th>
                                <!-- Q3 -->
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Jul</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Aug</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Sep</th>
                                <th
                                    class="p-1 border-r border-slate-200 min-w-[74px] w-[74px] text-center bg-emerald-50/80 font-black text-emerald-800">
                                    Q3</th>
                                <!-- Q4 -->
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Oct</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Nov</th>
                                <th class="p-1 border-r border-slate-200 min-w-[68px] w-[68px] text-center">Dec</th>
                                <th
                                    class="p-1 border-r border-slate-200 min-w-[74px] w-[74px] text-center bg-emerald-50/80 font-black text-emerald-800">
                                    Q4</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white" id="sheetTbody">
                            @php
                                $rowIndex = 0;
                                $savedPapIds = $existingPlans->pluck('pap_id')->unique()->all();
                                $papsWithPlans = $paps->whereIn('id', $savedPapIds);
                                $papsByCategory = $papsWithPlans->groupBy(function ($pap) {
                                    return !empty(trim($pap->category ?? ''))
                                        ? strtoupper(trim($pap->category))
                                        : 'OPERATIONS';
                                });
                            @endphp

                            {{-- EMPTY BLANK OPENING PROMPT --}}
                            @if ($papsByCategory->isEmpty())
                                <tr id="emptySheetPromptRow">
                                    <td colspan="22" class="p-12 text-center bg-slate-50/50">
                                        <div class="max-w-md mx-auto space-y-4">
                                            <div
                                                class="w-14 h-14 mx-auto rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-3xl shadow-sm border border-emerald-200">
                                                📋
                                            </div>
                                            <div>
                                                <h3 class="text-base font-black text-slate-900 tracking-tight">Your FY
                                                    {{ $year }} Physical Plan is Blank</h3>
                                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                                    No target plans have been added yet for
                                                    {{ $operatingUnits->firstWhere('id', $defaultOuId)?->name ?? 'your unit' }}.
                                                    Click below to add your division's Program (PAP) and start encoding.
                                                </p>
                                            </div>
                                            <button type="button" onclick="openNewCategoryPapModal()"
                                                class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-lg shadow-emerald-600/25 transition inline-flex items-center gap-2">
                                                <span class="text-lg font-black leading-none">+</span>
                                                <span>Add Program / PAP to Start</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                {{-- Render ONLY the plans this OU has saved --}}
                                @foreach ($papsByCategory as $categoryTitle => $groupedPaps)
                                    <tr class="category-banner-row bg-slate-100/90 text-slate-800 font-extrabold text-xs uppercase tracking-wider border-y border-slate-200 select-none"
                                        data-category="{{ $categoryTitle }}">
                                        <!-- Col 1: Category Title -->
                                        <td
                                            class="p-2.5 px-4 border-r border-slate-200 border-l-4 border-l-emerald-600 bg-slate-100/90 text-slate-900 font-extrabold text-xs uppercase tracking-wider">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                                <span>{{ $categoryTitle }}</span>
                                            </div>
                                        </td>
                                        <!-- Col 2: Action Button -->
                                        <td class="p-2 border-r border-slate-200 bg-slate-100/90">
                                            <button type="button"
                                                onclick="openNewCategoryPapModal('{{ addslashes($categoryTitle) }}')"
                                                class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 border border-emerald-200 text-[10px] text-emerald-800 font-bold shadow-sm transition flex items-center gap-1">
                                                <span class="text-emerald-600 font-extrabold">+</span>
                                                <span>Add Program under {{ $categoryTitle }}</span>
                                            </button>
                                        </td>
                                        <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                                        <td class="bg-slate-100/70"></td>
                                    </tr>

                                    @foreach ($groupedPaps as $pap)
                                        <tr class="pap-banner-row bg-emerald-50/90 text-emerald-950 font-extrabold text-xs uppercase tracking-wide border-t border-b border-emerald-200 select-none"
                                            data-pap-id="{{ $pap->id }}" data-pap-code="{{ $pap->code }}"
                                            data-pap-name="{{ $pap->name }}">
                                            <!-- Col 1: PAP Badge & Name -->
                                            <td
                                                class="p-2 px-3 border-r border-emerald-200 bg-emerald-50/90 text-emerald-950 font-extrabold text-xs uppercase tracking-wide">
                                                <div class="flex items-start gap-2">
                                                    <span
                                                        class="font-mono bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded border border-emerald-300 font-black text-[11px] shrink-0 mt-0.5">
                                                        {{ $pap->code }}
                                                    </span>
                                                    <span class="font-extrabold text-emerald-950 text-xs leading-snug whitespace-normal break-words" title="{{ $pap->name }}">{{ $pap->name }}</span>
                                                </div>
                                            </td>
                                            <!-- Col 2: Action Buttons -->
                                            <td class="p-2 border-r border-emerald-200 bg-emerald-50/90">
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button"
                                                        onclick="addRowUnderPap({{ $pap->id }}, '{{ addslashes($pap->code) }}', '{{ addslashes($pap->name) }}')"
                                                        class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] transition flex items-center gap-1 shadow-sm">
                                                        + Add Indicator Row
                                                    </button>
                                                    <button type="button"
                                                        onclick="openAddSubProgramModal({{ $pap->id }}, '{{ addslashes($pap->code) }}', '{{ addslashes($pap->name) }}')"
                                                        class="px-2 py-0.5 rounded bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 font-extrabold text-[10px] transition">
                                                        + Sub-Program
                                                    </button>
                                                    <button type="button"
                                                        onclick="openAddActivityModal({{ $pap->id }}, '{{ addslashes($pap->code) }}', '{{ addslashes($pap->name) }}')"
                                                        class="px-2 py-0.5 rounded bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 font-extrabold text-[10px] transition">
                                                        + Activity
                                                    </button>
                                                </div>
                                            </td>
                                            <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                                            <td class="border-r border-emerald-200 bg-emerald-50/50"></td>
                                            <td class="border-r border-emerald-300 bg-amber-50/30"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                                            <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                                            <td class="border-r border-emerald-200 bg-emerald-50/50"></td>
                                            <td class="bg-emerald-50/50"></td>
                                        </tr>

                                        @php
                                            $lastSubProgram = null;
                                            $lastActivity = null;
                                        @endphp

                                        @foreach ($pap->performanceIndicators as $indicator)
                                            @php
                                                $plan = $existingPlans[$indicator->id] ?? null;
                                                if (!$plan) {
                                                    continue;
                                                }
                                                $annualTarget = (float) $plan->annual_target;
                                                $subProg = trim($indicator->sub_program ?? '');
                                                $act = trim($indicator->activity ?? '');
                                                $subAct = trim($indicator->sub_activity ?? '');
                                            @endphp

                                            @if (!empty($subProg) && $subProg !== $lastSubProgram)
                                                @php
                                                    $lastSubProgram = $subProg;
                                                    $lastActivity = null;
                                                @endphp
                                                <tr class="subprogram-row bg-slate-100 font-black text-slate-800 text-[11px] border-b border-slate-200"
                                                    data-pap-id="{{ $pap->id }}"
                                                    data-subprogram="{{ $subProg }}">
                                                    <!-- Col 1: Sub-Program Title -->
                                                    <td class="py-2 px-3 border-r border-slate-200 bg-slate-100">
                                                        <span class="italic font-bold text-slate-800 whitespace-normal break-words leading-snug block"
                                                            title="{{ $subProg }}">{{ $subProg }}</span>
                                                    </td>
                                                    <!-- Col 2: Action Button -->
                                                    <td class="py-2 px-2 border-r border-slate-200 bg-slate-100">
                                                        <div class="flex items-center justify-between gap-1">
                                                            <div class="flex items-center gap-1.5">
                                                                <button type="button"
                                                                    onclick="addRowUnderPap({{ $pap->id }}, '{{ addslashes($pap->code) }}', '{{ addslashes($pap->name) }}', '{{ addslashes($subProg) }}')"
                                                                    class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[9px] transition flex items-center gap-1 shadow-sm">
                                                                    + Add Indicator Row
                                                                </button>
                                                                <button type="button"
                                                                    onclick="openAddActivityModal({{ $pap->id }}, '{{ addslashes($pap->code) }}', '{{ addslashes($pap->name) }}', '{{ addslashes($subProg) }}')"
                                                                    class="text-[9px] font-bold text-slate-600 hover:text-emerald-700 px-2 py-0.5 rounded hover:bg-slate-200 transition">
                                                                    + Activity
                                                                </button>
                                                            </div>
                                                            <button type="button"
                                                                onclick="removeHeaderRow(this, 'Sub-Program')"
                                                                class="text-slate-400 hover:text-rose-600 font-bold text-sm px-1 leading-none"
                                                                title="Remove Sub-Program Header">&times;</button>
                                                        </div>
                                                    </td>
                                                    <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                                                    <td class="border-r border-slate-200 bg-slate-50"></td>
                                                    <td class="border-r border-slate-300 bg-amber-50/20"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-200 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-200 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-200 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-100 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-200 bg-slate-50/50"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-200 bg-slate-50"></td>
                                                    <td class="bg-slate-50"></td>
                                                </tr>
                                            @endif

                                            @if (!empty($act) && $act !== $lastActivity)
                                                @php $lastActivity = $act; @endphp
                                                <tr class="activity-row bg-slate-50 font-extrabold text-slate-700 text-[11px] border-b border-slate-200"
                                                    data-pap-id="{{ $pap->id }}"
                                                    data-subprogram="{{ $subProg }}"
                                                    data-activity="{{ $act }}">
                                                    <!-- Col 1: Activity Title -->
                                                    <td class="py-2 px-5 border-r border-slate-200 bg-slate-50">
                                                        <span class="font-extrabold text-emerald-950 whitespace-normal break-words leading-snug block"
                                                            title="{{ $act }}">&bull;
                                                            {{ $act }}</span>
                                                    </td>
                                                    <!-- Col 2: Action Button -->
                                                    <td class="py-2 px-2 border-r border-slate-200 bg-slate-50">
                                                        <div class="flex items-center justify-between gap-1">
                                                            <button type="button"
                                                                onclick="addRowUnderPap({{ $pap->id }}, '{{ addslashes($pap->code) }}', '{{ addslashes($pap->name) }}', '{{ addslashes($subProg) }}', '{{ addslashes($act) }}')"
                                                                class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[9px] transition flex items-center gap-1 shadow-sm">
                                                                + Add Indicator Row
                                                            </button>
                                                            <button type="button"
                                                                onclick="removeHeaderRow(this, 'Activity')"
                                                                class="text-slate-400 hover:text-rose-600 font-bold text-base px-1 leading-none transition"
                                                                title="Remove Activity Header">&times;</button>
                                                        </div>
                                                    </td>
                                                    <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                                                    <td class="border-r border-slate-200 bg-white"></td>
                                                    <td class="border-r border-slate-300 bg-amber-50/20"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-200 bg-white"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-200 bg-white"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-200 bg-white"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-100 bg-white"></td>
                                                    <td class="border-r border-slate-200 bg-white"></td>
                                                    <td class="border-r border-slate-300 bg-slate-100"></td>
                                                    <td class="border-r border-slate-200 bg-white"></td>
                                                    <td class="bg-white"></td>
                                                </tr>
                                            @endif

                                            <tr class="data-row hover:bg-slate-50/80 transition"
                                                id="row_{{ $rowIndex }}" data-index="{{ $rowIndex }}"
                                                data-plan-id="{{ $plan->id }}" data-pap-id="{{ $pap->id }}"
                                                data-subprogram="{{ $subProg }}"
                                                data-activity="{{ $act }}">
                                                <input type="hidden" name="plans[{{ $rowIndex }}][pap_id]"
                                                    value="{{ $pap->id }}">
                                                <input type="hidden"
                                                    name="plans[{{ $rowIndex }}][performance_indicator_id]"
                                                    value="{{ $indicator->id }}">
                                                <input type="hidden" name="plans[{{ $rowIndex }}][sub_program]"
                                                    value="{{ $subProg }}">
                                                <input type="hidden" name="plans[{{ $rowIndex }}][activity]"
                                                    value="{{ $act }}">

                                                <!-- Col 1: Specific Activity / Breakdown -->
                                                <td class="p-0 border-r border-slate-200 align-top min-w-[340px] max-w-[440px] w-[380px]">
                                                    <textarea name="plans[{{ $rowIndex }}][sub_activity]"
                                                        placeholder="1. Specific activity or task breakdown..."
                                                        oninput="autoResizeTextarea(this)"
                                                        class="auto-expand-textarea w-full p-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 rounded-none focus:outline-none focus:ring-0 leading-relaxed block">{{ $subAct ?: $indicator->name }}</textarea>
                                                </td>

                                                <!-- Col 2: Key Indicator (KPI) -->
                                                <td class="p-0 border-r border-slate-200 align-top min-w-[320px] max-w-[420px] w-[360px]">
                                                    <textarea name="plans[{{ $rowIndex }}][indicator_name]"
                                                        placeholder="Key Performance Indicator target text..."
                                                        oninput="autoResizeTextarea(this)"
                                                        class="auto-expand-textarea w-full p-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 rounded-none focus:outline-none focus:ring-0 leading-relaxed block">{{ $indicator->name }}</textarea>
                                                </td>

                                                <!-- Col 3: Unit -->
                                                <td
                                                    class="p-2 border-r border-slate-200 align-middle text-center bg-white min-w-[70px] w-[70px]">
                                                    <input type="text"
                                                        name="plans[{{ $rowIndex }}][unit_of_measure]"
                                                        value="{{ $indicator->unit_of_measure }}"
                                                        class="w-16 px-1.5 py-1 text-center font-bold text-slate-700 border border-slate-200 rounded-lg text-xs bg-slate-50/50 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                                                </td>

                                                <!-- Col 4: Annual Target -->
                                                <td
                                                    class="p-2 border-r border-slate-300 align-middle bg-amber-50/40 text-center min-w-[100px] w-[100px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][annual_target]"
                                                        id="annual_{{ $rowIndex }}" value="{{ $annualTarget }}"
                                                        required oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-24 min-w-[90px] px-2 py-1 text-right font-mono font-black text-slate-900 border border-amber-300 rounded-lg text-xs bg-white focus:ring-2 focus:ring-emerald-500 shadow-sm">
                                                </td>

                                                <!-- Q1 Months -->
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m1_target]"
                                                        id="m1_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m1_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m2_target]"
                                                        id="m2_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m2_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m3_target]"
                                                        id="m3_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m3_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                                                    <span id="q1_badge_{{ $rowIndex }}"
                                                        class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                                                </td>

                                                <!-- Q2 Months -->
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m4_target]"
                                                        id="m4_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m4_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m5_target]"
                                                        id="m5_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m5_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m6_target]"
                                                        id="m6_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m6_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                                                    <span id="q2_badge_{{ $rowIndex }}"
                                                        class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                                                </td>

                                                <!-- Q3 Months -->
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m7_target]"
                                                        id="m7_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m7_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m8_target]"
                                                        id="m8_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m8_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m9_target]"
                                                        id="m9_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m9_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                                                    <span id="q3_badge_{{ $rowIndex }}"
                                                        class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                                                </td>

                                                <!-- Q4 Months -->
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m10_target]"
                                                        id="m10_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m10_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m11_target]"
                                                        id="m11_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m11_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                                                    <input type="number" step="0.01" min="0"
                                                        name="plans[{{ $rowIndex }}][m12_target]"
                                                        id="m12_{{ $rowIndex }}"
                                                        value="{{ (float) $plan->m12_target }}"
                                                        oninput="recalcRow({{ $rowIndex }})"
                                                        class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                                                </td>
                                                <td
                                                    class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                                                    <span id="q4_badge_{{ $rowIndex }}"
                                                        class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                                                </td>

                                                <!-- Balance Status -->
                                                <td
                                                    class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[105px] w-[105px]">
                                                    <span id="balance_badge_{{ $rowIndex }}"
                                                        class="text-[10px] font-black px-1.5 py-1 rounded bg-slate-100 text-slate-600 inline-block w-full text-center">
                                                        0 / 0
                                                    </span>
                                                </td>

                                                <!-- Presets & Actions -->
                                                <td class="p-1 text-center align-middle bg-white min-w-[120px] w-[120px]">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button"
                                                            onclick="distributeEvenly({{ $rowIndex }})"
                                                            class="px-1.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded border border-emerald-200"
                                                            title="Distribute Evenly">
                                                            ⚡Even
                                                        </button>
                                                        <button type="button"
                                                            onclick="distributeQuarterly({{ $rowIndex }})"
                                                            class="px-1.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-extrabold text-[10px] rounded border border-indigo-200"
                                                            title="Distribute Quarterly">
                                                            ⚡Qtr
                                                        </button>
                                                        <button type="button" onclick="removeRow({{ $rowIndex }})"
                                                            class="p-1 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 rounded border border-rose-200 transition"
                                                            title="Remove this row">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                @php $rowIndex++; @endphp
                @endforeach
                @endforeach
                @endforeach
                @endif
                </tbody>
                </table>
            </div>
    </div>

    <!-- Sticky Bottom Floating Action Bar -->
    <div
        class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md text-slate-800 p-4 rounded-2xl shadow-lg border border-slate-200/90 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                📊
            </div>
            <div>
                <p class="font-extrabold text-sm text-slate-900">
                    <span id="encodedCountDisplay" class="text-emerald-700">{{ $rowIndex }}</span> Physical Target Rows Configured
                </p>
                <p class="text-[11px] text-slate-500">
                    Save changes anytime to store your targets as official baseline drafts.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="openNewCategoryPapModal()"
                class="px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm">
                <span class="text-emerald-600 font-extrabold text-sm leading-none">+</span>
                <span>Add Program / Activity</span>
            </button>
            <button type="button" onclick="document.getElementById('smartSheetForm').requestSubmit()"
                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                <span>💾</span>
                <span>Save BED-2 Plan</span>
            </button>
        </div>
    </div>
    </form>
    </div>

    <!-- Modal 1: Add Program (PAP) to Sheet -->
    <div id="newCategoryPapModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-xs my-8"
            onclick="event.stopPropagation()">
            <div class="bg-slate-50/90 border-b border-slate-100 p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-extrabold text-base shrink-0">
                        +
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">
                            Add Program (PAP) to Sheet
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Select an existing bureau program or create a new Category Banner &amp; Program.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeNewCategoryPapModal()"
                    class="text-slate-400 hover:text-slate-600 font-bold text-lg p-1 transition">&times;</button>
            </div>

            <form id="quickStructureForm" onsubmit="submitNewStructure(event)" class="p-6 space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block font-black text-slate-700 uppercase tracking-wider text-xs">
                            1. Program / PAP <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] text-slate-400 font-semibold">Type to jump / lock</span>
                    </div>

                    <div class="relative mb-2">
                        <input type="text" id="modalPapSearchInput"
                            placeholder="Type any word or code to lock (e.g. Crop, Seed, Plant, PAP-01)..."
                            oninput="filterPapSelect(this.value)"
                            onkeydown="handlePapSearchKeydown(event)"
                            autocomplete="off"
                            class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400 shadow-sm transition">
                        <span class="absolute left-2.5 top-2.5 text-slate-400 text-xs">🔍</span>
                    </div>

                    <select id="modalPapSelect" onchange="onPapSelectChange(this)" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-bold text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-400">
                        <option value="">-- Select a Bureau Program or Create New --</option>
                        <optgroup label="✨ Bureau Program Catalog">
                            @foreach ($paps as $p)
                                <option value="{{ $p->id }}" data-category="{{ $p->category ?? 'Operations' }}"
                                    data-code="{{ $p->code }}" data-name="{{ $p->name }}">
                                    {{ $p->code }} &mdash; {{ $p->name }}
                                    ({{ $p->category ?? 'Operations' }})
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="➕ Custom">
                            <option value="new">+ Create Brand New Category Banner &amp; Program...</option>
                        </optgroup>
                    </select>
                </div>

                <div id="customPapFields" class="hidden space-y-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <label class="block font-black text-slate-700 uppercase tracking-wider mb-1 text-[11px]">
                            Category Banner <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" list="category_options" id="modalCategoryInput" name="category"
                            placeholder="e.g. III. OPERATIONS or type custom category..."
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-bold text-xs focus:outline-none focus:ring-2 focus:ring-slate-400">
                        <p class="text-[10px] text-slate-400 mt-0.5">Top-level Roman numeral banner across BED-2 (e.g. III.
                            OPERATIONS, IV. LOCALLY-FUNDED PROJECTS).</p>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block font-black text-slate-700 uppercase tracking-wider mb-1 text-[11px]">
                                Code <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="modalPapCode" name="code" placeholder="e.g. AFRSP"
                                class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-mono font-bold text-xs uppercase focus:outline-none focus:ring-2 focus:ring-slate-400">
                        </div>
                        <div class="col-span-2">
                            <label class="block font-black text-slate-700 uppercase tracking-wider mb-1 text-[11px]">
                                Program Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="modalPapName" name="name"
                                placeholder="e.g. Agriculture and Fishery Regulatory Support Program"
                                class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-bold text-xs focus:outline-none focus:ring-2 focus:ring-slate-400">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-black text-slate-700 uppercase tracking-wider mb-1">
                        2. Sub-Program / Component <span class="text-slate-400 font-normal">(Optional Header Row)</span>
                    </label>
                    <input type="text" id="modalSubProgram" name="sub_program"
                        placeholder="e.g. Quality control and inspection and issuance of regulatory documents"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-400 font-semibold text-xs">
                </div>

                <div>
                    <label class="block font-black text-slate-700 uppercase tracking-wider mb-1">
                        3. Activity / Service <span class="text-slate-400 font-normal">(Optional Header Row)</span>
                    </label>
                    <input type="text" id="modalActivity" name="activity"
                        placeholder="e.g. Issuance of Biosafety Permit"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-400 font-semibold text-xs">
                </div>

                <!-- Toggle to include target row or create header only -->
                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="modalIncludeIndicatorChk" checked
                            onchange="toggleModalIndicatorSection(this.checked)"
                            class="rounded border-slate-300 text-slate-700 focus:ring-slate-400">
                        <span class="font-extrabold text-slate-800 text-xs">Include initial target row now</span>
                    </label>
                    <p class="text-[10px] text-slate-400 ml-6">Uncheck if you only want to set up the Program/Activity
                        headers with no targets yet.</p>
                </div>

                <div id="modalIndicatorSection"
                    class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-0.5">
                            Col 1: Specific Activity / Task Process Step
                        </label>
                        <input type="text" id="modalSubActivity" name="indicator_sub_activity"
                            placeholder="e.g. 1. Receive and conduct of initial technical assessment..."
                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 mb-0.5">
                            Col 2: Key Indicator (KPI)
                        </label>
                        <input type="text" id="modalIndicatorName" name="indicator_name"
                            placeholder="e.g. 6 GM applications assessed for sufficiency in substance..."
                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 mb-0.5">Unit of Measure</label>
                            <input type="text" id="modalIndicatorUnit" name="indicator_unit"
                                placeholder="e.g. Applications" value="Units"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 mb-0.5">Annual Target</label>
                            <input type="number" step="0.01" min="0" id="modalAnnualTarget" value="0"
                                class="w-full px-2.5 py-1.5 rounded-lg border border-amber-300 bg-white text-xs font-black text-slate-900 text-right">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeNewCategoryPapModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmitModal"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5">
                        <span>Insert into Sheet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Add Sub-Program Header Row (No Targets) -->
    <div id="addSubProgramModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-xs"
            onclick="event.stopPropagation()">
            <div class="bg-slate-50/90 border-b border-slate-100 p-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-extrabold text-sm">+</span>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Add Sub-Program Header Row</h3>
                </div>
                <button type="button" onclick="closeAddSubProgramModal()"
                    class="text-slate-400 hover:text-slate-600 font-bold text-base transition">&times;</button>
            </div>
            <form onsubmit="submitSubProgramOnly(event)" class="p-5 space-y-4">
                <input type="hidden" id="spModalPapId">
                <input type="hidden" id="spModalPapCode">
                <input type="hidden" id="spModalPapName">
                <div>
                    <label class="block font-black text-slate-700 uppercase tracking-wider mb-1">Sub-Program / Component
                        Title</label>
                    <input type="text" id="spModalTitle" required
                        placeholder="e.g. Quality control and inspection and issuance of regulatory documents"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400">
                    <p class="text-[10px] text-slate-400 mt-1">This will insert a structural sub-header row across the
                        table with no targets.</p>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddSubProgramModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-sm transition">Insert
                        Sub-Program Row</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Add Activity Header Row (No Targets) -->
    <div id="addActivityModal"
        class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-xs"
            onclick="event.stopPropagation()">
            <div class="bg-slate-50/90 border-b border-slate-100 p-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-extrabold text-sm">+</span>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Add Activity Header Row</h3>
                </div>
                <button type="button" onclick="closeAddActivityModal()"
                    class="text-slate-400 hover:text-slate-600 font-bold text-base transition">&times;</button>
            </div>
            <form onsubmit="submitActivityOnly(event)" class="p-5 space-y-4">
                <input type="hidden" id="actModalPapId">
                <input type="hidden" id="actModalPapCode">
                <input type="hidden" id="actModalPapName">
                <input type="hidden" id="actModalSubProgram">
                <div>
                    <label class="block font-black text-slate-700 uppercase tracking-wider mb-1">Operational Activity /
                        Service Title</label>
                    <input type="text" id="actModalTitle" required placeholder="e.g. Issuance of Biosafety Permit"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-slate-400">
                    <p class="text-[10px] text-slate-400 mt-1">This will insert an Activity row with a direct "+ Add
                        Indicator Row" button.</p>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeAddActivityModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-sm transition">Insert
                        Activity Row</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Datalist for Category Autocomplete -->
    <datalist id="category_options">
        @foreach ($categories as $cat)
            <option value="{{ $cat }}"></option>
        @endforeach
    </datalist>

    <!-- Client-Side JavaScript for Smart Sheet Calculations & Live Row Management -->
    <script>
        let totalRows = {{ $rowIndex }};

        document.addEventListener('DOMContentLoaded', function() {
            for (let i = 0; i < totalRows; i++) {
                recalcRow(i);
            }
            updateSummaryCounters();
        });

        function changeOuOrYear() {
            const ouId = document.getElementById('ouSelector') ? document.getElementById('ouSelector').value :
                '{{ $defaultOuId }}';
            const year = document.getElementById('yearSelector').value;
            window.location.href = "{{ route('bed2-plans.create') }}?operating_unit_id=" + ouId + "&year=" + year;
        }

        function toggleModalIndicatorSection(isChecked) {
            const sec = document.getElementById('modalIndicatorSection');
            const indNameInput = document.getElementById('modalIndicatorName');
            if (isChecked) {
                sec.classList.remove('hidden');
                indNameInput.required = true;
            } else {
                sec.classList.add('hidden');
                indNameInput.required = false;
            }
        }

        function onPapSelectChange(selectEl) {
            const customDiv = document.getElementById('customPapFields');
            const catInput = document.getElementById('modalCategoryInput');
            const codeInput = document.getElementById('modalPapCode');
            const nameInput = document.getElementById('modalPapName');

            if (selectEl.value === 'new') {
                customDiv.classList.remove('hidden');
                catInput.required = true;
                codeInput.required = true;
                nameInput.required = true;
                catInput.value = '';
                codeInput.value = '';
                nameInput.value = '';
            } else if (selectEl.value !== '') {
                customDiv.classList.add('hidden');
                catInput.required = false;
                codeInput.required = false;
                nameInput.required = false;

                const opt = selectEl.options[selectEl.selectedIndex];
                catInput.value = opt.getAttribute('data-category') || 'Operations';
                codeInput.value = opt.getAttribute('data-code') || '';
                nameInput.value = opt.getAttribute('data-name') || '';
            } else {
                customDiv.classList.add('hidden');
            }
        }

        function recalcRow(idx) {
            const annual = parseFloat(document.getElementById('annual_' + idx)?.value) || 0;

            const m = [];
            for (let i = 1; i <= 12; i++) {
                m[i] = parseFloat(document.getElementById(`m${i}_${idx}`)?.value) || 0;
            }

            const q1 = m[1] + m[2] + m[3];
            const q2 = m[4] + m[5] + m[6];
            const q3 = m[7] + m[8] + m[9];
            const q4 = m[10] + m[11] + m[12];
            const total = q1 + q2 + q3 + q4;

            const bq1 = document.getElementById(`q1_badge_${idx}`);
            const bq2 = document.getElementById(`q2_badge_${idx}`);
            const bq3 = document.getElementById(`q3_badge_${idx}`);
            const bq4 = document.getElementById(`q4_badge_${idx}`);
            if (bq1) bq1.innerText = q1.toFixed(2);
            if (bq2) bq2.innerText = q2.toFixed(2);
            if (bq3) bq3.innerText = q3.toFixed(2);
            if (bq4) bq4.innerText = q4.toFixed(2);

            const balanceBadge = document.getElementById(`balance_badge_${idx}`);
            if (balanceBadge) {
                const variance = Math.round((total - annual) * 100) / 100;
                if (annual > 0 && Math.abs(variance) < 0.01) {
                    balanceBadge.className =
                        'text-[10px] font-black px-1.5 py-1 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 inline-block w-full text-center';
                    balanceBadge.innerText = `✓ ${total.toFixed(0)} / ${annual.toFixed(0)}`;
                } else if (annual === 0) {
                    balanceBadge.className =
                        'text-[10px] font-black px-1.5 py-1 rounded bg-slate-100 text-slate-500 inline-block w-full text-center';
                    balanceBadge.innerText = `0 / 0`;
                } else {
                    balanceBadge.className =
                        'text-[10px] font-black px-1.5 py-1 rounded bg-amber-100 text-amber-900 border border-amber-300 inline-block w-full text-center';
                    balanceBadge.innerText =
                        `⚠️ ${total.toFixed(0)} / ${annual.toFixed(0)} (${variance > 0 ? '+' : ''}${variance.toFixed(0)})`;
                }
            }

            updateSummaryCounters();
        }

        function distributeEvenly(idx) {
            const annual = parseFloat(document.getElementById('annual_' + idx)?.value) || 0;
            if (annual <= 0) return;

            const base = Math.floor((annual / 12) * 100) / 100;
            const remainder = Math.round((annual - (base * 12)) * 100) / 100;

            for (let i = 1; i <= 12; i++) {
                const val = (i === 12) ? (base + remainder) : base;
                const input = document.getElementById(`m${i}_${idx}`);
                if (input) input.value = val.toFixed(2);
            }
            recalcRow(idx);
        }

        function distributeQuarterly(idx) {
            const annual = parseFloat(document.getElementById('annual_' + idx)?.value) || 0;
            if (annual <= 0) return;

            const base = Math.floor((annual / 4) * 100) / 100;
            const remainder = Math.round((annual - (base * 4)) * 100) / 100;

            for (let i = 1; i <= 12; i++) {
                const input = document.getElementById(`m${i}_${idx}`);
                if (input) input.value = '0.00';
            }

            document.getElementById(`m3_${idx}`).value = base.toFixed(2);
            document.getElementById(`m6_${idx}`).value = base.toFixed(2);
            document.getElementById(`m9_${idx}`).value = base.toFixed(2);
            document.getElementById(`m12_${idx}`).value = (base + remainder).toFixed(2);

            recalcRow(idx);
        }

        function removeRow(idx) {
            const row = document.getElementById(`row_${idx}`);
            if (!row) return;

            const planId = row.getAttribute('data-plan-id');
            if (planId) {
                if (!confirm('Are you sure you want to permanently delete this target plan row from the database?')) {
                    return;
                }

                fetch(`{{ url('bed2-plans') }}/${planId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                            updateSummaryCounters();
                        } else {
                            alert(data.error || 'Failed to delete row.');
                        }
                    })
                    .catch(err => {
                        alert('Error deleting row: ' + err.message);
                    });
            } else {
                row.remove();
                updateSummaryCounters();
            }
        }

        function removeHeaderRow(btn, typeName) {
            const tr = btn.closest('tr');
            if (!tr) return;
            if (confirm(`Remove this ${typeName} header row?`)) {
                tr.remove();
            }
        }

        function updateSummaryCounters() {
            const rows = document.querySelectorAll('.data-row');
            let configuredCount = 0;
            rows.forEach(r => {
                const idx = r.getAttribute('data-index');
                const annual = parseFloat(document.getElementById('annual_' + idx)?.value) || 0;
                if (annual > 0) configuredCount++;
            });

            const summaryEl = document.getElementById('matrixSummaryBadge');
            const bottomEl = document.getElementById('encodedCountDisplay');
            if (summaryEl) summaryEl.innerText = `${configuredCount} Active Commitments`;
            if (bottomEl) bottomEl.innerText = configuredCount;
        }

        function openNewCategoryPapModal(defaultCategory = '') {
            if (defaultCategory) {
                document.getElementById('modalCategoryInput').value = defaultCategory;
            }
            document.getElementById('newCategoryPapModal').classList.remove('hidden');
            setTimeout(() => {
                const searchInp = document.getElementById('modalPapSearchInput');
                if (searchInp) {
                    searchInp.value = '';
                    filterPapSelect('');
                    searchInp.focus();
                }
            }, 60);
        }

        function closeNewCategoryPapModal() {
            document.getElementById('newCategoryPapModal').classList.add('hidden');
        }

        let papKeystrokeBuffer = '';
        let papKeystrokeTimer = null;

        function initPapKeystrokeLock() {
            const select = document.getElementById('modalPapSelect');
            if (!select) return;

            // Direct keydown on select element
            select.addEventListener('keydown', function(e) {
                if (['Tab', 'Escape', 'Enter', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) return;
                if (e.altKey || e.ctrlKey || e.metaKey) return;

                if (e.key === 'Backspace') {
                    papKeystrokeBuffer = papKeystrokeBuffer.slice(0, -1);
                    lockSelectToMatch(select, papKeystrokeBuffer);
                    return;
                }

                if (e.key.length !== 1) return;

                e.preventDefault();
                clearTimeout(papKeystrokeTimer);
                papKeystrokeBuffer += e.key.toLowerCase();
                papKeystrokeTimer = setTimeout(() => { papKeystrokeBuffer = ''; }, 1200);

                lockSelectToMatch(select, papKeystrokeBuffer);
            });

            // If modal is open and user presses keys anywhere not in another form input, redirect to lock
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('newCategoryPapModal');
                if (!modal || modal.classList.contains('hidden')) return;

                if (e.key === 'Escape') {
                    closeNewCategoryPapModal();
                    return;
                }

                const active = document.activeElement;
                const searchInput = document.getElementById('modalPapSearchInput');
                const isOtherFormInput = active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName) && active !== searchInput && active !== select;

                if (!isOtherFormInput && e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    if (searchInput && active !== searchInput && active !== select) {
                        searchInput.focus();
                    }
                }
            });
        }

        function lockSelectToMatch(select, query) {
            if (!query) return;
            const cleanQuery = query.trim().toLowerCase();
            if (!cleanQuery) return;

            const options = Array.from(select.options);

            const match = options.find(opt => {
                if (!opt.value || opt.value === 'new') return false;
                const name = (opt.getAttribute('data-name') || '').toLowerCase();
                return name.startsWith(cleanQuery);
            }) || options.find(opt => {
                if (!opt.value || opt.value === 'new') return false;
                const code = (opt.getAttribute('data-code') || '').toLowerCase();
                return code.startsWith(cleanQuery) || code.replace(/[^a-z0-9]/g, '').startsWith(cleanQuery.replace(/[^a-z0-9]/g, ''));
            }) || options.find(opt => {
                if (!opt.value || opt.value === 'new') return false;
                const name = (opt.getAttribute('data-name') || '').toLowerCase();
                const words = name.split(/[\s\-_\/]+/);
                return words.some(w => w.startsWith(cleanQuery));
            }) || options.find(opt => {
                if (!opt.value || opt.value === 'new') return false;
                const name = (opt.getAttribute('data-name') || '').toLowerCase();
                return name.includes(cleanQuery);
            }) || options.find(opt => {
                if (!opt.value || opt.value === 'new') return false;
                const code = (opt.getAttribute('data-code') || '').toLowerCase();
                return code.includes(cleanQuery);
            }) || options.find(opt => {
                if (!opt.value || opt.value === 'new') return false;
                return opt.textContent.toLowerCase().includes(cleanQuery);
            });

            if (match) {
                select.value = match.value;
                onPapSelectChange(select);
            }
        }

        function filterPapSelect(val) {
            const select = document.getElementById('modalPapSelect');
            if (!select) return;

            const query = val.trim().toLowerCase();
            const options = Array.from(select.options);

            if (!query) {
                options.forEach(opt => { opt.hidden = false; });
                return;
            }

            options.forEach(opt => {
                if (!opt.value || opt.value === 'new') {
                    opt.hidden = false;
                    return;
                }

                const name = (opt.getAttribute('data-name') || '').toLowerCase();
                const code = (opt.getAttribute('data-code') || '').toLowerCase();
                const text = opt.textContent.toLowerCase();

                const isMatch = name.includes(query) || code.includes(query) || text.includes(query);
                opt.hidden = !isMatch;
            });

            lockSelectToMatch(select, query);
        }

        function handlePapSearchKeydown(e) {
            const select = document.getElementById('modalPapSelect');
            if (!select) return;

            if (e.key === 'Enter') {
                e.preventDefault();
                if (select.value) {
                    const nextInput = document.getElementById('modalSubProgram');
                    if (nextInput) nextInput.focus();
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                select.focus();
            }
        }

        function openAddSubProgramModal(papId, papCode, papName) {
            document.getElementById('spModalPapId').value = papId;
            document.getElementById('spModalPapCode').value = papCode;
            document.getElementById('spModalPapName').value = papName;
            document.getElementById('spModalTitle').value = '';
            document.getElementById('addSubProgramModal').classList.remove('hidden');
            document.getElementById('spModalTitle').focus();
        }

        function closeAddSubProgramModal() {
            document.getElementById('addSubProgramModal').classList.add('hidden');
        }

        function submitSubProgramOnly(event) {
            event.preventDefault();
            const papId = document.getElementById('spModalPapId').value;
            const papCode = document.getElementById('spModalPapCode').value;
            const papName = document.getElementById('spModalPapName').value;
            const title = document.getElementById('spModalTitle').value.trim();

            ensureSubProgramRow(papId, papCode, papName, title);
            closeAddSubProgramModal();
        }

        function openAddActivityModal(papId, papCode, papName, subProgram = '') {
            document.getElementById('actModalPapId').value = papId;
            document.getElementById('actModalPapCode').value = papCode;
            document.getElementById('actModalPapName').value = papName;
            document.getElementById('actModalSubProgram').value = subProgram;
            document.getElementById('actModalTitle').value = '';
            document.getElementById('addActivityModal').classList.remove('hidden');
            document.getElementById('actModalTitle').focus();
        }

        function closeAddActivityModal() {
            document.getElementById('addActivityModal').classList.add('hidden');
        }

        function submitActivityOnly(event) {
            event.preventDefault();
            const papId = document.getElementById('actModalPapId').value;
            const papCode = document.getElementById('actModalPapCode').value;
            const papName = document.getElementById('actModalPapName').value;
            const subProgram = document.getElementById('actModalSubProgram').value;
            const title = document.getElementById('actModalTitle').value.trim();

            if (subProgram) {
                ensureSubProgramRow(papId, papCode, papName, subProgram);
            }
            ensureActivityRow(papId, papCode, papName, subProgram, title);
            closeAddActivityModal();
        }

        function submitNewStructure(event) {
            event.preventDefault();
            const btn = document.getElementById('btnSubmitModal');
            btn.disabled = true;
            btn.innerText = 'Inserting...';

            const sel = document.getElementById('modalPapSelect');
            const isNew = sel.value === 'new';
            const includeIndicator = document.getElementById('modalIncludeIndicatorChk').checked;

            let papId = isNew ? null : sel.value;
            let papCode = isNew ? document.getElementById('modalPapCode').value.trim() : sel.options[sel.selectedIndex]
                .getAttribute('data-code');
            let papName = isNew ? document.getElementById('modalPapName').value.trim() : sel.options[sel.selectedIndex]
                .getAttribute('data-name');
            let category = isNew ? document.getElementById('modalCategoryInput').value.trim() : sel.options[sel
                .selectedIndex].getAttribute('data-category');

            const subProgram = document.getElementById('modalSubProgram').value.trim();
            const activity = document.getElementById('modalActivity').value.trim();
            const subActivity = document.getElementById('modalSubActivity').value.trim();
            const indicatorName = document.getElementById('modalIndicatorName').value.trim();
            const indicatorUnit = document.getElementById('modalIndicatorUnit').value.trim();
            const annualTarget = parseFloat(document.getElementById('modalAnnualTarget').value) || 0;

            if (isNew) {
                fetch("{{ route('paps.quick-store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            category: category,
                            code: papCode,
                            name: papName,
                            indicator_name: includeIndicator ? indicatorName : null,
                            indicator_unit: indicatorUnit,
                            indicator_type: 'output'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerText = 'Insert into Sheet';

                        if (data.success) {
                            ensureCategoryRow(category);
                            ensurePapRow(data.pap.id, data.pap.code, data.pap.name);
                            if (subProgram) ensureSubProgramRow(data.pap.id, data.pap.code, data.pap.name, subProgram);
                            if (activity) ensureActivityRow(data.pap.id, data.pap.code, data.pap.name, subProgram,
                                activity);

                            if (includeIndicator) {
                                insertDynamicRow({
                                    papId: data.pap.id,
                                    papCode: data.pap.code,
                                    papName: data.pap.name,
                                    category: category,
                                    subProgram: subProgram,
                                    activity: activity,
                                    subActivity: subActivity,
                                    indicatorId: data.indicator ? data.indicator.id : null,
                                    indicatorCode: data.indicator ? data.indicator.code : (papCode + '-IND-01'),
                                    indicatorName: indicatorName,
                                    indicatorUnit: indicatorUnit,
                                    indicatorType: 'output',
                                    annualTarget: annualTarget
                                });
                            }

                            closeNewCategoryPapModal();
                            document.getElementById('quickStructureForm').reset();
                        } else {
                            alert(data.message || 'Error creating PAP.');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerText = 'Insert into Sheet';
                        alert('Network error: ' + err.message);
                    });
            } else {
                btn.disabled = false;
                btn.innerText = 'Insert into Sheet';

                ensureCategoryRow(category);
                ensurePapRow(papId, papCode, papName);
                if (subProgram) ensureSubProgramRow(papId, papCode, papName, subProgram);
                if (activity) ensureActivityRow(papId, papCode, papName, subProgram, activity);

                if (includeIndicator) {
                    insertDynamicRow({
                        papId: papId,
                        papCode: papCode,
                        papName: papName,
                        category: category,
                        subProgram: subProgram,
                        activity: activity,
                        subActivity: subActivity,
                        indicatorId: null,
                        indicatorCode: papCode + '-IND-NEW',
                        indicatorName: indicatorName || '',
                        indicatorUnit: indicatorUnit || 'Units',
                        indicatorType: 'output',
                        annualTarget: annualTarget
                    });
                }

                closeNewCategoryPapModal();
                document.getElementById('quickStructureForm').reset();
            }
        }

        function ensureCategoryRow(category) {
            const tbody = document.getElementById('sheetTbody');
            const emptyPrompt = document.getElementById('emptySheetPromptRow');
            if (emptyPrompt) emptyPrompt.remove();

            const catUpper = (category || 'OPERATIONS').toUpperCase().trim();
            let catRow = tbody.querySelector(`.category-banner-row[data-category="${catUpper}"]`);
            if (!catRow) {
                const catHtml = `
                    <tr class="category-banner-row bg-slate-100/90 text-slate-800 font-extrabold text-xs uppercase tracking-wider border-y border-slate-200 select-none"
                        data-category="${catUpper}">
                        <!-- Col 1: Category Title -->
                        <td class="p-2.5 px-4 border-r border-slate-200 border-l-4 border-l-emerald-600 bg-slate-100/90 text-slate-900 font-extrabold text-xs uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                <span>${catUpper}</span>
                            </div>
                        </td>
                        <!-- Col 2: Action Button -->
                        <td class="p-2 border-r border-slate-200 bg-slate-100/90">
                            <button type="button" onclick="openNewCategoryPapModal('${catUpper}')"
                                class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-50 border border-emerald-200 text-[10px] text-emerald-800 font-bold shadow-sm transition flex items-center gap-1">
                                <span class="text-emerald-600 font-extrabold">+</span>
                                <span>Add Program under ${catUpper}</span>
                            </button>
                        </td>
                        <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="border-r border-slate-200/80 bg-slate-100/70"></td>
                        <td class="bg-slate-100/70"></td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', catHtml);
            }
        }

        function ensurePapRow(papId, papCode, papName) {
            const tbody = document.getElementById('sheetTbody');
            const emptyPrompt = document.getElementById('emptySheetPromptRow');
            if (emptyPrompt) emptyPrompt.remove();

            let papRow = tbody.querySelector(`.pap-banner-row[data-pap-id="${papId}"]`);
            if (!papRow) {
                const papHtml = `
                    <tr class="pap-banner-row bg-emerald-50/90 text-emerald-950 font-extrabold text-xs uppercase tracking-wide border-t border-b border-emerald-200 select-none"
                        data-pap-id="${papId}" data-pap-code="${papCode}" data-pap-name="${papName}">
                        <!-- Col 1: PAP Badge & Name -->
                        <td class="p-2 px-3 border-r border-emerald-200 bg-emerald-50/90 text-emerald-950 font-extrabold text-xs uppercase tracking-wide">
                            <div class="flex items-start gap-2">
                                <span class="font-mono bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded border border-emerald-300 font-black text-[11px] shrink-0 mt-0.5">
                                    ${papCode}
                                </span>
                                <span class="font-extrabold text-emerald-950 text-xs leading-snug whitespace-normal break-words" title="${papName}">${papName}</span>
                            </div>
                        </td>
                        <!-- Col 2: Action Buttons -->
                        <td class="p-2 border-r border-emerald-200 bg-emerald-50/90">
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="addRowUnderPap(${papId}, '${papCode}', '${papName}')"
                                    class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] transition flex items-center gap-1 shadow-sm">
                                    + Add Indicator Row
                                </button>
                                <button type="button" onclick="openAddSubProgramModal(${papId}, '${papCode}', '${papName}')"
                                    class="px-2 py-0.5 rounded bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 font-extrabold text-[10px] transition">
                                    + Sub-Program
                                </button>
                                <button type="button" onclick="openAddActivityModal(${papId}, '${papCode}', '${papName}')"
                                    class="px-2 py-0.5 rounded bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 font-extrabold text-[10px] transition">
                                    + Activity
                                </button>
                            </div>
                        </td>
                        <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                        <td class="border-r border-emerald-200 bg-emerald-50/50"></td>
                        <td class="border-r border-emerald-300 bg-amber-50/30"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-100 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-200 bg-emerald-50/30"></td>
                        <td class="border-r border-emerald-300 bg-emerald-100/60"></td>
                        <td class="border-r border-emerald-200 bg-emerald-50/50"></td>
                        <td class="bg-emerald-50/50"></td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', papHtml);
            }
        }

        function ensureSubProgramRow(papId, papCode, papName, subProgram) {
            const tbody = document.getElementById('sheetTbody');
            let spRow = tbody.querySelector(`.subprogram-row[data-pap-id="${papId}"][data-subprogram="${subProgram}"]`);
            if (!spRow) {
                const spHtml = `
                    <tr class="subprogram-row bg-slate-100 font-black text-slate-800 text-[11px] border-b border-slate-200"
                        data-pap-id="${papId}" data-subprogram="${subProgram}">
                        <!-- Col 1: Sub-Program Title -->
                        <td class="py-2 px-3 border-r border-slate-200 bg-slate-100">
                            <span class="italic font-bold text-slate-800 whitespace-normal break-words leading-snug block" title="${subProgram}">${subProgram}</span>
                        </td>
                        <!-- Col 2: Action Button -->
                        <td class="py-2 px-2 border-r border-slate-200 bg-slate-100">
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="addRowUnderPap(${papId}, '${papCode}', '${papName}', '${subProgram}')"
                                        class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[9px] transition flex items-center gap-1 shadow-sm">
                                        + Add Indicator Row
                                    </button>
                                    <button type="button" onclick="openAddActivityModal(${papId}, '${papCode}', '${papName}', '${subProgram}')"
                                        class="text-[9px] font-bold text-slate-600 hover:text-emerald-700 px-2 py-0.5 rounded hover:bg-slate-200 transition">
                                        + Activity
                                    </button>
                                </div>
                                <button type="button" onclick="removeHeaderRow(this, 'Sub-Program')"
                                    class="text-slate-400 hover:text-rose-600 font-bold text-sm px-1 leading-none" title="Remove Sub-Program Header">&times;</button>
                            </div>
                        </td>
                        <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                        <td class="border-r border-slate-200 bg-slate-50"></td>
                        <td class="border-r border-slate-300 bg-amber-50/20"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-200 bg-slate-50/50"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-200 bg-slate-50/50"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-200 bg-slate-50/50"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-100 bg-slate-50/50"></td>
                        <td class="border-r border-slate-200 bg-slate-50/50"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-200 bg-slate-50"></td>
                        <td class="bg-slate-50"></td>
                    </tr>
                `;
                // Insert after papRow or at end
                let papRow = tbody.querySelector(`.pap-banner-row[data-pap-id="${papId}"]`);
                if (papRow) {
                    papRow.insertAdjacentHTML('afterend', spHtml);
                } else {
                    tbody.insertAdjacentHTML('beforeend', spHtml);
                }
            }
        }

        function ensureActivityRow(papId, papCode, papName, subProgram, activity) {
            const tbody = document.getElementById('sheetTbody');
            let actRow = tbody.querySelector(`.activity-row[data-pap-id="${papId}"][data-activity="${activity}"]`);
            if (!actRow) {
                const actHtml = `
                    <tr class="activity-row bg-slate-50 font-extrabold text-slate-700 text-[11px] border-b border-slate-200"
                        data-pap-id="${papId}" data-subprogram="${subProgram || ''}" data-activity="${activity}">
                        <!-- Col 1: Activity Title -->
                        <td class="py-2 px-5 border-r border-slate-200 bg-slate-50">
                            <span class="font-extrabold text-emerald-950 whitespace-normal break-words leading-snug block" title="${activity}">&bull; ${activity}</span>
                        </td>
                        <!-- Col 2: Action Button -->
                        <td class="py-2 px-2 border-r border-slate-200 bg-slate-50">
                            <div class="flex items-center justify-between gap-1">
                                <button type="button" onclick="addRowUnderPap(${papId}, '${papCode}', '${papName}', '${subProgram || ''}', '${activity}')"
                                    class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[9px] transition flex items-center gap-1 shadow-sm">
                                    + Add Indicator Row
                                </button>
                                <button type="button" onclick="removeHeaderRow(this, 'Activity')"
                                    class="text-slate-400 hover:text-rose-600 font-bold text-sm px-1 leading-none" title="Remove Activity Header">&times;</button>
                            </div>
                        </td>
                        <!-- Cols 3-22: Shaded Grid Cells with Vertical Borders -->
                        <td class="border-r border-slate-200 bg-white"></td>
                        <td class="border-r border-slate-300 bg-amber-50/20"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-200 bg-white"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-200 bg-white"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-200 bg-white"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-100 bg-white"></td>
                        <td class="border-r border-slate-200 bg-white"></td>
                        <td class="border-r border-slate-300 bg-slate-100"></td>
                        <td class="border-r border-slate-200 bg-white"></td>
                        <td class="bg-white"></td>
                    </tr>
                `;
                // Place after subprogram if exists, else after papRow
                let spRow = subProgram ? tbody.querySelector(
                    `.subprogram-row[data-pap-id="${papId}"][data-subprogram="${subProgram}"]`) : null;
                if (spRow) {
                    spRow.insertAdjacentHTML('afterend', actHtml);
                } else {
                    let papRow = tbody.querySelector(`.pap-banner-row[data-pap-id="${papId}"]`);
                    if (papRow) papRow.insertAdjacentHTML('afterend', actHtml);
                    else tbody.insertAdjacentHTML('beforeend', actHtml);
                }
            }
        }

        function addRowUnderPap(papId, papCode, papName, defaultSubProgram = '', defaultActivity = '') {
            insertDynamicRow({
                papId: papId,
                papCode: papCode,
                papName: papName,
                category: '',
                subProgram: defaultSubProgram,
                activity: defaultActivity,
                subActivity: '',
                indicatorId: null,
                indicatorCode: papCode + '-IND-NEW',
                indicatorName: '',
                indicatorUnit: 'Units',
                indicatorType: 'output',
                annualTarget: 0
            });
        }

        function insertDynamicRow(data) {
            const tbody = document.getElementById('sheetTbody');
            const emptyPrompt = document.getElementById('emptySheetPromptRow');
            if (emptyPrompt) emptyPrompt.remove();

            const idx = totalRows++;

            // Ensure structural rows exist
            if (data.category) ensureCategoryRow(data.category);
            ensurePapRow(data.papId, data.papCode, data.papName);
            if (data.subProgram) ensureSubProgramRow(data.papId, data.papCode, data.papName, data.subProgram);
            if (data.activity) ensureActivityRow(data.papId, data.papCode, data.papName, data.subProgram, data.activity);

            // Compute default process step number if possible
            let stepPrefix = '';
            if (!data.subActivity) {
                const existingInAct = tbody.querySelectorAll(
                    `.data-row[data-pap-id="${data.papId}"][data-activity="${data.activity || ''}"]`);
                stepPrefix = (existingInAct.length + 1) + '. ';
            }

            const newRowHtml = `
                <tr class="data-row hover:bg-slate-50/80 transition" id="row_${idx}" data-index="${idx}"
                    data-pap-id="${data.papId}" data-subprogram="${data.subProgram || ''}" data-activity="${data.activity || ''}">
                    <input type="hidden" name="plans[${idx}][pap_id]" value="${data.papId}">
                    <input type="hidden" name="plans[${idx}][performance_indicator_id]" value="${data.indicatorId || ''}">
                    <input type="hidden" name="plans[${idx}][sub_program]" value="${data.subProgram || ''}">
                    <input type="hidden" name="plans[${idx}][activity]" value="${data.activity || ''}">

                    <!-- Col 1: Specific Activity / Breakdown -->
                    <td class="p-0 border-r border-slate-200 align-top min-w-[340px] max-w-[440px] w-[380px]">
                        <input type="hidden" name="plans[${idx}][indicator_code]" value="${data.indicatorCode || ''}">
                        <textarea name="plans[${idx}][sub_activity]"
                            placeholder="1. Specific activity or task breakdown..."
                            oninput="autoResizeTextarea(this)"
                            class="auto-expand-textarea w-full p-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 rounded-none focus:outline-none focus:ring-0 leading-relaxed block">${data.subActivity ? data.subActivity : stepPrefix}</textarea>
                    </td>

                    <!-- Col 2: Key Indicator (KPI) -->
                    <td class="p-0 border-r border-slate-200 align-top min-w-[320px] max-w-[420px] w-[360px]">
                        <textarea name="plans[${idx}][indicator_name]"
                            placeholder="Key Performance Indicator target text..."
                            oninput="autoResizeTextarea(this)"
                            class="auto-expand-textarea w-full p-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 rounded-none focus:outline-none focus:ring-0 leading-relaxed block">${data.indicatorName || ''}</textarea>
                    </td>

                    <!-- Col 3: Unit -->
                    <td class="p-2 border-r border-slate-200 align-middle text-center bg-white min-w-[70px] w-[70px]">
                        <input type="text" name="plans[${idx}][unit_of_measure]" value="${data.indicatorUnit || 'Units'}"
                            class="w-16 px-1.5 py-1 text-center font-bold text-slate-700 border border-slate-200 rounded-lg text-xs bg-slate-50/50 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                    </td>

                    <!-- Col 4: Annual Target -->
                    <td class="p-2 border-r border-slate-300 align-middle bg-amber-50/40 text-center min-w-[100px] w-[100px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][annual_target]"
                            id="annual_${idx}" value="${data.annualTarget || 0}" required
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-24 min-w-[90px] px-2 py-1 text-right font-mono font-black text-slate-900 border border-amber-300 rounded-lg text-xs bg-white focus:ring-2 focus:ring-emerald-500 shadow-sm">
                    </td>

                    <!-- Q1 Months -->
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m1_target]" id="m1_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m2_target]" id="m2_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m3_target]" id="m3_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                        <span id="q1_badge_${idx}" class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                    </td>

                    <!-- Q2 Months -->
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m4_target]" id="m4_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m5_target]" id="m5_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m6_target]" id="m6_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                        <span id="q2_badge_${idx}" class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                    </td>

                    <!-- Q3 Months -->
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m7_target]" id="m7_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m8_target]" id="m8_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m9_target]" id="m9_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                        <span id="q3_badge_${idx}" class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                    </td>

                    <!-- Q4 Months -->
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m10_target]" id="m10_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-100 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m11_target]" id="m11_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[68px] w-[68px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m12_target]" id="m12_${idx}" value="0"
                            oninput="recalcRow(${idx})"
                            class="sheet-number-input w-[60px] min-w-[60px] px-1 py-1 text-center font-mono font-bold border border-slate-200 rounded text-xs focus:ring-1 focus:ring-emerald-500">
                    </td>
                    <td class="p-1 border-r border-slate-300 text-center align-middle bg-slate-100 min-w-[74px] w-[74px]">
                        <span id="q4_badge_${idx}" class="w-[66px] min-w-[66px] py-1 font-mono font-extrabold text-xs text-slate-900 inline-block bg-slate-200 rounded text-center px-1">0.00</span>
                    </td>

                    <!-- Balance Status -->
                    <td class="p-1 border-r border-slate-200 text-center align-middle bg-white min-w-[105px] w-[105px]">
                        <span id="balance_badge_${idx}" class="text-[10px] font-black px-1.5 py-1 rounded bg-slate-100 text-slate-600 inline-block w-full text-center">
                            0 / 0
                        </span>
                    </td>

                    <!-- Presets & Actions -->
                    <td class="p-1 text-center align-middle bg-white min-w-[120px] w-[120px]">
                        <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="distributeEvenly(${idx})"
                                    class="px-1.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded border border-emerald-200" title="Distribute Evenly">
                                    ⚡Even
                                </button>
                                <button type="button" onclick="distributeQuarterly(${idx})"
                                    class="px-1.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-extrabold text-[10px] rounded border border-indigo-200" title="Distribute Quarterly">
                                    ⚡Qtr
                                </button>
                                <button type="button" onclick="removeRow(${idx})"
                                    class="p-1 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-800 rounded border border-rose-200 transition" title="Remove this row">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                        </div>
                    </td>
                </tr>
            `;

            // Insert in correct position under the PAP hierarchy
            if (data.activity) {
                const existingRowsInActivity = tbody.querySelectorAll(
                    `.data-row[data-pap-id="${data.papId}"][data-activity="${data.activity}"]`);
                if (existingRowsInActivity.length > 0) {
                    existingRowsInActivity[existingRowsInActivity.length - 1].insertAdjacentHTML('afterend', newRowHtml);
                } else {
                    const actRow = tbody.querySelector(
                        `.activity-row[data-pap-id="${data.papId}"][data-activity="${data.activity}"]`);
                    if (actRow) {
                        actRow.insertAdjacentHTML('afterend', newRowHtml);
                    } else {
                        tbody.insertAdjacentHTML('beforeend', newRowHtml);
                    }
                }
            } else if (data.subProgram) {
                const existingInSub = tbody.querySelectorAll(
                    `.data-row[data-pap-id="${data.papId}"][data-subprogram="${data.subProgram}"]`);
                if (existingInSub.length > 0) {
                    existingInSub[existingInSub.length - 1].insertAdjacentHTML('afterend', newRowHtml);
                } else {
                    const spRow = tbody.querySelector(
                        `.subprogram-row[data-pap-id="${data.papId}"][data-subprogram="${data.subProgram}"]`);
                    if (spRow) {
                        spRow.insertAdjacentHTML('afterend', newRowHtml);
                    } else {
                        tbody.insertAdjacentHTML('beforeend', newRowHtml);
                    }
                }
            } else {
                // Direct child row under the PAP banner
                const existingDirectPapRows = tbody.querySelectorAll(
                    `.data-row[data-pap-id="${data.papId}"][data-subprogram=""][data-activity=""]`);
                if (existingDirectPapRows.length > 0) {
                    existingDirectPapRows[existingDirectPapRows.length - 1].insertAdjacentHTML('afterend', newRowHtml);
                } else {
                    const papRow = tbody.querySelector(`.pap-banner-row[data-pap-id="${data.papId}"]`);
                    if (papRow) {
                        papRow.insertAdjacentHTML('afterend', newRowHtml);
                    } else {
                        tbody.insertAdjacentHTML('beforeend', newRowHtml);
                    }
                }
            }

            recalcRow(idx);
            updateSummaryCounters();

            const newRow = document.getElementById(`row_${idx}`);
            if (newRow) {
                newRow.querySelectorAll('.auto-expand-textarea').forEach(autoResizeTextarea);
                newRow.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        function autoResizeTextarea(el) {
            if (!el) return;
            // Reset height first so scrollHeight accurately reflects full content
            el.style.height = 'auto';
            // In Chromium box-sizing: border-box, scrollHeight omits bottom padding.
            // Add a generous safety buffer (+18px) so descenders and the bottom line are never clipped.
            const targetHeight = Math.max(54, el.scrollHeight + 18);
            el.style.height = targetHeight + 'px';
        }

        function initAutoResizeTextareas() {
            document.querySelectorAll('.auto-expand-textarea').forEach(function(el) {
                autoResizeTextarea(el);
            });
        }

        function initPage() {
            initAutoResizeTextareas();
            initPapKeystrokeLock();
        }

        window.addEventListener('DOMContentLoaded', initPage);
        window.addEventListener('load', initAutoResizeTextareas);
        window.addEventListener('resize', initAutoResizeTextareas);
        setTimeout(initAutoResizeTextareas, 50);
        setTimeout(initAutoResizeTextareas, 200);
        setTimeout(initAutoResizeTextareas, 600);

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(initAutoResizeTextareas);
        }
    </script>
@endsection
