<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
