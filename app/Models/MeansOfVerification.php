<?php
//MeansOfVerification.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeansOfVerification extends Model
{
    protected $table = 'means_of_verification';

    protected $fillable = ['monthly_report_item_id', 'file_name', 'file_path', 'file_type', 'file_size', 'uploaded_by', 'description'];

    public function monthlyReportItem()
    {
        return $this->belongsTo(MonthlyReportItem::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
