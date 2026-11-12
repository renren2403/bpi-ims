<?php
//UnlockRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlockRequest extends Model
{
    protected $fillable = [
        'monthly_report_id', 'operating_unit_id', 'requested_by', 'reason', 'status',
        'reviewed_by', 'reviewed_at', 'review_remarks', 'expires_at'];

    protected $casts = ['reviewed_at' => 'datetime', 'expires_at' => 'datetime'];

    public function monthlyReport()
    {
        return $this->belongsTo(MonthlyReport::class);
    }

    public function operatingUnit()
    {
        return $this->belongsTo(OperatingUnit::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
