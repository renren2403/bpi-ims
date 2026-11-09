<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingUnit extends Model
{
    protected $fillable = ['name', 'code', 'type', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function users(){ return $this->hasMany(User::class);}
    public function bed2Plans(){ return $this->hasMany(Bed2Plan::class);}
    public function monthlyReports(){ return $this->hasMany(MonthlyReport::class);}
    public function unlockRequests(){ return $this->hasMany(UnlockRequest::class);}
    
}
