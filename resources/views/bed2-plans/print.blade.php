<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DBM BED Form No. 2 - Physical Plan FY {{ $year }} ({{ $operatingUnit->code }})</title>
    <link rel="icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Tinos:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .serif-official {
            font-family: 'Tinos', 'Times New Roman', serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-after: always;
            }
            @page {
                size: A4 landscape;
                margin: 10mm 8mm 10mm 8mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen py-8 px-4 antialiased">

    <!-- Floating Top Action Bar (Hidden on Print) -->
    <div class="no-print max-w-[1300px] mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-md">
        <div class="flex items-center gap-3">
            <a href="{{ route('bed2-plans.index', ['year' => $year, 'operating_unit_id' => $operatingUnit->id]) }}"
                class="px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-bold text-slate-700 transition flex items-center gap-1.5">
                <span>&larr;</span> Back to Target Matrix
            </a>
            <div class="h-6 w-px bg-slate-200"></div>
            <div>
                <p class="text-xs font-extrabold text-slate-900">Official Government Printout Preview</p>
                <p class="text-[10px] text-slate-400">DBM BED Form No. 2 &bull; Ready for A4 Landscape physical printing or PDF archival.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('bed2-plans.export.csv', ['year' => $year, 'operating_unit_id' => $operatingUnit->id]) }}"
                class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Download CSV / Excel</span>
            </a>

            <button type="button" onclick="window.print()"
                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Print Official BED-2 Form</span>
            </button>
        </div>
    </div>

    <!-- Official DBM Document Container -->
    <div class="max-w-[1300px] mx-auto bg-white border border-slate-300 shadow-xl print:shadow-none print:border-none p-8 print:p-0">

        <!-- DBM Document Header -->
        <div class="border-b-2 border-slate-900 pb-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png" alt="BPI Logo" class="w-16 h-16 object-contain">
                    <div class="serif-official">
                        <p class="text-[11px] uppercase tracking-wider text-slate-700 leading-tight">Republic of the Philippines</p>
                        <p class="text-sm font-bold uppercase tracking-wide text-slate-900 leading-tight">Department of Agriculture</p>
                        <p class="text-base font-bold uppercase tracking-wide text-emerald-900 leading-tight">Bureau of Plant Industry</p>
                        <p class="text-[10px] text-slate-500 italic mt-0.5">{{ $operatingUnit->location }}</p>
                    </div>
                </div>

                <div class="text-right">
                    <div class="inline-block border-2 border-slate-900 px-3 py-1 font-black text-xs uppercase tracking-widest bg-slate-50">
                        DBM BED FORM NO. 2
                    </div>
                    <p class="text-[9px] uppercase font-bold text-slate-500 mt-1">Physical Plan Matrix</p>
                    <p class="text-[9px] text-slate-400">Generated on {{ date('F d, Y g:i A') }}</p>
                </div>
            </div>

            <div class="text-center pt-2">
                <h1 class="text-lg font-black uppercase tracking-wider text-slate-900">ANNUAL PHYSICAL PLAN</h1>
                <p class="text-xs font-bold text-slate-600 uppercase tracking-widest">Fiscal Year {{ $year }}</p>
            </div>
        </div>

        <!-- Entity Metadata Header Grid -->
        <div class="grid grid-cols-2 gap-4 text-xs mb-5 border border-slate-300 p-3 bg-slate-50/50">
            <div class="space-y-1">
                <div class="flex">
                    <span class="w-32 font-bold text-slate-600">Department:</span>
                    <span class="font-extrabold text-slate-900">Department of Agriculture (DA)</span>
                </div>
                <div class="flex">
                    <span class="w-32 font-bold text-slate-600">Agency:</span>
                    <span class="font-extrabold text-slate-900">Bureau of Plant Industry (BPI)</span>
                </div>
                <div class="flex">
                    <span class="w-32 font-bold text-slate-600">Operating Unit:</span>
                    <span class="font-extrabold text-emerald-900">{{ $operatingUnit->name }}</span>
                </div>
            </div>

            <div class="space-y-1 text-right sm:text-left sm:pl-6 border-t sm:border-t-0 sm:border-l border-slate-200">
                <div class="flex sm:justify-start justify-end">
                    <span class="w-32 font-bold text-slate-600 text-left">Unit Code:</span>
                    <span class="font-mono font-extrabold text-slate-900">{{ $operatingUnit->code }}</span>
                </div>
                <div class="flex sm:justify-start justify-end">
                    <span class="w-32 font-bold text-slate-600 text-left">Funding Source:</span>
                    <span class="font-extrabold text-slate-900">Regular General Appropriations Act (GAA)</span>
                </div>
                <div class="flex sm:justify-start justify-end">
                    <span class="w-32 font-bold text-slate-600 text-left">Plan Status:</span>
                    <span class="font-extrabold text-slate-900 uppercase">
                        {{ $latestApprovedPlan && $latestApprovedPlan->isApproved() ? 'Approved Official Baseline' : 'Under Review / Draft' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Official Matrix Table -->
        <table class="w-full text-left text-[10px] border-collapse border border-slate-300">
            <thead>
                <tr class="bg-slate-200/90 text-slate-900 font-extrabold text-center border-b border-slate-300">
                    <th rowspan="2" class="p-2 border border-slate-300 w-1/4 text-left">Programs / Activities / Projects (PAP) &amp; Performance Indicators</th>
                    <th rowspan="2" class="p-2 border border-slate-300 w-12">Unit</th>
                    <th colspan="4" class="p-1 border border-slate-300 bg-slate-100">1st Quarter</th>
                    <th colspan="4" class="p-1 border border-slate-300 bg-slate-100">2nd Quarter</th>
                    <th colspan="4" class="p-1 border border-slate-300 bg-slate-100">3rd Quarter</th>
                    <th colspan="4" class="p-1 border border-slate-300 bg-slate-100">4th Quarter</th>
                    <th rowspan="2" class="p-2 border border-slate-300 bg-emerald-50 text-emerald-950 font-black w-20">Full Year Target</th>
                </tr>
                <tr class="bg-slate-100 text-slate-700 font-bold text-center border-b border-slate-300 text-[9px]">
                    <!-- Q1 -->
                    <th class="p-1 border border-slate-300 w-8">M1</th>
                    <th class="p-1 border border-slate-300 w-8">M2</th>
                    <th class="p-1 border border-slate-300 w-8">M3</th>
                    <th class="p-1 border border-slate-300 w-10 bg-slate-200 font-extrabold text-slate-900">Q1</th>
                    <!-- Q2 -->
                    <th class="p-1 border border-slate-300 w-8">M4</th>
                    <th class="p-1 border border-slate-300 w-8">M5</th>
                    <th class="p-1 border border-slate-300 w-8">M6</th>
                    <th class="p-1 border border-slate-300 w-10 bg-slate-200 font-extrabold text-slate-900">Q2</th>
                    <!-- Q3 -->
                    <th class="p-1 border border-slate-300 w-8">M7</th>
                    <th class="p-1 border border-slate-300 w-8">M8</th>
                    <th class="p-1 border border-slate-300 w-8">M9</th>
                    <th class="p-1 border border-slate-300 w-10 bg-slate-200 font-extrabold text-slate-900">Q3</th>
                    <!-- Q4 -->
                    <th class="p-1 border border-slate-300 w-8">M10</th>
                    <th class="p-1 border border-slate-300 w-8">M11</th>
                    <th class="p-1 border border-slate-300 w-8">M12</th>
                    <th class="p-1 border border-slate-300 w-10 bg-slate-200 font-extrabold text-slate-900">Q4</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plansByPap as $papId => $group)
                    @php $pap = $group->first()->pap; @endphp
                    <!-- Program PAP Header Row -->
                    <tr class="bg-slate-100 font-extrabold text-slate-900 border-t border-b border-slate-300">
                        <td colspan="19" class="p-2 border border-slate-300">
                            <span class="text-emerald-800 font-mono">{{ $pap?->code }}</span> &mdash; {{ $pap?->name }}
                        </td>
                    </tr>

                    @foreach ($group as $plan)
                        <tr class="border-b border-slate-200 hover:bg-slate-50/50">
                            <!-- Indicator Column -->
                            <td class="p-2 border border-slate-300 pl-5">
                                <div class="font-bold text-slate-900">{{ $plan->performanceIndicator?->name }}</div>
                                <div class="text-[9px] font-mono text-slate-500">
                                    {{ $plan->performanceIndicator?->code }} &bull; {{ strtoupper($plan->performanceIndicator?->type ?? 'OUTPUT') }}
                                </div>
                            </td>

                            <!-- Unit -->
                            <td class="p-1 border border-slate-300 text-center font-medium text-slate-600">
                                {{ $plan->performanceIndicator?->unit_of_measure }}
                            </td>

                            <!-- Q1 Breakdown -->
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m1_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m2_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m3_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono font-bold bg-slate-50 text-slate-900">{{ number_format($plan->q1_target, 0) }}</td>

                            <!-- Q2 Breakdown -->
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m4_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m5_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m6_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono font-bold bg-slate-50 text-slate-900">{{ number_format($plan->q2_target, 0) }}</td>

                            <!-- Q3 Breakdown -->
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m7_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m8_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m9_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono font-bold bg-slate-50 text-slate-900">{{ number_format($plan->q3_target, 0) }}</td>

                            <!-- Q4 Breakdown -->
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m10_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m11_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono">{{ number_format($plan->m12_target, 0) }}</td>
                            <td class="p-1 border border-slate-300 text-right font-mono font-bold bg-slate-50 text-slate-900">{{ number_format($plan->q4_target, 0) }}</td>

                            <!-- Full Year Annual Target -->
                            <td class="p-2 border border-slate-300 text-right font-mono font-black text-slate-950 bg-emerald-50/70">
                                {{ number_format($plan->annual_target, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="19" class="p-8 text-center text-slate-400 italic font-medium">
                            No physical indicator targets encoded for this operating unit in FY {{ $year }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-200/90 font-black text-slate-900 border-t-2 border-slate-900 text-right">
                    <td colspan="2" class="p-2 text-left uppercase tracking-wider text-[10px]">Total Bureau Allocations:</td>
                    <td colspan="4" class="p-2 border border-slate-300 font-mono text-center">
                        <span class="text-[9px] text-slate-500 block">Q1 Target</span>
                        {{ number_format($overallQ1, 2) }}
                    </td>
                    <td colspan="4" class="p-2 border border-slate-300 font-mono text-center">
                        <span class="text-[9px] text-slate-500 block">Q2 Target</span>
                        {{ number_format($overallQ2, 2) }}
                    </td>
                    <td colspan="4" class="p-2 border border-slate-300 font-mono text-center">
                        <span class="text-[9px] text-slate-500 block">Q3 Target</span>
                        {{ number_format($overallQ3, 2) }}
                    </td>
                    <td colspan="4" class="p-2 border border-slate-300 font-mono text-center">
                        <span class="text-[9px] text-slate-500 block">Q4 Target</span>
                        {{ number_format($overallQ4, 2) }}
                    </td>
                    <td class="p-2 border border-slate-300 bg-emerald-100 font-mono font-black text-emerald-950 text-right">
                        {{ number_format($overallAnnual, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Official Signatories Certification Block -->
        <div class="mt-8 pt-4 border-t border-slate-300 grid grid-cols-3 gap-8 text-[11px] leading-tight">
            <!-- Prepared By -->
            <div>
                <p class="text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-8">Prepared by:</p>
                <p class="font-black text-slate-900 uppercase underline text-xs">
                    {{ $operatingUnit->reportingOfficer?->name ?? ($latestApprovedPlan?->submittedBy?->name ?? 'Planning Officer / OU Encoder') }}
                </p>
                <p class="text-slate-600 font-medium">
                    {{ $operatingUnit->reportingOfficer?->designation ?? 'Planning Officer / Designated Encoder' }}
                </p>
                <p class="text-slate-400 text-[9px] mt-0.5">{{ $operatingUnit->name }}</p>
            </div>

            <!-- Recommending Approval / Reviewed By -->
            <div>
                <p class="text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-8">Recommending Approval:</p>
                <p class="font-black text-slate-900 uppercase underline text-xs">
                    {{ $operatingUnit->divisionChief?->name ?? ($latestApprovedPlan?->headApprovedBy?->name ?? 'Division Chief / Center Director') }}
                </p>
                <p class="text-slate-600 font-medium">
                    {{ $operatingUnit->divisionChief?->designation ?? 'Chief, Operating Unit / Division Head' }}
                </p>
                <p class="text-slate-400 text-[9px] mt-0.5">{{ $operatingUnit->name }}</p>
            </div>

            <!-- Approved By -->
            <div>
                <p class="text-slate-500 font-bold uppercase tracking-wider text-[9px] mb-8">Approved by:</p>
                <p class="font-black text-slate-900 uppercase underline text-xs">
                    {{ $latestApprovedPlan?->pmoApprovedBy?->name ?? 'Charles Dickens' }}
                </p>
                <p class="text-slate-600 font-medium">
                    {{ $latestApprovedPlan?->pmoApprovedBy?->designation ?? 'Director, Bureau of Plant Industry' }}
                </p>
                <p class="text-slate-400 text-[9px] mt-0.5">PMO Administrator / Bureau Oversight</p>
            </div>
        </div>

    </div>

</body>
</html>
