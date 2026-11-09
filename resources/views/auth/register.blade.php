<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Account - BPI Performance Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        govblue: {
                            800: '#1e3a8a',
                            900: '#0f2454',
                            950: '#081430'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 sm:p-8">

    <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden my-8">
        
        <!-- Header -->
        <div class="bg-govblue-950 p-6 sm:p-8 text-center text-white">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 mb-3">
                <div class="w-12 h-12 bg-emerald-500/20 border border-emerald-400 rounded-full flex items-center justify-center text-2xl shadow-sm">
                    🌱
                </div>
            </a>
            <h1 class="text-xl font-extrabold tracking-wide">BPI - IMS PORTAL</h1>
            <p class="text-xs text-slate-300 mt-1">Personnel Account Registration & Access Request</p>
        </div>

        <div class="p-6 sm:p-8">

            <!-- Notice Callout -->
            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex gap-3 items-start">
                <span class="text-lg">ℹ️</span>
                <div class="leading-relaxed">
                    <p class="font-bold">Administrative Approval Required</p>
                    <p class="text-blue-700 mt-0.5">All new registration requests are submitted to the Planning & Management Office (PMO) for identity and role verification prior to activation.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                    <p class="font-bold">Please correct the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs">
                @csrf

                <!-- Name & Designation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Juan P. Dela Cruz" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Designation / Plantilla Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Agriculturist II" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <!-- Official Email -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Official / Government Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. j.delacruz@bpi.gov.ph" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <!-- Operating Unit & Role Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Operating Unit / Division <span class="text-rose-500">*</span></label>
                        <select name="operating_unit_id" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                            <option value="">-- Select Division / Unit --</option>
                            @foreach ($operatingUnits as $ou)
                                <option value="{{ $ou->id }}" {{ old('operating_unit_id') == $ou->id ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Requested Role <span class="text-rose-500">*</span></label>
                        <select name="role" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                            <option value="">-- Select Role --</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>
                                    @if ($r->name === 'encoder')
                                        OPERATING UNIT ENCODER
                                    @elseif ($r->name === 'ou-head')
                                        DIVISION CHIEF / OU HEAD
                                    @elseif ($r->name === 'evaluator')
                                        PMO EVALUATOR
                                    @elseif ($r->name === 'higher-up')
                                        EXECUTIVE / HIGHER-UP
                                    @else
                                        {{ strtoupper($r->name) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Justification -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Access Purpose / Justification</label>
                    <textarea name="justification" rows="2" placeholder="Briefly state your reporting or encoding assignment (optional)..."
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('justification') }}</textarea>
                </div>

                <!-- Password & Confirm -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" placeholder="Min. 8 characters" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Confirm Password <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Re-enter password" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full py-3.5 bg-blue-700 hover:bg-blue-800 text-white font-extrabold rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Submit Registration Request
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
                Already have an active account? 
                <a href="{{ route('login') }}" class="font-extrabold text-blue-700 hover:underline">Sign In Here &rarr;</a>
            </div>
        </div>
    </div>

</body>
</html>
