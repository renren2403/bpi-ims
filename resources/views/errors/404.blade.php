<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4 text-center">
    <div class="max-w-md bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
        <div class="text-5xl mb-3">🔍</div>
        <h1 class="text-2xl font-extrabold text-slate-800">404 - Page Not Found</h1>
        <p class="text-xs text-slate-500 mt-2">The requested resource or page does not exist.</p>
        <div class="mt-6">
            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-lg text-xs font-bold">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>