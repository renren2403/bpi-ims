<!--forgot-password.blade.php auth-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password &bull; Bureau of Plant Industry (BPI-IPMS)</title>
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

    <!-- Forgot Password Container -->
    <div class="flex items-center justify-center p-4 my-auto">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden">

            <!-- Official Agency Banner Header -->
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
                <h1 class="text-lg font-black tracking-tight text-white mt-0.5">PASSWORD RESET REQUEST</h1>
                <p class="text-[11px] font-semibold text-slate-200 mt-1 uppercase tracking-wider">BPI Integrated
                    Performance Monitoring System</p>
            </div>

            <div class="p-7">
                <div
                    class="mb-5 p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs leading-relaxed">
                    Enter your registered official email address and we will send you a secure link to choose a new
                    password.
                </div>

                @if ($errors->any())
                    <div
                        class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if (session('status'))
                    <div
                        class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Official
                            Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="e.g. user@bpi.gov.ph"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 focus:border-dagreen-800 text-xs font-semibold transition">
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-dagreen-800 hover:bg-dagreen-900 text-white font-extrabold rounded-lg text-xs shadow transition flex items-center justify-center gap-2">
                        <span>Send Password Reset Link</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-slate-200 text-center space-y-2 text-xs">
                    <p class="text-slate-600">
                        Remember your password?
                        <a href="{{ route('login') }}"
                            class="font-extrabold text-dagreen-800 hover:underline">&larr; Back to Sign In</a>
                    </p>
                    <p class="text-[10px] text-slate-400">
                        RA 10175 &bull; Department of Agriculture &bull; Bureau of Plant Industry
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Footer -->
    <footer class="bg-white border-t border-slate-200 py-3 text-center text-[10px] text-slate-500">
        Planning and Management Office (PMO) &bull; Bureau of Plant Industry &bull; {{ date('Y') }}
    </footer>
</body>

</html>
