<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - BPI Performance Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="bg-[#081430] p-8 text-center text-white">
            <div class="w-14 h-14 bg-emerald-500/20 border border-emerald-400 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                🌱
            </div>
            <h1 class="text-xl font-extrabold tracking-wide">BPI - IMS PORTAL</h1>
            <p class="text-xs text-slate-300 mt-1">Planning & Management Information System</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-5 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-slate-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg text-sm shadow transition">
                    Sign In to Portal
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-200 text-center space-y-2 text-xs">
                <p class="text-slate-600">
                    Need system access? 
                    <a href="{{ route('register') }}" class="font-extrabold text-blue-700 hover:underline">Request Account &rarr;</a>
                </p>
                <p>
                    <a href="{{ route('landing') }}" class="text-slate-400 hover:text-slate-600 transition">&larr; Back to Portal Homepage</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>