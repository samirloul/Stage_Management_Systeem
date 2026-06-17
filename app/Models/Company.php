<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Domeinmodel voor bedrijfsgegevens.
class Company extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'city',
        'industry',
        'website',
        'status',
    ];

    public function user(): BelongsTo
    {
        // Optionele user-koppeling wanneer bedrijf ook een loginaccount heeft.
        return $this->belongsTo(User::class);
    }

    public function internships(): HasMany
    {
        // Een bedrijf kan meerdere stagekoppelingen ontvangen.
        return $this->hasMany(Internship::class);
    }
}
