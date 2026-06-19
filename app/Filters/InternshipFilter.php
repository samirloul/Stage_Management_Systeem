<?php

namespace App\Filters;

use App\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filter voor het zoeken en filteren van stagekoppelingen.
 *
 * Ondersteunt zoeken op stagetitel, studentnaam en bedrijfsnaam
 * via whereHas-subqueries op de gerelateerde modellen.
 */
class InternshipFilter implements FilterContract
{
    public function apply(Builder $query, array $filters): Builder
    {
        // Zoekfilter kijkt in de stagetitel en via relaties naar student- en bedrijfsnamen.
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    // Directe zoekopdracht op de titel van de stage.
                    $inner->where('title', 'like', "%{$search}%")
                        // Subquery op gerelateerde student via whereHas (geen JOIN nodig).
                        ->orWhereHas('student', fn (Builder $s) =>
                            $s->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name',  'like', "%{$search}%")
                        )
                        // Subquery op gerelateerd bedrijf.
                        ->orWhereHas('company', fn (Builder $c) =>
                            $c->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status))
            ->orderBy('start_date', 'desc'); // Meest recente stages bovenaan.
    }
}