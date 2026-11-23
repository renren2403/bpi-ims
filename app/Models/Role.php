<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
    ];

    public function getDisplayTitleAttribute(): string
    {
        return match ($this->name) {
            'superadmin' => 'Superadmin',
            'admin' => 'PMO Administrator',
            'evaluator' => 'PMO Evaluator',
            'ou-head' => 'OU Head / Division Chief',
            'encoder' => 'OU Encoder',
            'higher-up' => 'Executive Directorate',
            default => ucwords(str_replace(['-', '_'], ' ', $this->name)),
        };
    }
}
