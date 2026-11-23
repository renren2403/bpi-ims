<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        $query = AuditLog::with(['user.operatingUnit'])->latest();

        // Search by action, IP, or user name/email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by action
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by user
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter by Date
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(25)->withQueryString();

        $distinctActions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $metrics = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'logins' => AuditLog::where('action', 'auth.login')->whereDate('created_at', today())->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'distinctActions', 'users', 'metrics'));
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->can('audit_logs.view') && ! $user->hasAnyRole(['superadmin', 'admin', 'evaluator']))) {
            abort(403, 'Unauthorized access to Audit Logs.');
        }
    }
}
