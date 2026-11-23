<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'label',
        'module',
        'description',
    ];

    public function getDisplayLabelAttribute(): string
    {
        return $this->label ?: ucwords(str_replace(['.', '_', '-'], ' ', $this->name));
    }

    public function getDisplayModuleAttribute(): string
    {
        return $this->module ?: 'General';
    }
}
