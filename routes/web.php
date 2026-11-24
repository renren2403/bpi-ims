<?php

// web.php
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\OperatingUnitController;
use App\Http\Controllers\Admin\PAPController;
use App\Http\Controllers\Admin\PerformanceIndicatorController;
use App\Http\Controllers\Admin\ReportingPeriodController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Bed2PlanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Head\ReviewController as HeadReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnderConstructionController;
use App\Models\OperatingUnit;
use App\Models\ReportingPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    $currentReportingPeriod = null;
    try {
        $currentReportingPeriod = ReportingPeriod::where('is_active', true)
            ->orWhere('status', 'open')
            ->orderByDesc('is_active')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();
    } catch (Throwable $e) {
        $currentReportingPeriod = null;
    }
    $operatingUnits = collect();
    try {
        $operatingUnits = OperatingUnit::coveredDirectoryUnits();
    } catch (Throwable $e) {
        $operatingUnits = collect();
    }
    $operatingUnitsCount = $operatingUnits->count();

    return view('landing', compact('currentReportingPeriod', 'operatingUnits', 'operatingUnitsCount'));
})->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Central Role-Based Dashboard Redirector
    Route::get('/dashboard', function () {
        return app(AuthController::class)->redirectUserByRole(Auth::user());
    })->name('dashboard');
    // User Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    // BED-2 Target Plans Module
    Route::post('/bed2-plans/rollover',[Bed2PlanController::class, 'rolloverYear'])->name('bed2-plans.rollover');
    Route::post('/bed2-plans/batch-submit-head', [Bed2PlanController::class, 'batchSubmitHead'])->name('bed2-plans.batch-submit-head');
    Route::post('/bed2-plans/batch-head-approve', [Bed2PlanController::class, 'batchHeadApprove'])->name('bed2-plans.batch-head-approve');
    Route::post('/bed2-plans/batch-pmo-approve', [Bed2PlanController::class, 'batchPmoApprove'])->name('bed2-plans.batch-pmo-approve');
    Route::post('/bed2-plans/batch-return', [Bed2PlanController::class, 'batchReturn'])->name('bed2-plans.batch-return');
    Route::post('/bed2-plans/{bed2Plan}/submit-head', [Bed2PlanController::class, 'submitToHead'])->name('bed2-plans.submit-head');
    Route::post('/bed2-plans/{bed2Plan}/head-approve', [Bed2PlanController::class, 'headApprove'])->name('bed2-plans.head-approve');
    Route::post('/bed2-plans/{bed2Plan}/return', [Bed2PlanController::class, 'returnPlan'])->name('bed2-plans.return');
    Route::post('/bed2-plans/{bed2Plan}/pmo-approve', [Bed2PlanController::class, 'pmoApprove'])->name('bed2-plans.pmo-approve');
    Route::get('/bed2-plans/export/print', [Bed2PlanController::class, 'printDocument'])->name('bed2-plans.export.print');
    Route::get('/bed2-plans/export/csv', [Bed2PlanController::class, 'exportCsv'])->name('bed2-plans.export.csv');
    Route::resource('bed2-plans', Bed2PlanController::class)->parameters(['bed2-plans' => 'bed2Plan']);
    Route::post('/api/quick-pap', [PAPController::class, 'quickStore'])->name('paps.quick-store');
    Route::post('/api/quick-indicator', [PerformanceIndicatorController::class, 'quickStore'])->name('indicators.quick-store');
    Route::get('/monthly-reports', [UnderConstructionController::class, 'monthlyReports'])->name('monthly-reports.index');
    Route::get('/unlock-requests', [UnderConstructionController::class, 'unlockRequests'])->name('unlock-requests.index');
    // 1. PMO GOVERNANCE & ADMINISTRATION Routes (Superadmin, Admin, and PMO Evaluators)
    Route::middleware(['role:superadmin|admin|evaluator'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        // User Management Module
        Route::post('users/bulk', [UserController::class, 'bulkAction'])->name('users.bulk');
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Reporting Periods & Cycle Rules Module
        Route::resource('reporting-periods', ReportingPeriodController::class);
        Route::patch('reporting-periods/{reportingPeriod}/toggle-active', [ReportingPeriodController::class, 'toggleActive'])->name('reporting-periods.toggle-active');
        Route::resource('operating-units', OperatingUnitController::class)->except(['show']);
        Route::patch('operating-units/{operatingUnit}/toggle-status', [OperatingUnitController::class, 'toggleStatus'])->name('operating-units.toggle-status');

        // Programs / PAPs & Indicators Module
        Route::resource('paps', PAPController::class)->except(['show']);
        Route::patch('paps/{pap}/toggle-status', [PAPController::class, 'toggleStatus'])->name('paps.toggle-status');
        Route::post('paps/{pap}/indicators', [PerformanceIndicatorController::class, 'store'])->name('paps.indicators.store');
        Route::put('indicators/{indicator}', [PerformanceIndicatorController::class, 'update'])->name('indicators.update');
        Route::patch('indicators/{indicator}/toggle-status', [PerformanceIndicatorController::class, 'toggleStatus'])->name('indicators.toggle-status');
        Route::delete('indicators/{indicator}', [PerformanceIndicatorController::class, 'destroy'])->name('indicators.destroy');

        // Dynamic Roles & Permissions Matrix
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
        Route::post('/roles-permissions/toggle', [RolePermissionController::class, 'toggle'])->name('roles-permissions.toggle');
        Route::post('/roles-permissions/permissions', [RolePermissionController::class, 'storePermission'])->name('roles-permissions.permissions.store');
        Route::post('/roles-permissions/roles', [RolePermissionController::class, 'storeRole'])->name('roles-permissions.roles.store');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
    // 2. EVALUATOR (PMO) Routes
    Route::middleware(['role:evaluator'])->prefix('evaluator')->name('evaluator.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'evaluator'])->name('dashboard');
    });
    // 3. OU HEAD / DIVISION CHIEF Routes
    Route::middleware(['role:ou-head'])->prefix('head')->name('head.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'head'])->name('dashboard');
        Route::get('/for-review', [HeadReviewController::class, 'index'])->name('reviews.index');
    });
    // 4. ENCODER Routes
    Route::middleware(['role:encoder'])->prefix('encoder')->name('encoder.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'encoder'])->name('dashboard');
    });
    // 5. HIGHER-UP / EXECUTIVE Routes
    Route::middleware(['role:higher-up'])->prefix('executive')->name('executive.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'executive'])->name('dashboard');
    });
});
