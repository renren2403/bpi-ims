<!--create.blade.php BED-2 Target Plan (Spreadsheet Editor with Dedicated Hierarchy Rows)-->
@extends('layouts.app')
@section('title', 'BED-2 Physical Plan Spreadsheet Editor')

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

        /* Autocomplete dropdown scrollbar */
        .autocomplete-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        .autocomplete-dropdown::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* True Spreadsheet Cell Focus & In-Cell Input Styling */
        .sheet-cell-input {
            border: 0 !important;
            outline: none !important;
            background: transparent !important;
            width: 100% !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        input.sheet-cell-input {
            height: 100% !important;
        }

        .sheet-cell-input:focus {
            background: rgba(16, 185, 129, 0.08) !important;
            box-shadow: inset 0 0 0 1.5px #10b981 !important;
        }

        .sheet-cell-input-amber:focus {
            background: rgba(245, 158, 11, 0.1) !important;
            box-shadow: inset 0 0 0 1.5px #f59e0b !important;
        }

        /* Auto-wrapping multi-line spreadsheet textarea cells */
        .sheet-cell-textarea {
            resize: none !important;
            overflow: hidden !important;
            white-space: pre-wrap !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            line-height: 1.4 !important;
            min-height: 28px !important;
            padding: 4px 6px !important;
            display: block !important;
            box-sizing: border-box !important;
        }
    </style>
    <div class="space-y-4">
        <!-- Top Toolbar & Status Bar -->
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/90 shadow-sm">
            <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-3">
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            BED-2 Physical Plan Worksheet
                        </h2>
                        <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                            FY {{ $year }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-2 flex-wrap text-[11px]">
                        <span class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">Hierarchy Legend:</span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-purple-50 text-purple-900 font-extrabold border-l-4 border-l-purple-600 shadow-2xs">
                            Purple Border = Program / PAP
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-indigo-50 text-indigo-900 font-extrabold border-l-4 border-l-indigo-500 shadow-2xs">
                            Indigo Border = Activity
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-amber-50 text-amber-900 font-extrabold border-l-4 border-l-amber-500 shadow-2xs">
                            Amber Border = Sub-activity
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-slate-50 text-slate-700 font-bold border-l-4 border-l-slate-300 shadow-2xs">
                            White Rows = Indicators &amp; Targets
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('bed2-plans.index', ['year' => $year]) }}"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-1.5 shadow-xs">
                        <span>&larr;</span>
                        <span>Back to Matrix</span>
                    </a>
                </div>
            </div>
            <!-- Jurisdiction Selector Bar -->
            <div
                class="mt-3 pt-2.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
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
                        0 Indicator Commitments
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
            <!-- Table Container with Full Screen Height and Responsive Scrollbars -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col h-[calc(100vh-285px)] min-h-[440px]">
                <div id="tableScrollContainer" class="sheet-scroll-container flex-1 overflow-x-auto overflow-y-auto relative">
                    <table class="w-full text-left text-xs border-collapse" id="smartSheetTable">
                        <!-- Clean Sticky Table Header -->
                        <thead
                            class="sticky top-0 z-30 bg-slate-50 text-slate-600 text-[10px] font-extrabold uppercase tracking-wider border-b border-slate-200 shadow-[0_1px_3px_rgba(15,23,42,0.06)] select-none">
                            <tr>
                                <th rowspan="2"
                                    class="p-3 border-r border-slate-200 min-w-[340px] max-w-[420px] w-[370px] text-left bg-slate-50 text-slate-700">
                                    PROGRAM / PROJECTS / ACTIVITIES
                                </th>
                                <th rowspan="2"
                                    class="p-3 border-r border-slate-200 min-w-[300px] max-w-[380px] w-[330px] text-left bg-slate-50 text-slate-700">
                                    KEY INDICATORS
                                </th>
                                <th rowspan="2"
                                    class="p-2 border-r border-slate-200 min-w-[70px] w-[70px] text-center bg-slate-50 text-slate-700">
                                    Unit
                                </th>
                                <th rowspan="2"
                                    class="p-2 border-r border-amber-200 min-w-[100px] w-[100px] text-center bg-amber-50/90 text-amber-900 font-black">
                                    Annual Target
                                </th>
                                <!-- Q1: Emerald / Mint -->
                                <th colspan="4"
                                    class="p-1.5 border-r border-emerald-300 text-center bg-emerald-100 font-extrabold text-emerald-950 tracking-wide">
                                    1st Quarter
                                </th>
                                <!-- Q2: Sky / Cyan Blue -->
                                <th colspan="4"
                                    class="p-1.5 border-r border-sky-300 text-center bg-sky-100 font-extrabold text-sky-950 tracking-wide">
                                    2nd Quarter
                                </th>
                                <!-- Q3: Warm Amber / Gold -->
                                <th colspan="4"
                                    class="p-1.5 border-r border-amber-300 text-center bg-amber-100 font-extrabold text-amber-950 tracking-wide">
                                    3rd Quarter
                                </th>
                                <!-- Q4: Soft Purple / Violet -->
                                <th colspan="4"
                                    class="p-1.5 border-r border-purple-300 text-center bg-purple-100 font-extrabold text-purple-950 tracking-wide">
                                    4th Quarter
                                </th>
                                <th rowspan="2"
                                    class="p-2 border-r border-slate-200 min-w-[100px] w-[100px] text-center bg-slate-50 text-slate-700">
                                    Balance
                                </th>
                                <th rowspan="2"
                                    class="sticky right-0 top-0 z-40 p-2 text-center min-w-[210px] w-[210px] bg-slate-100 text-slate-800 shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)] border-l border-slate-300">
                                    ⚡ Action / Insert
                                </th>
                            </tr>
                            <tr class="text-[9px] font-bold text-slate-500 bg-slate-100/60 border-t border-slate-200">
                                <!-- Q1 (Mint / Emerald) -->
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-emerald-50/50 text-emerald-900">Jan</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-emerald-50/50 text-emerald-900">Feb</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-emerald-50/50 text-emerald-900">Mar</th>
                                <th class="p-1 border-r border-emerald-300 min-w-[70px] w-[70px] text-center bg-emerald-200/90 font-black text-emerald-950">Q1</th>

                                <!-- Q2 (Sky / Cyan) -->
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-sky-50/50 text-sky-900">Apr</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-sky-50/50 text-sky-900">May</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-sky-50/50 text-sky-900">Jun</th>
                                <th class="p-1 border-r border-sky-300 min-w-[70px] w-[70px] text-center bg-sky-200/90 font-black text-sky-950">Q2</th>

                                <!-- Q3 (Warm Amber) -->
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-amber-50/50 text-amber-900">Jul</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-amber-50/50 text-amber-900">Aug</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-amber-50/50 text-amber-900">Sep</th>
                                <th class="p-1 border-r border-amber-300 min-w-[70px] w-[70px] text-center bg-amber-200/90 font-black text-amber-950">Q3</th>

                                <!-- Q4 (Soft Purple) -->
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-purple-50/50 text-purple-900">Oct</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-purple-50/50 text-purple-900">Nov</th>
                                <th class="p-1 border-r border-slate-200 min-w-[65px] w-[65px] text-center bg-purple-50/50 text-purple-900">Dec</th>
                                <th class="p-1 border-r border-purple-300 min-w-[70px] w-[70px] text-center bg-purple-200/90 font-black text-purple-950">Q4</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="sheetTbody">
                            <!-- Populated dynamically via JavaScript with structural & indicator rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Spreadsheet Action Bar -->
                <div
                    class="p-3 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="addProgramRow()"
                            class="px-3.5 py-2 bg-purple-700 hover:bg-purple-800 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <span>+ Add Program</span>
                        </button>
                        <button type="button" onclick="addActivityRow()"
                            class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <span>+ Add Activity</span>
                        </button>
                        <button type="button" onclick="addSubactivityRow()"
                            class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <span>+ Add Sub-activity</span>
                        </button>
                        <button type="button" onclick="addIndicatorRow()"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <span class="text-white font-extrabold text-sm leading-none">+</span>
                            <span>Add Indicator Row</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('smartSheetForm').requestSubmit()"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                            <span>💾</span>
                            <span>Save BED-2 Plan</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Client-Side JavaScript for BED-2 Spreadsheet Layout with Dedicated Hierarchy Rows -->
    <script>
        const bureauCatalog = @json($catalog ?? []);
        const existingPlansData = @json($existingPlans->values() ?? []);
        const currentOuId = {{ (int) $defaultOuId }};
        let totalRows = 0;

        document.addEventListener('DOMContentLoaded', function() {
            if (existingPlansData && existingPlansData.length > 0) {
                // Group existing plans into their Program -> Activity -> Subactivity -> Indicator hierarchy
                loadExistingPlansHierarchically(existingPlansData);
            } else {
                // Initialize clean spreadsheet with just 1 Program row.
                // No default indicator row: user can add Activity, Sub-activity, or Indicator rows as needed.
                addProgramRow();
            }
            syncAllHierarchies();
            updateSummaryCounters();
            resizeAllTextareas();
            setTimeout(resizeAllTextareas, 50);
            setTimeout(resizeAllTextareas, 200);
            window.addEventListener('resize', resizeAllTextareas);
            if (document.fonts) {
                document.fonts.ready.then(resizeAllTextareas);
            }

            // Intercept form submission to guarantee all indicator rows have synced parent hierarchy
            const form = document.getElementById('smartSheetForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    syncAllHierarchies();

                    const dataRows = document.querySelectorAll('.data-row');
                    if (dataRows.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one Indicator Row before saving.');
                        return false;
                    }

                    for (const tr of dataRows) {
                        const idx = tr.getAttribute('data-index');
                        const papId = document.getElementById(`pap_id_${idx}`)?.value;
                        const progName = document.getElementById(`program_name_${idx}`)?.value?.trim();
                        const indName = document.getElementById(`indicator_search_${idx}`)?.value.trim();

                        if (!papId && !progName) {
                            e.preventDefault();
                            alert(
                                'Every indicator row must be placed under a Program / PAP. Please type a Program above your indicators.'
                            );
                            return false;
                        }
                        if (!indName) {
                            e.preventDefault();
                            alert(
                                'Please enter a Key Performance Indicator for all indicator rows.'
                            );
                            document.getElementById(`indicator_search_${idx}`)?.focus();
                            return false;
                        }
                    }
                });
            }
        });

        function changeOuOrYear() {
            const ouId = document.getElementById('ouSelector') ? document.getElementById('ouSelector').value :
                '{{ $defaultOuId }}';
            const year = document.getElementById('yearSelector').value;
            window.location.href = "{{ route('bed2-plans.create') }}?operating_unit_id=" + ouId + "&year=" + year;
        }

        // Dynamically auto-resizes multi-line textareas so content is cleanly wrapped and never truncated
        function autoResizeTextarea(el) {
            if (!el) return;
            el.style.setProperty('height', 'auto', 'important');
            const targetHeight = Math.max(28, el.scrollHeight);
            el.style.setProperty('height', targetHeight + 'px', 'important');
        }

        function resizeAllTextareas() {
            document.querySelectorAll('.sheet-cell-textarea').forEach(autoResizeTextarea);
        }

        // Prevent Enter key in single-cell textareas from inserting unwanted linebreaks
        document.addEventListener('keydown', function(e) {
            if (e.target && e.target.classList.contains('sheet-cell-textarea') && e.key === 'Enter') {
                e.preventDefault();
                e.target.blur();
            }
        });

        // Helper to insert a row either after a specific reference row or at the end of the table
        function insertRow(html, insertAfterRow = null) {
            const tbody = document.getElementById('sheetTbody');
            if (insertAfterRow && insertAfterRow.parentNode === tbody) {
                insertAfterRow.insertAdjacentHTML('afterend', html);
                return insertAfterRow.nextElementSibling;
            } else {
                tbody.insertAdjacentHTML('beforeend', html);
                return tbody.lastElementChild;
            }
        }

        // Removes any structural or header row and resyncs
        function removeRow(tr) {
            if (!tr) return;
            tr.remove();
            syncAllHierarchies();
            updateSummaryCounters();
        }

        // Toggle row in-between insert popover menu
        function toggleRowInsertMenu(btn) {
            const menu = btn.nextElementSibling;
            if (!menu) return;
            const isHidden = menu.classList.contains('hidden');
            closeAllInsertMenus();
            if (isHidden) {
                menu.classList.remove('hidden');
            }
        }

        function closeAllInsertMenus() {
            document.querySelectorAll('.row-insert-menu').forEach(m => m.classList.add('hidden'));
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.row-insert-menu') && !e.target.closest(
                    'button[onclick*="toggleRowInsertMenu"]')) {
                closeAllInsertMenus();
            }
        });

        // Traverses backwards to find the nearest preceding Program row
        function getPrecedingProgram(rowEl) {
            let prev = rowEl ? rowEl.previousElementSibling : null;
            while (prev) {
                if (prev.classList.contains('program-row')) {
                    const idInput = prev.querySelector('.program-id-hidden');
                    const nameInput = prev.querySelector('.program-input');
                    return {
                        id: idInput ? idInput.value : '',
                        name: nameInput ? nameInput.value : ''
                    };
                }
                prev = prev.previousElementSibling;
            }
            return null;
        }

        // Traverses backwards to find the nearest preceding Activity row within the current Program
        function getPrecedingActivity(rowEl) {
            let prev = rowEl ? rowEl.previousElementSibling : null;
            while (prev) {
                if (prev.classList.contains('program-row')) break; // Program boundary reached
                if (prev.classList.contains('activity-row')) {
                    const actInput = prev.querySelector('.activity-input');
                    return actInput ? actInput.value.trim() : '';
                }
                prev = prev.previousElementSibling;
            }
            return '';
        }

        // Traverses backwards to find the nearest preceding Sub-activity row within the current Activity
        function getPrecedingSubactivity(rowEl) {
            let prev = rowEl ? rowEl.previousElementSibling : null;
            while (prev) {
                if (prev.classList.contains('program-row') || prev.classList.contains('activity-row'))
                    break; // Boundary reached
                if (prev.classList.contains('subactivity-row')) {
                    const subInput = prev.querySelector('.subactivity-input');
                    return subInput ? subInput.value.trim() : '';
                }
                prev = prev.previousElementSibling;
            }
            return '';
        }

        // Scans the full DOM top-to-bottom and stamps parent values into all indicator rows
        function syncAllHierarchies() {
            const tbody = document.getElementById('sheetTbody');
            if (!tbody) return;

            let currentPapId = '';
            let currentPapName = '';
            let currentActivity = '';
            let currentSubProgram = '';

            for (const tr of tbody.children) {
                if (tr.classList.contains('program-row')) {
                    const idInput = tr.querySelector('.program-id-hidden');
                    const nameInput = tr.querySelector('.program-input');
                    currentPapId = idInput ? idInput.value : '';
                    currentPapName = nameInput ? nameInput.value.trim() : '';
                    currentActivity = '';
                    currentSubProgram = '';
                } else if (tr.classList.contains('activity-row')) {
                    const actInput = tr.querySelector('.activity-input');
                    currentActivity = actInput ? actInput.value.trim() : '';
                    currentSubProgram = ''; // Reset sub-program whenever a new activity is encountered
                } else if (tr.classList.contains('subactivity-row')) {
                    const subInput = tr.querySelector('.subactivity-input');
                    currentSubProgram = subInput ? subInput.value.trim() : '';
                } else if (tr.classList.contains('data-row')) {
                    const idx = tr.getAttribute('data-index');
                    const papHidden = document.getElementById(`pap_id_${idx}`);
                    const progHidden = document.getElementById(`program_name_${idx}`);
                    const actHidden = document.getElementById(`activity_${idx}`);
                    const subHidden = document.getElementById(`sub_program_${idx}`);
                    if (papHidden) papHidden.value = currentPapId;
                    if (progHidden) progHidden.value = currentPapName;
                    if (actHidden) actHidden.value = currentActivity;
                    if (subHidden) subHidden.value = currentSubProgram;
                }
            }
        }

        // Loads saved plans structured under their dedicated hierarchy rows
        function loadExistingPlansHierarchically(plans) {
            let lastProgram = null;
            let lastActivity = null;
            let lastSubProgram = null;

            plans.forEach(plan => {
                const pap = plan.pap;
                const progName = pap ? (pap.category || pap.name) : '';
                const actName = plan.activity || (plan.performance_indicator ? plan.performance_indicator.activity :
                    '');
                const subProgName = plan.sub_program || (plan.performance_indicator ? plan.performance_indicator
                    .sub_program : '');

                if (progName && progName !== lastProgram) {
                    addProgramRow(plan.pap_id, progName);
                    lastProgram = progName;
                    lastActivity = null;
                    lastSubProgram = null;
                }

                if (actName && actName !== lastActivity) {
                    addActivityRow(actName);
                    lastActivity = actName;
                    lastSubProgram = null; // Always reset sub-program on activity change
                }

                if (subProgName && subProgName !== lastSubProgram) {
                    addSubactivityRow(subProgName);
                    lastSubProgram = subProgName;
                }

                addIndicatorRow(plan);
            });
            syncAllHierarchies();
            resizeAllTextareas();
            setTimeout(resizeAllTextareas, 50);
            setTimeout(resizeAllTextareas, 200);
        }

        // 1. PROGRAM ROW: Dedicated structural row with NOTHING in Indicator & Target columns
        function addProgramRow(progId = '', progName = '', insertAfterRow = null) {
            const rowId = 'prog_row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

            const html = `
                <tr class="program-row bg-slate-100/90 border-t-2 border-b border-slate-300 font-bold select-none" id="${rowId}">
                    <!-- Col 1 & 2: Program Title (Bleeds across Key Indicators column with full auto-wrapping) -->
                    <td colspan="2" class="p-1 px-2 border-r border-slate-300 border-l-4 border-l-purple-600 align-middle">
                        <div class="relative w-full">
                            <textarea rows="1" class="program-input sheet-cell-input sheet-cell-textarea text-xs font-black text-slate-900 px-2 py-1 tracking-wide uppercase placeholder:text-slate-400 placeholder:normal-case placeholder:font-normal w-full"
                                placeholder="Type Program / PAP title..." autocomplete="off"
                                oninput="autoResizeTextarea(this)">${escapeHtml(progName)}</textarea>
                            <input type="hidden" class="program-id-hidden" value="${progId}">
                            <div class="autocomplete-dropdown absolute left-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-[60] max-h-64 overflow-y-auto hidden min-w-[360px] w-full"></div>
                        </div>
                    </td>
                    <!-- Col 3: Unit (Blank) -->
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <!-- Col 4: Annual Target (Blank) -->
                    <td class="p-1 border-r border-slate-300 bg-amber-50/30"></td>
                    <!-- Q1 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-emerald-300 bg-emerald-100/50"></td>
                    <!-- Q2 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-sky-300 bg-sky-100/50"></td>
                    <!-- Q3 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-amber-300 bg-amber-100/50"></td>
                    <!-- Q4 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <td class="p-1 border-r border-purple-300 bg-purple-100/50"></td>
                    <!-- Balance (Blank) -->
                    <td class="p-1 border-r border-slate-200 bg-slate-100/60"></td>
                    <!-- Col 22: Contextual Actions / Insert (Sticky Right) -->
                    <td class="sticky right-0 z-20 p-1 text-center bg-slate-100 align-middle shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)] border-l border-slate-300 min-w-[210px] w-[210px]">
                        <div class="grid grid-cols-2 gap-1 w-full max-w-[200px] mx-auto">
                            <button type="button" onclick="addActivityRow('', this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-[10px] rounded border border-indigo-200 shadow-2xs transition whitespace-nowrap text-center" title="Add an Activity under this Program">
                                + Activity
                            </button>
                            <button type="button" onclick="addSubactivityRow('', this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-[10px] rounded border border-amber-200 shadow-2xs transition whitespace-nowrap text-center" title="Add a Sub-activity under this Program">
                                + Sub-act
                            </button>
                            <button type="button" onclick="addIndicatorRow(null, this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-[10px] rounded border border-emerald-200 shadow-2xs transition whitespace-nowrap text-center" title="Add an Indicator Row under this Program">
                                + Indicator
                            </button>
                            <button type="button" onclick="removeRow(this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[10px] rounded border border-rose-200 shadow-2xs transition whitespace-nowrap text-center" title="Remove Program Row">
                                ✕ Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;

            const newRow = insertRow(html, insertAfterRow);
            const input = newRow.querySelector('.program-input');
            const hidden = newRow.querySelector('.program-id-hidden');
            if (input) {
                autoResizeTextarea(input);
                setTimeout(() => autoResizeTextarea(input), 30);
                input.addEventListener('input', function() {
                    if (hidden) hidden.value = '';
                    syncAllHierarchies();
                });
            }
            initProgramAutocomplete(newRow);
            syncAllHierarchies();
        }

        // 2. ACTIVITY ROW: Dedicated structural row with NOTHING in Indicator & Target columns
        function addActivityRow(actName = '', insertAfterRow = null) {
            const rowId = 'act_row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

            const html = `
                <tr class="activity-row bg-slate-50 border-b border-slate-200 font-semibold select-none" id="${rowId}">
                    <!-- Col 1 & 2: Activity Title (Bleeds across Key Indicators column with full auto-wrapping) -->
                    <td colspan="2" class="p-1 px-2 border-r border-slate-200 border-l-4 border-l-indigo-500 align-middle">
                        <div class="relative w-full pl-3">
                            <textarea rows="1" class="activity-input sheet-cell-input sheet-cell-textarea text-xs font-bold text-slate-800 px-2 py-1 placeholder:text-slate-400 placeholder:font-normal w-full"
                                placeholder="Type Activity title..." autocomplete="off"
                                oninput="autoResizeTextarea(this)">${escapeHtml(actName)}</textarea>
                            <div class="autocomplete-dropdown absolute left-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-[60] max-h-64 overflow-y-auto hidden min-w-[360px] w-full"></div>
                        </div>
                    </td>
                    <!-- Col 3: Unit (Blank) -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <!-- Col 4: Annual Target (Blank) -->
                    <td class="p-1 border-r border-slate-300 bg-amber-50/20"></td>
                    <!-- Q1 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-emerald-300 bg-emerald-50/60"></td>
                    <!-- Q2 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-sky-300 bg-sky-50/60"></td>
                    <!-- Q3 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-amber-300 bg-amber-50/60"></td>
                    <!-- Q4 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <td class="p-1 border-r border-purple-300 bg-purple-50/60"></td>
                    <!-- Balance (Blank) -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/70"></td>
                    <!-- Col 22: Contextual Actions / Insert (Sticky Right) -->
                    <td class="sticky right-0 z-20 p-1 text-center bg-slate-50 align-middle shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)] border-l border-slate-200 min-w-[210px] w-[210px]">
                        <div class="grid grid-cols-2 gap-1 w-full max-w-[200px] mx-auto">
                            <button type="button" onclick="addActivityRow('', this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-[10px] rounded border border-indigo-200 shadow-2xs transition whitespace-nowrap text-center" title="Add another consecutive Activity">
                                + Activity
                            </button>
                            <button type="button" onclick="addSubactivityRow('', this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-[10px] rounded border border-amber-200 shadow-2xs transition whitespace-nowrap text-center" title="Add a Sub-activity under this Activity">
                                + Sub-act
                            </button>
                            <button type="button" onclick="addIndicatorRow(null, this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-[10px] rounded border border-emerald-200 shadow-2xs transition whitespace-nowrap text-center" title="Add an Indicator Row under this Activity">
                                + Indicator
                            </button>
                            <button type="button" onclick="removeRow(this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[10px] rounded border border-rose-200 shadow-2xs transition whitespace-nowrap text-center" title="Remove Activity Row">
                                ✕ Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;

            const newRow = insertRow(html, insertAfterRow);
            const input = newRow.querySelector('.activity-input');
            if (input) {
                autoResizeTextarea(input);
                setTimeout(() => autoResizeTextarea(input), 30);
                input.addEventListener('input', function() {
                    syncAllHierarchies();
                });
            }
            initActivityAutocomplete(newRow);
            syncAllHierarchies();
        }

        // 3. SUB-ACTIVITY ROW: Dedicated structural row with NOTHING in Indicator & Target columns
        function addSubactivityRow(subActName = '', insertAfterRow = null) {
            const rowId = 'subact_row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);

            const html = `
                <tr class="subactivity-row bg-slate-50/50 border-b border-slate-200 select-none" id="${rowId}">
                    <!-- Col 1 & 2: Sub-activity Title (Bleeds across Key Indicators column with full auto-wrapping) -->
                    <td colspan="2" class="p-1 px-2 border-r border-slate-200 border-l-4 border-l-amber-500 align-middle">
                        <div class="relative w-full pl-6">
                            <textarea rows="1" class="subactivity-input sheet-cell-input sheet-cell-textarea text-xs font-semibold text-slate-700 px-2 py-1 placeholder:text-slate-400 placeholder:font-normal w-full"
                                placeholder="Type sub-activity or component title..." autocomplete="off"
                                oninput="autoResizeTextarea(this)">${escapeHtml(subActName)}</textarea>
                        </div>
                    </td>
                    <!-- Col 3: Unit (Blank) -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <!-- Col 4: Annual Target (Blank) -->
                    <td class="p-1 border-r border-slate-300 bg-amber-50/10"></td>
                    <!-- Q1 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-emerald-300 bg-emerald-50/40"></td>
                    <!-- Q2 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-sky-300 bg-sky-50/40"></td>
                    <!-- Q3 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-amber-300 bg-amber-50/40"></td>
                    <!-- Q4 Months & Total -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <td class="p-1 border-r border-purple-300 bg-purple-50/40"></td>
                    <!-- Balance (Blank) -->
                    <td class="p-1 border-r border-slate-200 bg-slate-50/40"></td>
                    <!-- Col 22: Contextual Actions / Insert (Sticky Right) -->
                    <td class="sticky right-0 z-20 p-1 text-center bg-slate-50 align-middle shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)] border-l border-slate-200 min-w-[210px] w-[210px]">
                        <div class="grid grid-cols-2 gap-1 w-full max-w-[200px] mx-auto">
                            <button type="button" onclick="addSubactivityRow('', this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-[10px] rounded border border-amber-200 shadow-2xs transition whitespace-nowrap text-center" title="Add another consecutive Sub-activity">
                                + Sub-act
                            </button>
                            <button type="button" onclick="addActivityRow('', this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-[10px] rounded border border-indigo-200 shadow-2xs transition whitespace-nowrap text-center" title="Add an Activity row directly below this Sub-activity">
                                + Activity
                            </button>
                            <button type="button" onclick="addIndicatorRow(null, this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-[10px] rounded border border-emerald-200 shadow-2xs transition whitespace-nowrap text-center" title="Add an Indicator Row under this Sub-activity">
                                + Indicator
                            </button>
                            <button type="button" onclick="removeRow(this.closest('tr'))"
                                class="px-1.5 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[10px] rounded border border-rose-200 shadow-2xs transition whitespace-nowrap text-center" title="Remove Sub-activity Row">
                                ✕ Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;

            const newRow = insertRow(html, insertAfterRow);
            const input = newRow.querySelector('.subactivity-input');
            if (input) {
                autoResizeTextarea(input);
                setTimeout(() => autoResizeTextarea(input), 30);
                input.addEventListener('input', function() {
                    syncAllHierarchies();
                });
            }
            syncAllHierarchies();
        }

        // 4. INDICATOR ROW: Sleek In-Cell Spreadsheet Row with Direct Cell Inputs & No Redundant Labels
        function addIndicatorRow(planData = null, insertAfterRow = null) {
            const idx = totalRows++;

            // Resolve values dynamically from planData or DOM context
            let papId = planData ? planData.pap_id : '';
            let programName = planData ? (planData.program_name || (planData.pap ? (planData.pap.category || planData.pap.name) : '')) : '';
            let activity = planData ? (planData.activity || (planData.performance_indicator ? planData.performance_indicator
                .activity : '')) : '';
            let subProgram = planData ? (planData.sub_program || (planData.performance_indicator ? planData
                .performance_indicator.sub_program : '')) : '';
            let subActivity = planData ? (planData.sub_activity || (planData.performance_indicator ? planData
                .performance_indicator.sub_activity : '')) : '';
            let indicatorId = planData ? planData.performance_indicator_id : '';
            let indicatorName = planData ? (planData.indicator_name || (planData.performance_indicator ? planData
                .performance_indicator.name : '')) : '';
            let indicatorCode = planData ? (planData.indicator_code || (planData.performance_indicator ? planData
                .performance_indicator.code : '')) : '';
            let unit = planData ? (planData.unit_of_measure || (planData.performance_indicator ? planData
                .performance_indicator.unit_of_measure : '')) : '';
            let annualTarget = planData ? parseFloat(planData.annual_target || 0) : 0;
            let planId = planData ? (planData.id || '') : '';

            const m = [];
            for (let i = 1; i <= 12; i++) {
                m[i] = planData ? parseFloat(planData[`m${i}_target`] || 0) : 0;
            }

            const html = `
                <tr class="data-row hover:bg-slate-50/90 transition border-b border-slate-200" id="row_${idx}" data-index="${idx}" data-plan-id="${planId}">
                    <!-- Col 1: Specific Process Step / Task Breakdown -->
                    <td class="p-1 border-r border-slate-200 align-middle min-w-[320px] max-w-[400px] w-[350px]">
                        <div class="w-full pl-10 pr-1 py-0.5">
                            <textarea rows="1" name="plans[${idx}][sub_activity]" id="sub_activity_${idx}"
                                placeholder="Specific task or process step (optional)..."
                                class="sheet-cell-input sheet-cell-textarea text-xs font-medium text-slate-800 px-2 py-1 placeholder:text-slate-400 w-full"
                                oninput="autoResizeTextarea(this)">${escapeHtml(subActivity)}</textarea>
                            <input type="hidden" name="plans[${idx}][pap_id]" id="pap_id_${idx}" value="${papId}">
                            <input type="hidden" name="plans[${idx}][program_name]" id="program_name_${idx}" value="${escapeHtml(programName)}">
                            <input type="hidden" name="plans[${idx}][activity]" id="activity_${idx}" value="${escapeHtml(activity)}">
                            <input type="hidden" name="plans[${idx}][sub_program]" id="sub_program_${idx}" value="${escapeHtml(subProgram)}">
                        </div>
                    </td>

                    <!-- Col 2: Key Indicators (STRICTLY ON INDICATOR ROWS!) -->
                    <td class="p-1 border-r border-slate-200 align-middle min-w-[280px] max-w-[360px] w-[320px] relative">
                        <div class="w-full py-0.5">
                            <textarea rows="1" name="plans[${idx}][indicator_name]" id="indicator_search_${idx}"
                                placeholder="Type Key Performance Indicator..."
                                autocomplete="off"
                                class="sheet-cell-input sheet-cell-textarea text-xs font-bold text-slate-900 px-2.5 py-1 placeholder:text-slate-400 w-full"
                                oninput="autoResizeTextarea(this)">${escapeHtml(indicatorName)}</textarea>
                            <input type="hidden" name="plans[${idx}][performance_indicator_id]" id="indicator_id_${idx}" value="${indicatorId}">
                            <input type="hidden" name="plans[${idx}][indicator_code]" id="indicator_code_${idx}" value="${escapeHtml(indicatorCode)}">
                            <div id="indicator_dropdown_${idx}" class="autocomplete-dropdown absolute left-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-[60] max-h-64 overflow-y-auto hidden min-w-[360px] w-full"></div>
                        </div>
                    </td>

                    <!-- Col 3: Unit -->
                    <td class="p-0 border-r border-slate-200 text-center align-middle bg-slate-50/40 min-w-[65px] w-[65px]">
                        <input type="text" name="plans[${idx}][unit_of_measure]" id="unit_${idx}" value="${escapeHtml(unit)}"
                            class="sheet-cell-input text-center font-bold text-slate-700 py-1.5 text-xs">
                    </td>

                    <!-- Col 4: Annual Target -->
                    <td class="p-0 border-r border-slate-300 align-middle bg-amber-50/30 text-center min-w-[95px] w-[95px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][annual_target]"
                            id="annual_${idx}" value="${annualTarget}" required
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-cell-input-amber sheet-number-input text-right pr-2 font-mono font-black text-slate-900 py-1.5 text-xs">
                    </td>

                    <!-- Q1 Months -->
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m1_target]" id="m1_${idx}" value="${m[1]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m2_target]" id="m2_${idx}" value="${m[2]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-200 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m3_target]" id="m3_${idx}" value="${m[3]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-emerald-200 text-center align-middle bg-emerald-50/70 min-w-[65px] w-[65px]">
                        <span id="q1_badge_${idx}" class="font-mono font-extrabold text-xs text-emerald-950 block py-1.5">0.00</span>
                    </td>

                    <!-- Q2 Months -->
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m4_target]" id="m4_${idx}" value="${m[4]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m5_target]" id="m5_${idx}" value="${m[5]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-200 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m6_target]" id="m6_${idx}" value="${m[6]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-sky-200 text-center align-middle bg-sky-50/70 min-w-[65px] w-[65px]">
                        <span id="q2_badge_${idx}" class="font-mono font-extrabold text-xs text-sky-950 block py-1.5">0.00</span>
                    </td>

                    <!-- Q3 Months -->
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m7_target]" id="m7_${idx}" value="${m[7]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m8_target]" id="m8_${idx}" value="${m[8]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-200 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m9_target]" id="m9_${idx}" value="${m[9]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-amber-200 text-center align-middle bg-amber-50/70 min-w-[65px] w-[65px]">
                        <span id="q3_badge_${idx}" class="font-mono font-extrabold text-xs text-amber-950 block py-1.5">0.00</span>
                    </td>

                    <!-- Q4 Months -->
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m10_target]" id="m10_${idx}" value="${m[10]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-100 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m11_target]" id="m11_${idx}" value="${m[11]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-slate-200 text-center align-middle min-w-[60px] w-[60px]">
                        <input type="number" step="0.01" min="0" name="plans[${idx}][m12_target]" id="m12_${idx}" value="${m[12]}"
                            oninput="recalcRow(${idx})"
                            class="sheet-cell-input sheet-number-input text-center font-mono font-semibold text-slate-800 py-1.5 text-xs">
                    </td>
                    <td class="p-0 border-r border-purple-200 text-center align-middle bg-purple-50/70 min-w-[65px] w-[65px]">
                        <span id="q4_badge_${idx}" class="font-mono font-extrabold text-xs text-purple-950 block py-1.5">0.00</span>
                    </td>

                    <!-- Balance Status -->
                    <td class="p-1 border-r border-slate-200 text-center align-middle min-w-[90px] w-[90px]">
                        <span id="balance_badge_${idx}" class="text-[10px] font-black px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 inline-block w-full text-center border border-slate-200">
                            0 / 0
                        </span>
                    </td>

                    <!-- Presets & Actions (Sticky Right) -->
                    <td class="sticky right-0 z-10 p-1 text-center align-middle bg-white shadow-[-4px_0_6px_-2px_rgba(0,0,0,0.08)] border-l border-slate-200 min-w-[210px] w-[210px]">
                        <div class="grid grid-cols-2 gap-1 w-full max-w-[200px] mx-auto">
                            <button type="button" onclick="distributeEvenly(${idx})"
                                class="px-1.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded border border-emerald-200 shadow-2xs transition whitespace-nowrap text-center" title="Distribute Evenly across 12 months">
                                ⚡Even
                            </button>
                            <button type="button" onclick="distributeQuarterly(${idx})"
                                class="px-1.5 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 font-extrabold text-[10px] rounded border border-indigo-200 shadow-2xs transition whitespace-nowrap text-center" title="Distribute Quarterly">
                                ⚡Qtr
                            </button>
                            <!-- In-between Insert Dropdown -->
                            <div class="relative w-full text-left">
                                <button type="button" onclick="toggleRowInsertMenu(this)"
                                    class="w-full px-1.5 py-0.5 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 font-extrabold text-[10px] rounded border border-slate-300 transition flex items-center justify-center gap-0.5 shadow-2xs whitespace-nowrap" title="Insert a new row directly below this row">
                                    <span>+ Insert</span>
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div class="row-insert-menu hidden absolute right-0 mt-1 w-44 bg-white rounded-xl shadow-xl border border-slate-200 py-1 z-50 text-left text-xs font-bold divide-y divide-slate-100">
                                    <button type="button" onclick="addIndicatorRow(null, this.closest('tr')); closeAllInsertMenus();" class="w-full px-3 py-1.5 text-left text-emerald-800 hover:bg-emerald-50 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        <span>+ Indicator Row</span>
                                    </button>
                                    <button type="button" onclick="addActivityRow('', this.closest('tr')); closeAllInsertMenus();" class="w-full px-3 py-1.5 text-left text-indigo-700 hover:bg-indigo-50 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                        <span>+ Activity Header</span>
                                    </button>
                                    <button type="button" onclick="addSubactivityRow('', this.closest('tr')); closeAllInsertMenus();" class="w-full px-3 py-1.5 text-left text-amber-700 hover:bg-amber-50 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                                        <span>+ Sub-activity Header</span>
                                    </button>
                                    <button type="button" onclick="addProgramRow('', '', this.closest('tr')); closeAllInsertMenus();" class="w-full px-3 py-1.5 text-left text-purple-700 hover:bg-purple-50 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                        <span>+ Program / PAP</span>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="removeSpreadsheetRow(${idx})"
                                class="px-1.5 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[10px] rounded border border-rose-200 shadow-2xs transition whitespace-nowrap text-center" title="Delete this row">
                                ✕ Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;

            const newRow = insertRow(html, insertAfterRow);
            const subActEl = document.getElementById(`sub_activity_${idx}`);
            const indSearchEl = document.getElementById(`indicator_search_${idx}`);
            if (subActEl) autoResizeTextarea(subActEl);
            if (indSearchEl) autoResizeTextarea(indSearchEl);
            setTimeout(() => {
                if (subActEl) autoResizeTextarea(subActEl);
                if (indSearchEl) autoResizeTextarea(indSearchEl);
            }, 30);

            initIndicatorAutocomplete(idx);
            syncAllHierarchies();
            recalcRow(idx);
            updateSummaryCounters();
        }

        // Generic Autocomplete Engine
        function attachCombobox(inputEl, dropdownEl, getItemsFn, onSelectFn) {
            function positionDropdown() {
                const rect = inputEl.getBoundingClientRect();
                const container = document.getElementById('tableScrollContainer');
                const contRect = container ? container.getBoundingClientRect() : { bottom: window.innerHeight, top: 0 };
                const spaceBelow = Math.min(window.innerHeight, contRect.bottom) - rect.bottom;

                if (spaceBelow < 220 && (rect.top - contRect.top) > 200) {
                    dropdownEl.classList.remove('top-full', 'mt-1');
                    dropdownEl.classList.add('bottom-full', 'mb-1');
                } else {
                    dropdownEl.classList.remove('bottom-full', 'mb-1');
                    dropdownEl.classList.add('top-full', 'mt-1');
                }
            }

            function renderDropdown(items, query = '') {
                dropdownEl.innerHTML = '';
                if (!items || items.length === 0) {
                    dropdownEl.classList.add('hidden');
                    return;
                }

                const recommended = items.filter(i => i.isRecommended);
                const others = items.filter(i => !i.isRecommended);

                if (recommended.length > 0) {
                    const header = document.createElement('div');
                    header.className =
                        'px-3 py-1.5 bg-amber-50 text-[10px] font-black text-amber-800 uppercase tracking-wider border-b border-amber-100 sticky top-0 z-10 shadow-xs';
                    header.innerText = '⭐ Recommended for Your Division';
                    dropdownEl.appendChild(header);

                    recommended.forEach(item => {
                        dropdownEl.appendChild(createItemEl(item));
                    });
                }

                if (others.length > 0) {
                    if (recommended.length > 0) {
                        const header = document.createElement('div');
                        header.className =
                            'px-3 py-1.5 bg-slate-50 text-[10px] font-black text-slate-500 uppercase tracking-wider border-b border-slate-100 sticky top-0 z-10 shadow-xs';
                        header.innerText = 'Other Bureau Options';
                        dropdownEl.appendChild(header);
                    }
                    others.forEach(item => {
                        dropdownEl.appendChild(createItemEl(item));
                    });
                }

                positionDropdown();
                dropdownEl.classList.remove('hidden');
            }

            function createItemEl(item) {
                const el = document.createElement('div');
                el.className =
                    'px-3 py-2 text-xs text-slate-800 hover:bg-emerald-50 hover:text-emerald-950 cursor-pointer transition border-b border-slate-50 last:border-b-0 flex items-center justify-between gap-2';
                el.innerHTML = `
                    <div class="truncate">
                        <span class="font-bold">${item.isRecommended ? '⭐ ' : ''}${escapeHtml(item.label)}</span>
                        ${item.sublabel ? `<span class="text-[10px] text-slate-400 block truncate">${escapeHtml(item.sublabel)}</span>` : ''}
                    </div>
                `;
                el.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    inputEl.value = item.label;
                    autoResizeTextarea(inputEl);
                    onSelectFn(item);
                    dropdownEl.classList.add('hidden');
                });
                return el;
            }

            inputEl.addEventListener('focus', function() {
                const query = inputEl.value.trim().toLowerCase();
                const allItems = getItemsFn();
                const filtered = allItems.filter(i => i.label.toLowerCase().includes(query));
                renderDropdown(filtered, query);
            });

            inputEl.addEventListener('input', function() {
                const query = inputEl.value.trim().toLowerCase();
                const allItems = getItemsFn();
                const filtered = allItems.filter(i => i.label.toLowerCase().includes(query));
                renderDropdown(filtered, query);
            });

            inputEl.addEventListener('blur', function() {
                setTimeout(() => {
                    dropdownEl.classList.add('hidden');
                }, 180);
            });
        }

        // Program Autocomplete in Program Row
        function initProgramAutocomplete(rowEl) {
            const input = rowEl.querySelector('.program-input');
            const dropdown = rowEl.querySelector('.autocomplete-dropdown');
            const hidden = rowEl.querySelector('.program-id-hidden');

            attachCombobox(
                input,
                dropdown,
                function() {
                    const seen = new Map();
                    bureauCatalog.forEach(p => {
                        const catName = p.category || p.name;
                        const isRec = p.operating_unit_ids && p.operating_unit_ids.includes(currentOuId);
                        if (!seen.has(catName)) {
                            seen.set(catName, {
                                id: p.id,
                                label: catName,
                                sublabel: '',
                                isRecommended: isRec,
                                raw: p,
                                paps: [p]
                            });
                        } else {
                            const existing = seen.get(catName);
                            existing.paps.push(p);
                            if (isRec) {
                                existing.isRecommended = true;
                                existing.id = p.id;
                                existing.raw = p;
                            }
                        }
                    });
                    return Array.from(seen.values());
                },
                function(selected) {
                    let chosenPap = selected.raw;
                    if (selected.paps && selected.paps.length > 1) {
                        const ouMatch = selected.paps.find(p => p.operating_unit_ids && p.operating_unit_ids.includes(
                            currentOuId));
                        if (ouMatch) chosenPap = ouMatch;
                    }
                    hidden.value = chosenPap ? chosenPap.id : selected.id;
                    syncAllHierarchies();
                }
            );

            input.addEventListener('input', function() {
                syncAllHierarchies();
            });
        }

        // Activity Autocomplete in Activity Row
        function initActivityAutocomplete(rowEl) {
            const input = rowEl.querySelector('.activity-input');
            const dropdown = rowEl.querySelector('.autocomplete-dropdown');

            attachCombobox(
                input,
                dropdown,
                function() {
                    const parentProg = getPrecedingProgram(rowEl);
                    let papsToSearch = [];
                    if (parentProg && parentProg.id) {
                        const p = bureauCatalog.find(item => String(item.id) === String(parentProg.id));
                        if (p) papsToSearch = [p];
                    } else if (parentProg && parentProg.name) {
                        papsToSearch = bureauCatalog.filter(item => (item.category || item.name).toLowerCase() ===
                            parentProg.name.toLowerCase());
                    }

                    if (papsToSearch.length > 0) {
                        const acts = [];
                        const seenAct = new Set();
                        papsToSearch.forEach(p => {
                            (p.activities || []).forEach(a => {
                                if (!seenAct.has(a.name)) {
                                    seenAct.add(a.name);
                                    acts.push({
                                        label: a.name,
                                        sublabel: a.sub_program || '',
                                        isRecommended: a.operating_unit_ids && a
                                            .operating_unit_ids.includes(currentOuId),
                                        parentPap: p,
                                        raw: a
                                    });
                                }
                            });
                        });
                        return acts;
                    }

                    const allActs = [];
                    const seen = new Set();
                    bureauCatalog.forEach(p => {
                        (p.activities || []).forEach(a => {
                            if (!seen.has(a.name)) {
                                seen.add(a.name);
                                allActs.push({
                                    label: a.name,
                                    sublabel: `${p.category || p.name}${a.sub_program ? ' · ' + a.sub_program : ''}`,
                                    isRecommended: a.operating_unit_ids && a.operating_unit_ids
                                        .includes(currentOuId),
                                    parentPap: p,
                                    raw: a
                                });
                            }
                        });
                    });
                    return allActs;
                },
                function(selected) {
                    input.value = selected.label;
                    syncAllHierarchies();
                }
            );

            input.addEventListener('input', function() {
                syncAllHierarchies();
            });
        }

        // Indicator Autocomplete in Indicator Row
        function initIndicatorAutocomplete(idx) {
            const indInput = document.getElementById(`indicator_search_${idx}`);
            const indDropdown = document.getElementById(`indicator_dropdown_${idx}`);
            const indIdHidden = document.getElementById(`indicator_id_${idx}`);
            const indCodeHidden = document.getElementById(`indicator_code_${idx}`);
            const unitInput = document.getElementById(`unit_${idx}`);

            attachCombobox(
                indInput,
                indDropdown,
                function() {
                    const row = document.getElementById(`row_${idx}`);
                    const parentProg = getPrecedingProgram(row);
                    const parentAct = getPrecedingActivity(row);

                    if (parentProg && parentProg.id && parentAct) {
                        const pap = bureauCatalog.find(p => String(p.id) === String(parentProg.id));
                        if (pap && pap.activities) {
                            const act = pap.activities.find(a => a.name === parentAct);
                            if (act && act.indicators && act.indicators.length > 0) {
                                return act.indicators.map(i => ({
                                    id: i.id,
                                    label: i.name,
                                    sublabel: `Unit: ${i.unit || 'Units'}`,
                                    isRecommended: i.operating_unit_id === currentOuId,
                                    raw: i
                                }));
                            }
                        }
                    }

                    if (parentProg && parentProg.id) {
                        const pap = bureauCatalog.find(p => String(p.id) === String(parentProg.id));
                        if (pap && pap.activities) {
                            const inds = [];
                            pap.activities.forEach(a => {
                                (a.indicators || []).forEach(i => {
                                    inds.push({
                                        id: i.id,
                                        label: i.name,
                                        sublabel: `${a.name} · Unit: ${i.unit || 'Units'}`,
                                        isRecommended: i.operating_unit_id === currentOuId,
                                        parentAct: a.name,
                                        parentSubProg: a.sub_program,
                                        raw: i
                                    });
                                });
                            });
                            return inds;
                        }
                    }

                    const allInds = [];
                    bureauCatalog.forEach(p => {
                        (p.activities || []).forEach(a => {
                            (a.indicators || []).forEach(i => {
                                allInds.push({
                                    id: i.id,
                                    label: i.name,
                                    sublabel: `${p.category || p.name} · ${a.name}`,
                                    isRecommended: i.operating_unit_id === currentOuId,
                                    parentPap: p,
                                    parentAct: a.name,
                                    parentSubProg: a.sub_program,
                                    raw: i
                                });
                            });
                        });
                    });
                    return allInds;
                },
                function(selected) {
                    indIdHidden.value = selected.id;
                    indCodeHidden.value = selected.raw.code || '';
                    if (unitInput) unitInput.value = selected.raw.unit || 'number';

                    const row = document.getElementById(`row_${idx}`);
                    syncAllHierarchies();
                }
            );

            indInput.addEventListener('input', function() {
                indIdHidden.value = ''; // custom indicator
            });
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
                        'text-[10px] font-black px-1.5 py-1.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 inline-block w-full text-center';
                    balanceBadge.innerText = `✓ ${total.toFixed(0)} / ${annual.toFixed(0)}`;
                } else if (annual === 0) {
                    balanceBadge.className =
                        'text-[10px] font-black px-1.5 py-1.5 rounded bg-slate-100 text-slate-500 inline-block w-full text-center border border-slate-200';
                    balanceBadge.innerText = `0 / 0`;
                } else {
                    balanceBadge.className =
                        'text-[10px] font-black px-1.5 py-1.5 rounded bg-amber-100 text-amber-900 border border-amber-300 inline-block w-full text-center';
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

        function removeSpreadsheetRow(idx) {
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
                            syncAllHierarchies();
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
                syncAllHierarchies();
                updateSummaryCounters();
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
            if (summaryEl) summaryEl.innerText = `${rows.length} Commitments (${configuredCount} with Targets)`;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
@endsection
