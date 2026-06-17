<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

// Abstracte helpercontroller met gedeelde CRUD-hulpmethoden.
abstract class CrudController extends Controller
{
    // Stuurt gebruiker terug naar een route met een succesmelding in de sessie.
    protected function successRedirect(string $route, string $message): RedirectResponse
    {
        return redirect()->route($route)->with('success', $message);
    }
}
