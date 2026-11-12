<!--login.blade.php auth-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Sign In &bull; Bureau of Plant Industry (BPI-IPMS)</title>
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

    <!-- Login Container -->
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
                <h1 class="text-lg font-black tracking-tight text-white mt-0.5">BUREAU OF PLANT INDUSTRY</h1>
                <p class="text-[11px] font-semibold text-slate-200 mt-1 uppercase tracking-wider">Integrated Performance
                    Monitoring System</p>
            </div>

            <div class="p-7">
                <!-- Legal Advisory Callout -->
                <div
                    class="mb-5 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs flex gap-2.5 items-start">
                    <span class="text-base shrink-0">🏛️</span>
                    <p class="text-[11px] leading-relaxed">
                        <strong>Official Government System.</strong> Authorized personnel, division encoders, and
                        designated PMO evaluators only.
                    </p>
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

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Official
                            Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="e.g. user@bpi.gov.ph"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 focus:border-dagreen-800 text-xs font-semibold transition">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="login_password" name="password" required placeholder="••••••••"
                                class="w-full px-3.5 py-2.5 pr-10 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-dagreen-700 focus:border-dagreen-800 text-xs font-semibold transition">
                            <button type="button"
                                onclick="togglePassword('login_password', 'login_eye_open', 'login_eye_closed')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <!-- Eye Open (Password hidden, click to show) -->
                                <svg id="login_eye_open" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Slashed (Password visible, click to hide) -->
                                <svg id="login_eye_closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                            <input type="checkbox" name="remember"
                                class="rounded text-dagreen-800 focus:ring-dagreen-700 border-slate-300">
                            <span class="font-medium text-xs">Keep me signed in</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="font-bold text-dagreen-800 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-dagreen-800 hover:bg-dagreen-900 text-white font-extrabold rounded-lg text-xs shadow transition flex items-center justify-center gap-2">
                        <span>Sign In to Government Portal</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-slate-200 text-center space-y-2 text-xs">
                    <p class="text-slate-600">
                        Need official division access?
                        <a href="{{ route('register') }}"
                            class="font-extrabold text-dagreen-800 hover:underline">Request Account &rarr;</a>
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
    <script>
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
