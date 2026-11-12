<?php

// OperatingUnit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OperatingUnit extends Model
{
    /**
     * Standardized operating unit classification types.
     *
     * @var list<string>
     */
    public const TYPES = [
        'Division',
        'Center',
        'Office',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'is_active',
        'division_chief_id',
        'reporting_officer_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected function code(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => $value !== null ? mb_strtoupper($value) : null,
            set: fn (?string $value): ?string => $value !== null ? mb_strtoupper(trim($value)) : null,
        );
    }

    /**
     * Exclude evaluating/oversight PMO units from covered reporting operating units.
     */
    #[Scope]
    protected function coveredForReporting(Builder $query): void
    {
        $query->where(function (Builder $subQuery): void {
            $subQuery->whereNull('code')
                ->orWhereRaw('UPPER(code) != ?', ['PMO']);
        });
    }

    /**
     * Retrieve covered reporting units for public directory display, excluding PMO.
     *
     * @return Collection<int, static>
     */
    public static function coveredDirectoryUnits(): Collection
    {
        $units = static::query()
            ->coveredForReporting()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($units->isEmpty()) {
            return static::query()
                ->coveredForReporting()
                ->orderBy('name')
                ->get();
        }

        return $units;
    }

    public function divisionChief()
    {
        return $this->belongsTo(User::class, 'division_chief_id');
    }

    public function reportingOfficer()
    {
        return $this->belongsTo(User::class, 'reporting_officer_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bed2Plans()
    {
        return $this->hasMany(Bed2Plan::class);
    }

    public function monthlyReports()
    {
        return $this->hasMany(MonthlyReport::class);
    }

    public function unlockRequests()
    {
        return $this->hasMany(UnlockRequest::class);
    }

    /**
     * Get the geographic station / headquarters location of the operating unit.
     */
    public function getLocationAttribute(): string
    {
        $code = strtoupper($this->code ?? '');

        if (str_contains($code, 'BNCRDC') || stripos($this->name ?? '', 'Baguio') !== false) {
            return 'Baguio City, Benguet';
        }
        if (str_contains($code, 'LNCRDC') || stripos($this->name ?? '', 'Los Baños') !== false || stripos($this->name ?? '', 'Los Banos') !== false) {
            return 'Los Baños, Laguna';
        }
        if (str_contains($code, 'DNCRDC') || stripos($this->name ?? '', 'Davao') !== false) {
            return 'Bago Oshiro, Davao City';
        }
        if (str_contains($code, 'GNCRDC') || stripos($this->name ?? '', 'Guimaras') !== false) {
            return 'Jordan, Guimaras';
        }

        return 'Central Office, Manila';
    }
}
