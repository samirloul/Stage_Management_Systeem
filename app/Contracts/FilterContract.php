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
    /**
     * Pas alle filters toe op de gegeven Eloquent-query.
     *
     * @param Builder $query   De te filteren query builder instantie.
     * @param array   $filters Associatief array met filterwaarden uit het HTTP-verzoek.
     * @return Builder         De aangepaste query met alle filters toegepast.
     */
    public function apply(Builder $query, array $filters): Builder;
}
