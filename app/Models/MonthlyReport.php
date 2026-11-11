<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $fillable = [
        'operating_unit_id', 'reporting_period_id', 'year', 'month', 'status', 'remarks',
        'submitted_by', 'submitted_at', 'head_approved_by', 'head_approved_at',
        'evaluated_by', 'evaluated_at'];

    protected $casts = ['submitted_at' => 'datetime', 'head_approved_at' => 'datetime', 'evaluated_at' => 'datetime'];

    public function operatingUnit()
    {
        return $this->belongsTo(OperatingUnit::class);
    }

    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriod::class);
    }

    public function items()
    {
        return $this->hasMany(MonthlyReportItem::class);
    }

    public function unlockRequests()
    {
        return $this->hasMany(UnlockRequest::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function headApprovedBy()
    {
        return $this->belongsTo(User::class, 'head_approved_by');
    }

    public function evaluatedBy()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
