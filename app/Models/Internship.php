<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model dat de koppeling student-bedrijf-stageperiode bewaart.
class Internship extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'company_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'hours_per_week',
        'mentor_name',
        'status',
    ];

    protected function casts(): array
    {
        // Datums worden als date object gecast voor nette formatting en logica.
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        // Verwijzing naar student van deze stage.
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        // Verwijzing naar bedrijf van deze stage.
        return $this->belongsTo(Company::class);
    }

    public function reviews(): HasMany
    {
        // Een stage kan meerdere beoordelingen hebben.
        return $this->hasMany(Review::class);
    }
}
