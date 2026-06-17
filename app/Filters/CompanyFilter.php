<?php

namespace App\Filters;

use App\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class CompanyFilter implements FilterContract
{
    public function apply(Builder $query, array $filters): Builder
    {
        // Combinatiefilter op naam, contactpersoon en e-mail.
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['city'] ?? null, fn (Builder $q, string $city) => $q->where('city', $city))
            ->when($filters['industry'] ?? null, fn (Builder $q, string $industry) => $q->where('industry', $industry))
            ->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status));
    }
}
