<?php

// User.php

namespace App\Models;

use App\Mail\PasswordResetLinkMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;
use Throwable;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'designation',
        'email',
        'password',
        'operating_unit_id',
        'is_active',
        'approval_status',
        'approval_remarks',
    ];

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function roleLabel(): string
    {
        $roleName = $this->roles->first()?->name;

        return match ($roleName) {
            'encoder' => 'OU Encoder',
            'ou-head' => 'OU Head / Division Chief',
            'evaluator' => 'PMO Evaluator',
            'higher-up' => 'Executive Directorate (Bureau-Wide Oversight)',
            'admin' => 'PMO Administrator',
            'superadmin' => 'Superadmin',
            default => $roleName ? strtoupper($roleName) : 'Unassigned',
        };
    }

    public function operatingUnitLabel(): string
    {
        if ($this->hasAnyRole(['higher-up', 'superadmin'])) {
            return 'Bureau-Wide Oversight (All Operating Units)';
        }
        if ($this->operatingUnit !== null) {
            return "{$this->operatingUnit->name} ({$this->operatingUnit->code})";
        }

        return 'Pending Assignment';
    }

    /**
     * Send the password reset link notification using the branded BPI-IPMS mailable.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        try {
            Mail::to($this->email)->send(new PasswordResetLinkMail($this, $token));
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function deletionDenialReason(?self $actor): ?string
    {
        if (! $actor) {
            return 'Authentication is required.';
        }

        if ($this->id === $actor->id) {
            return 'You cannot delete your own account.';
        }

        if ($this->hasRole('superadmin') && ! $actor->hasRole('superadmin')) {
            return 'Only a Superadmin can delete a Superadmin account.';
        }

        if ($this->hasRole('admin') && ! $actor->hasAnyRole(['superadmin', 'admin'])) {
            return 'You do not have permission to delete an Administrator account.';
        }

        return null;
    }

    /**
     * Determine whether the given actor is allowed to delete this user.
     */
    public function canBeDeletedBy(?self $actor): bool
    {
        return $this->deletionDenialReason($actor) === null;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function operatingUnit()
    {
        return $this->belongsTo(OperatingUnit::class);
    }

    public function submittedMonthlyReports()
    {
        return $this->hasMany(MonthlyReport::class, 'submitted_by');
    }

    public function unlockRequests()
    {
        return $this->hasMany(UnlockRequest::class, 'requested_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
