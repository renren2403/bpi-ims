<?php
//ReportingPeriod.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportingPeriod extends Model
{
    protected $fillable = ['year', 'month', 'name', 'window_start', 'window_end', 'is_active', 'status', 'remarks'];

    protected $casts = ['window_start' => 'datetime', 'window_end' => 'datetime', 'is_active' => 'boolean'];

    public function monthlyReports()
    {
        return $this->hasMany(MonthlyReport::class);
    }
}
