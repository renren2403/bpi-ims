<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bed2Plan extends Model
{
    protected $fillable = [
        'operating_unit_id', 'pap_id', 'performance_indicator_id', 'year',
        'annual_target', 'q1_target', 'q2_target', 'q3_target', 'q4_target',
        'm1_target', 'm2_target', 'm3_target', 'm4_target', 'm5_target', 'm6_target',
        'm7_target', 'm8_target', 'm9_target', 'm10_target', 'm11_target', 'm12_target',
        'status', 'remarks', 'submitted_by', 'head_approved_by', 'pmo_approved_by',
    ];

    public function operatingUnit()
    {
        return $this->belongsTo(OperatingUnit::class);
    }

    public function pap()
    {
        return $this->belongsTo(PAP::class);
    }

    public function performanceIndicator()
    {
        return $this->belongsTo(PerformanceIndicator::class);
    }

    public function monthlyReportItems()
    {
        return $this->hasMany(MonthlyReportItem::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
