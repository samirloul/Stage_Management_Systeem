<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Rolmodel voor autorisatie (admin, student, company).
class Role extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'label'];

    public function users(): HasMany
    {
        // Een rol kan aan meerdere gebruikers toegewezen zijn.
        return $this->hasMany(User::class);
    }
}
