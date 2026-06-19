<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model voor beoordelingsscores, feedback en aanbeveling.
class Review extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'reviewer_user_id',
        'score',
        'feedback',
        'review_date',
        'recommendation',
    ];

    protected function casts(): array
    {
        // Cast voor consistente datumverwerking in UI en validatie.
        return [
            'review_date' => 'date',
        ];
    }

    public function internship(): BelongsTo
    {
        // De stage waarop deze beoordeling van toepassing is.
        return $this->belongsTo(Internship::class);
    }

    public function reviewer(): BelongsTo
    {
        // De gebruiker die de beoordeling heeft geplaatst.
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function getRecommendationLabelAttribute(): string
    {
        // Presenteert technische waardes als Nederlandse labels in de UI.
        return match ($this->recommendation) {
            'yes' => 'Ja',
            'no' => 'Nee',
            default => 'Misschien',
        };
    }
}
