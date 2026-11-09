<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MonthlyReportItem extends Model
{
    protected $fillable = [
        'monthly_report_id', 'bed2_plan_id', 'performance_indicator_id',
        'target', 'actual', 'variance', 'slippage_rate', 'status',
        'root_cause', 'immediate_correction', 'preventative_action', 'remarks'];
    public function monthlyReport(){return $this->belongsTo(MonthlyReport::class);}
    public function bed2Plan()
    {return $this->belongsTo(Bed2Plan::class);}
    public function performanceIndicator(){return $this->belongsTo(PerformanceIndicator::class);}
    public function meansOfVerification(){return $this->hasMany(MeansOfVerification::class);}
}