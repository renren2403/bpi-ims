<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceIndicator extends Model
{
    protected $fillable = ['pap_id', 'code', 'name', 'unit_of_measure', 'type', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function pap()
    {
        return $this->belongsTo(PAP::class, 'pap_id');
    }

    public function bed2Plans()
    {
        return $this->hasMany(Bed2Plan::class);
    }

    public function monthlyReportItems()
    {
        return $this->hasMany(MonthlyReportItem::class);
    }
}
