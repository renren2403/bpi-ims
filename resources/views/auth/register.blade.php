<!--register.blade.php auth-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Access Request &bull; Bureau of Plant Industry (BPI-IPMS)</title>
    <link rel="icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <link rel="shortcut icon" type="image/png"
        href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dagreen: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 flex flex-col justify-between min-h-screen antialiased">
    <!-- Official Top Bar -->
    <div class="bg-[#0b1f3a] text-slate-300 text-[11px] px-6 lg:px-12 py-2 border-b-2 border-amber-500 w-full">
        <div class="w-full flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <span class="font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png" alt="DA-BPI Logo"
                        class="w-5 h-5 object-contain shrink-0">
                    <span>Bureau of Plant Industry</span>
                </span>
                <span class="text-slate-600">&bull;</span>
                <span class="text-emerald-400 font-bold">Integrated Performance Monitoring System</span>
            </div>
            <a href="{{ route('landing') }}" class="text-slate-400 hover:text-white transition font-medium">&larr;
                Return to Bureau Homepage</a>
        </div>
    </div>

    <!-- Registration Container -->
    <div class="flex items-center justify-center p-4 my-6">
        <div class="w-full max-w-xl bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden">
            <!-- Agency Header -->
            <div class="bg-dagreen-950 p-6 text-center text-white border-b-4 border-dagreen-700">
                <div
                    class="w-16 h-16 rounded-full bg-white border-2 border-amber-400 p-1 flex items-center justify-center mx-auto mb-2.5 shadow">
                    <img src="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png"
                        alt="Bureau of Plant Industry Logo" class="w-full h-full object-contain">
                </div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-slate-300">Republic of the Philippines
                </p>
                <p class="text-[11px] uppercase font-extrabold tracking-wider text-amber-400">Department of Agriculture
                </p>
                <h1 class="text-lg font-black tracking-tight text-white mt-0.5">BUREAU OF PLANT INDUSTRY</h1>
                <p class="text-[11px] font-semibold text-slate-200 mt-1 uppercase tracking-wider">Personnel Registration
                    & Access Request</p>
            </div>

            <div class="p-6 sm:p-7">
                <!-- Verification Advisory Notice -->
                @if ($errors->any())
                    <!-- Error Box (Shown on Failure) -->
                    <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                        <p class="font-bold">Please correct the following errors:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <!-- Verification Advisory Notice (Shown only on clean load) -->
                    <div
                        class="mb-5 p-3.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-950 text-xs flex gap-3 items-start">
                        <span class="text-base shrink-0">🏛️</span>
                        <div class="leading-relaxed">
                            <p class="font-extrabold">Official PMO Verification Required</p>
                            <p class="text-amber-800 text-[11px] mt-0.5">
                                New user accounts are subject to identity verification and approval by the Planning &
                                Management Office (PMO) prior to login activation.
                            </p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs">
                    @csrf

                    <!-- Name & Designation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name <span
                                    class="text-rose-600">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Juan P. Dela Cruz" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Designation /
                                Title <span class="text-rose-600">*</span></label>
                            <input type="text" name="designation" value="{{ old('designation') }}"
                                placeholder="e.g. Agriculturist II" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold">
                        </div>
                    </div>

                    <!-- Official Email -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Official Government
                            Email <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="e.g. j.delacruz@bpi.gov.ph" required
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold">
                        <p class="text-[10px] text-slate-500 mt-1">Please provide your official bureau mailbox.</p>
                    </div>

                    <!-- Requested Role Responsibility & Operating Unit Selection -->
                    @php
                        $pmoUnit =
                            $operatingUnits->firstWhere('code', 'PMO') ??
                            $operatingUnits->first(fn($u) => stripos($u->name, 'Planning') !== false);
                        $pmoId = $pmoUnit?->id ?? '';
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Left Column: Requested Responsibility / Role -->
                        <div>
                            <label for="role_select"
                                class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Requested Responsibility <span class="text-rose-600">*</span>
                            </label>
                            <select name="role" id="role_select" required onchange="handleRoleChange()"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold bg-white transition">
                                <option value="">-- Select Role Responsibility --</option>
                                <option value="encoder" {{ old('role') === 'encoder' ? 'selected' : '' }}>OU Encoder
                                    (Inputs Accomplishments)</option>
                                <option value="ou-head" {{ old('role') === 'ou-head' ? 'selected' : '' }}>OU Head /
                                    Division Chief (Certifies Reports)</option>
                                <option value="evaluator" {{ old('role') === 'evaluator' ? 'selected' : '' }}>PMO
                                    Evaluator (Evaluates Submissions)</option>
                                <option value="higher-up" {{ old('role') === 'higher-up' ? 'selected' : '' }}>Executive
                                    Directorate (Bureau-Wide Oversight)</option>
                            </select>
                        </div>

                        <!-- Right Column: Operating Unit / Division -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="ou_select" id="ou_label"
                                    class="block font-bold text-slate-700 uppercase tracking-wider">
                                    <span>Operating Unit / Division</span>
                                    <span id="ou_required_star" class="text-rose-600">*</span>
                                </label>
                                <!-- Sleek Tooltip Icon & Bubble -->
                                <div class="relative group inline-flex items-center">
                                    <span
                                        class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-200 hover:bg-dagreen-700 text-slate-600 hover:text-white transition cursor-help text-[10px] font-bold">
                                        i
                                    </span>
                                    <div id="ou_tooltip_bubble"
                                        class="absolute right-0 bottom-full mb-1.5 hidden group-hover:block w-64 p-2.5 bg-slate-900 text-white text-[11px] font-medium rounded-lg shadow-xl z-30 pointer-events-none leading-snug border border-slate-700">
                                        Select your assigned organizational unit.
                                    </div>
                                </div>
                            </div>
                            <select name="operating_unit_id" id="ou_select" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold bg-white transition">
                                <option value="" id="ou_default_option">-- Select Division / Unit --</option>
                                @foreach ($operatingUnits as $ou)
                                    <option value="{{ $ou->id }}" data-code="{{ $ou->code }}"
                                        {{ old('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                                        {{ $ou->name }} ({{ $ou->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Password & Confirmation with Visibility Toggle -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Password <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="reg_password" name="password" required
                                    placeholder="Min. 8 chars (A-z, 0-9, @#$)"
                                    class="w-full px-3 py-2 pr-10 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold">
                                <button type="button"
                                    onclick="togglePassword('reg_password', 'reg_eye_open', 'reg_eye_closed')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <svg id="reg_eye_open" class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="reg_eye_closed" class="w-4 h-4 hidden" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Must contain uppercase, lowercase, number, and
                                symbol.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Confirm Password <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="reg_confirm_password" name="password_confirmation"
                                    required placeholder="Re-enter password"
                                    class="w-full px-3 py-2 pr-10 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold">
                                <button type="button"
                                    onclick="togglePassword('reg_confirm_password', 'reg_confirm_eye_open', 'reg_confirm_eye_closed')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <svg id="reg_confirm_eye_open" class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="reg_confirm_eye_closed" class="w-4 h-4 hidden" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Access Justification -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Assignment /
                            Designation Remarks (Optional)</label>
                        <textarea name="justification" rows="2"
                            placeholder="Brief note on your reporting responsibility or unit assignment..."
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 text-xs font-semibold">{{ old('justification') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3 bg-dagreen-800 hover:bg-dagreen-900 text-white font-extrabold rounded-lg text-xs shadow transition flex items-center justify-center gap-2">
                            <span>Submit Registration Request for PMO Approval</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-4 border-t border-slate-200 text-center space-y-1.5 text-xs">
                    <p class="text-slate-600">
                        Already have an approved account?
                        <a href="{{ route('login') }}" class="font-extrabold text-dagreen-800 hover:underline">Sign
                            In here &rarr;</a>
                    </p>
                    <p class="text-[10px] text-slate-400">
                        Republic of the Philippines &bull; Department of Agriculture &bull; Bureau of Plant Industry
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Footer -->
    <footer class="bg-white border-t border-slate-200 py-3 text-center text-[10px] text-slate-500">
        Planning and Management Office (PMO) &bull; Bureau of Plant Industry &bull; {{ date('Y') }}
    </footer>

    <script>
        const pmoId = "{{ $pmoId }}";

        function handleRoleChange() {
            const roleSelect = document.getElementById('role_select');
            const ouSelect = document.getElementById('ou_select');
            const ouRequiredStar = document.getElementById('ou_required_star');
            const defaultOption = document.getElementById('ou_default_option');
            const ouTooltipBubble = document.getElementById('ou_tooltip_bubble');
            const role = roleSelect.value;

            let tooltipText = 'Select your assigned division or unit.';

            if (role === 'higher-up') {
                // Executive Directorate encompasses ALL Operating Units bureau-wide
                ouSelect.value = '';
                ouSelect.disabled = true;
                ouSelect.required = false;
                ouSelect.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'border-slate-200');
                ouSelect.classList.remove('bg-white', 'text-slate-800', 'bg-emerald-50/70', 'text-emerald-900',
                    'border-emerald-300');
                ouRequiredStar.classList.add('hidden');
                defaultOption.textContent = '-- Not Applicable (Bureau-Wide Oversight) --';
                tooltipText =
                    '🏛️ Executive Directorate encompasses all 12 bureau divisions and national centers. Division assignment is not applicable.';
            } else if (role === 'evaluator') {
                // PMO Evaluator belongs to the Planning & Management Office (PMO)
                if (pmoId) {
                    ouSelect.value = pmoId;
                }
                ouSelect.disabled = true;
                ouSelect.required = false;
                ouSelect.classList.add('bg-emerald-50/70', 'text-emerald-900', 'cursor-not-allowed', 'border-emerald-300');
                ouSelect.classList.remove('bg-white', 'bg-slate-100', 'text-slate-400');
                ouRequiredStar.classList.add('hidden');
                defaultOption.textContent = '-- Planning and Management Office (PMO) --';
                tooltipText =
                    '📊 Stationed at Planning & Management Office (PMO). Evaluators review physical actuals across all divisions.';
            } else if (role === 'ou-head') {
                // OU Head / Division Chief is tied to a specific unit
                ouSelect.disabled = false;
                ouSelect.required = true;
                ouSelect.classList.remove('bg-slate-100', 'bg-emerald-50/70', 'text-slate-400', 'cursor-not-allowed',
                    'border-emerald-300');
                ouSelect.classList.add('bg-white', 'text-slate-800');
                ouRequiredStar.classList.remove('hidden');
                defaultOption.textContent = '-- Select Division / Unit You Head --';
                tooltipText = 'Select the specific operating unit/division you lead and certify monthly BED-2 reports for.';
            } else if (role === 'encoder') {
                // OU Encoder is tied to a specific unit
                ouSelect.disabled = false;
                ouSelect.required = true;
                ouSelect.classList.remove('bg-slate-100', 'bg-emerald-50/70', 'text-slate-400', 'cursor-not-allowed',
                    'border-emerald-300');
                ouSelect.classList.add('bg-white', 'text-slate-800');
                ouRequiredStar.classList.remove('hidden');
                defaultOption.textContent = '-- Select Division / Unit You Encode For --';
                tooltipText =
                    'Select the specific operating unit/division where you encode monthly actuals and upload MOVs.';
            } else {
                // Default / No Role Selected
                ouSelect.disabled = false;
                ouSelect.required = true;
                ouSelect.classList.remove('bg-slate-100', 'bg-emerald-50/70', 'text-slate-400', 'cursor-not-allowed',
                    'border-emerald-300');
                ouSelect.classList.add('bg-white', 'text-slate-800');
                ouRequiredStar.classList.remove('hidden');
                defaultOption.textContent = '-- Select Division / Unit --';
                tooltipText = 'Select your assigned division or unit.';
            }

            if (ouTooltipBubble) {
                ouTooltipBubble.textContent = tooltipText;
            }
            ouSelect.title = tooltipText;
        }
        document.addEventListener('DOMContentLoaded', function() {
            handleRoleChange();
            // Re-enable disabled select right before submit so evaluator's PMO selection is submitted
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    const role = document.getElementById('role_select').value;
                    const ouSelect = document.getElementById('ou_select');
                    if (role === 'evaluator') {
                        ouSelect.disabled = false;
                    }
                });
            }
        });

        function togglePassword(inputId, openId, closedId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            if (closedId) {
                const eyeOpen = document.getElementById(openId);
                const eyeClosed = document.getElementById(closedId);
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeOpen) eyeOpen.classList.add('hidden');
                    if (eyeClosed) eyeClosed.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    if (eyeClosed) eyeClosed.classList.add('hidden');
                    if (eyeOpen) eyeOpen.classList.remove('hidden');
                }
            } else {
                const icon = document.getElementById(openId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML =
                        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;
                } else {
                    input.type = 'password';
                    icon.innerHTML =
                        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
                }
            }
        }
    </script>
</body>

</html>
