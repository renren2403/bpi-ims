<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportingPeriodController;
use Illuminate\Support\Facades\Auth;
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    $currentReportingPeriod = null;
    try {
        $currentReportingPeriod = \App\Models\ReportingPeriod::where('is_active', true)
            ->orWhere('status', 'open')
            ->orderByDesc('is_active')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();
    } catch (\Throwable $e) {
        $currentReportingPeriod = null;
    }
    return view('landing', compact('currentReportingPeriod'));
})->name('landing');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Central Role-Based Dashboard Redirector
    Route::get('/dashboard', function () {return app(AuthController::class)->redirectUserByRole(Auth::user());})->name('dashboard');
    // User Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    // 1. PMO GOVERNANCE & ADMINISTRATION Routes (Superadmin, Admin, and PMO Evaluators)
    Route::middleware(['role:superadmin|admin|evaluator'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn () => view('dashboard.admin'))->name('dashboard');
        // User Management Module
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Reporting Periods & Cycle Rules Module
        Route::resource('reporting-periods', ReportingPeriodController::class);
        Route::patch('reporting-periods/{reportingPeriod}/toggle-active', [ReportingPeriodController::class, 'toggleActive'])->name('reporting-periods.toggle-active');
    });
    // 2. EVALUATOR (PMO) Routes
    Route::middleware(['role:evaluator'])->prefix('evaluator')->name('evaluator.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.evaluator'))->name('dashboard');});
    // 3. OU HEAD / DIVISION CHIEF Routes
    Route::middleware(['role:ou-head'])->prefix('head')->name('head.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.head'))->name('dashboard');});
    // 4. ENCODER Routes
    Route::middleware(['role:encoder'])->prefix('encoder')->name('encoder.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.encoder'))->name('dashboard');});
    // 5. HIGHER-UP / EXECUTIVE Routes
    Route::middleware(['role:higher-up'])->prefix('executive')->name('executive.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.executive'))->name('dashboard');});
});