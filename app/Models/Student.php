<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Domeinmodel voor studentgegevens en student-gerelateerde relaties.
class Student extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'program',
        'start_year',
        'status',
    ];

    public function user(): BelongsTo
    {
        // Optionele user-koppeling wanneer student ook een loginaccount heeft.
        return $this->belongsTo(User::class);
    }

    public function internships(): HasMany
    {
        // Een student kan meerdere stages hebben in de tijd.
        return $this->hasMany(Internship::class);
    }

    public function getFullNameAttribute(): string
    {
        // Virtueel attribuut voor volledige naam in lijsten en selecties.
        return $this->first_name.' '.$this->last_name;
    }
}
