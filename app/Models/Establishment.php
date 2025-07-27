<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establishment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'category_id',
        'address',
        'region',
        'city',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'logo_url',
        'student_count',
        'success_rate',
        'professional_insertion_rate',
        'first_habilitation_year',
        'status',
        'international_partnerships',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'student_count' => 'integer',
        'success_rate' => 'float',
        'professional_insertion_rate' => 'float',
        'first_habilitation_year' => 'integer',
    ];

    /**
     * Get the category that owns the establishment.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the departments for the establishment.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the program offerings for the establishment.
     */
    public function programOfferings(): HasMany
    {
        return $this->hasMany(ProgramOffering::class);
    }

    /**
     * Get only the direct program offerings (not belonging to any department).
     */
    public function directProgramOfferings(): HasMany
    {
        return $this->hasMany(ProgramOffering::class)->whereNull('department_id');
    }

    /**
     * Get the labels for the establishment.
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'establishment_labels')
            ->withTimestamps();
    }

    /**
     * Get the users associated with the establishment.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'associated_establishment');
    }

    /**
     * Get the doctoral school affiliations for the establishment.
     */
    public function doctoralAffiliations(): HasMany
    {
        return $this->hasMany(Affiliation::class);
    }

    /**
     * Define search scope for filtering by region
     */
    public function scopeFilterByRegion($query, $region)
    {
        if ($region) {
            return $query->where('region', $region);
        }
        return $query;
    }

    /**
     * Define search scope for filtering by name
     */
    public function scopeFilterByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', "%{$name}%");
        }
        return $query;
    }

    /**
     * Define search scope for filtering by abbreviation
     */
    public function scopeFilterByAbbreviation($query, $abbreviation)
    {
        if ($abbreviation) {
            return $query->where('abbreviation', 'like', "%{$abbreviation}%");
        }
        return $query;
    }

    /**
     * Define search scope for filtering by category
     */
    public function scopeFilterByCategory($query, $categoryName)
    {
        if ($categoryName) {
            return $query->whereHas('category', function ($query) use ($categoryName) {
                $query->where('category_name', $categoryName);
            });
        }
        return $query;
    }

    /**
     * Define search scope for filtering by domain
     */
    public function scopeFilterByDomain($query, $domainName)
    {
        if ($domainName) {
            return $query->whereHas('programOfferings.domain', function ($query) use ($domainName) {
                $query->where('name', 'like', "%{$domainName}%");
            });
        }
        return $query;
    }

    /**
     * Define search scope for filtering by label
     */
    public function scopeFilterByLabel($query, $labelName)
    {
        if ($labelName) {
            return $query->whereHas('labels', function ($query) use ($labelName) {
                $query->where('name', $labelName);
            });
        }
        return $query;
    }

    /**
     * Define search scope for filtering by reference date range
     */
    public function scopeFilterByReferenceDate($query, $startDate, $endDate)
    {
        if ($startDate || $endDate) {
            return $query->whereHas('programOfferings.accreditations.reference', function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->where('main_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->where('main_date', '<=', $endDate);
                }
            });
        }
        return $query;
    }

    /**
     * Define search scope for filtering by city
     */
    public function scopeFilterByCity($query, $city)
    {
        if ($city) {
            return $query->where('city', 'like', "%{$city}%");
        }
        return $query;
    }

    /**
     * Define search scope for filtering by recent accreditation
     */
    public function scopeFilterByRecentAccreditation($query, $hasRecent)
    {
        if ($hasRecent !== null) {
            if ($hasRecent) {
                return $query->whereHas('programOfferings.accreditations', function ($query) {
                    $query->where('is_recent', true);
                });
            } else {
                return $query->whereDoesntHave('programOfferings.accreditations', function ($query) {
                    $query->where('is_recent', true);
                });
            }
        }
        return $query;
    }

    /**
     * Define search scope for filtering by student count range
     */
    public function scopeFilterByStudentCount($query, $minCount, $maxCount)
    {
        if ($minCount !== null) {
            $query->where('student_count', '>=', $minCount);
        }
        if ($maxCount !== null) {
            $query->where('student_count', '<=', $maxCount);
        }
        return $query;
    }

    /**
     * Define scope to get recent establishments
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orWhereHas('programOfferings.accreditations', function ($query) {
                $query->where('is_recent', true);
            });
    }
}
