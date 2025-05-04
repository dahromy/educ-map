<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramOffering extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'establishment_id',
        'department_id',
        'domain_id',
        'grade_id',
        'mention_id',
        'tuition_fees_info',
        'program_duration_info'
    ];

    /**
     * Get the establishment that owns the program offering.
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    /**
     * Get the department that owns the program offering.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the domain of this program offering.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the grade of this program offering.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the mention of this program offering.
     */
    public function mention(): BelongsTo
    {
        return $this->belongsTo(Mention::class);
    }

    /**
     * Get the accreditations for this program offering.
     */
    public function accreditations(): HasMany
    {
        return $this->hasMany(Accreditation::class);
    }
}
