<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bed2Plan extends Model
{
    use HasFactory;

    protected $table = 'bed2_plans';

    protected $fillable = [
        'operating_unit_id',
        'pap_id',
        'performance_indicator_id',
        'year',
        'annual_target',
        'q1_target',
        'q2_target',
        'q3_target',
        'q4_target',
        'm1_target',
        'm2_target',
        'm3_target',
        'm4_target',
        'm5_target',
        'm6_target',
        'm7_target',
        'm8_target',
        'm9_target',
        'm10_target',
        'm11_target',
        'm12_target',
        'status',
        'remarks',
        'submitted_by',
        'head_approved_by',
        'pmo_approved_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'annual_target' => 'decimal:2',
            'q1_target' => 'decimal:2',
            'q2_target' => 'decimal:2',
            'q3_target' => 'decimal:2',
            'q4_target' => 'decimal:2',
            'm1_target' => 'decimal:2',
            'm2_target' => 'decimal:2',
            'm3_target' => 'decimal:2',
            'm4_target' => 'decimal:2',
            'm5_target' => 'decimal:2',
            'm6_target' => 'decimal:2',
            'm7_target' => 'decimal:2',
            'm8_target' => 'decimal:2',
            'm9_target' => 'decimal:2',
            'm10_target' => 'decimal:2',
            'm11_target' => 'decimal:2',
            'm12_target' => 'decimal:2',
        ];
    }

    public function operatingUnit(): BelongsTo
    {
        return $this->belongsTo(OperatingUnit::class);
    }

    public function pap(): BelongsTo
    {
        return $this->belongsTo(PAP::class);
    }

    public function performanceIndicator(): BelongsTo
    {
        return $this->belongsTo(PerformanceIndicator::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function headApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_approved_by');
    }

    public function pmoApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pmo_approved_by');
    }

    public function monthlyReportItems(): HasMany
    {
        return $this->hasMany(MonthlyReportItem::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmittedToHead(): bool
    {
        return $this->status === 'submitted_to_head';
    }

    public function isHeadApproved(): bool
    {
        return $this->status === 'head_approved';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'returned']);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Draft Plan',
            'submitted_to_head' => 'Pending Division Review',
            'head_approved' => 'Endorsed (Pending PMO)',
            'approved' => 'Approved Baseline',
            'returned' => 'Returned for Revision',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
            'submitted_to_head' => 'bg-amber-50 text-amber-800 border-amber-200',
            'head_approved' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'returned' => 'bg-rose-50 text-rose-800 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    /**
     * Compute sum of all 12 monthly targets.
     */
    public function monthlySum(): float
    {
        return (float) (
            $this->m1_target + $this->m2_target + $this->m3_target +
            $this->m4_target + $this->m5_target + $this->m6_target +
            $this->m7_target + $this->m8_target + $this->m9_target +
            $this->m10_target + $this->m11_target + $this->m12_target
        );
    }

    /**
     * Calculate variance between annual target and sum of monthly milestone allocations.
     * Positive = under-allocated (annual > sum)
     * Negative = over-allocated (annual < sum)
     */
    public function balanceVariance(): float
    {
        return round((float) $this->annual_target - $this->monthlySum(), 2);
    }

    /**
     * Check if monthly milestone targets strictly balance against the annual commitment.
     */
    public function isBalanced(): bool
    {
        return abs($this->balanceVariance()) < 0.01;
    }
}
