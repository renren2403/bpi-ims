<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PAP extends Model
{
    protected $table = 'paps';

    protected $fillable = ['code', 'name', 'description', 'category', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function performanceIndicators()
    {
        return $this->hasMany(PerformanceIndicator::class, 'pap_id');
    }

    public function bed2Plans()
    {
        return $this->hasMany(Bed2Plan::class, 'pap_id');
    }
}
