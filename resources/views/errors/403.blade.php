<!--403.blade.php-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>403 - Unauthorized Access</title>
    <link rel="icon" type="image/png" href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <link rel="shortcut icon" type="image/png"
        href="https://buplant.da.gov.ph/wp-content/uploads/2024/03/DA-BPI-312313.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4 text-center">
    <div class="max-w-md bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
        <div class="text-5xl mb-3">🛡️</div>
        <h1 class="text-2xl font-extrabold text-rose-600">403 - Forbidden Access</h1>
        <p class="text-xs text-slate-500 mt-2">Your current user role does not have authorization to view this section.
        </p>
        <div class="mt-6">
            <a href="{{ url('/dashboard') }}"
                class="px-5 py-2.5 bg-slate-900 text-white rounded-lg text-xs font-bold">Return to Dashboard</a>
        </div>
    </div>
</body>

</html>
