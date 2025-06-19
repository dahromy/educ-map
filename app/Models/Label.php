<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'description'
    ];

    /**
     * Get the establishments for this label.
     */
    public function establishments()
    {
        return $this->belongsToMany(Establishment::class, 'establishment_labels')
            ->withTimestamps();
    }
}
