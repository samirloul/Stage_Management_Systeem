<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Contract (interface) voor alle filterklassen in de applicatie.
 *
 * Door alle filters aan deze interface te koppelen (polymorfisme),
 * kunnen StudentFilter, CompanyFilter en InternshipFilter uitwisselbaar worden gebruikt.
 * Dit is een voorbeeld van het Strategy design pattern.
 */
interface FilterContract
{
    public function apply(Builder $query, array $filters): Builder;
}
