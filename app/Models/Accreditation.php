<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accreditation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_offering_id',
        'reference_id',
        'reference_type',
        'accreditation_date',
        'is_recent'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'accreditation_date' => 'date',
        'is_recent' => 'boolean',
    ];

    /**
     * Get the program offering that owns the accreditation.
     */
    public function programOffering(): BelongsTo
    {
        return $this->belongsTo(ProgramOffering::class);
    }

    /**
     * Get the reference that owns the accreditation.
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(Reference::class);
    }
}
