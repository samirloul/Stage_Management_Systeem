<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Haal ingelogde gebruiker op uit de sessie.
        $user = $request->user();

        // Stop verzoek direct met 403 wanneer rol niet is toegestaan.
        if ($user === null || ! $user->hasRole(...$roles)) {
            abort(403, 'Je hebt geen toegang tot deze pagina.');
        }

        // Gebruiker heeft toegang, dus ga door naar volgende middleware/controller.
        return $next($request);
    }
}
