<?php
//PerformanceIndicator.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceIndicator extends Model
{
    use HasFactory;
    protected $fillable = [
        'pap_id',
        'operating_unit_id',
        'sub_program',
        'activity',
        'sub_activity',
        'code',
        'name',
        'unit_of_measure',
        'type',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function pap()
    {
        return $this->belongsTo(PAP::class, 'pap_id');
    }

    public function operatingUnit()
    {
        return $this->belongsTo(OperatingUnit::class, 'operating_unit_id');
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
