<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use App\Models\Review;
use App\Models\Student;
use Illuminate\View\View;

// Levert alle overzichtsdata voor het dashboard.
class DashboardController extends Controller
{
    // Bouwt KPI-statistieken en recente stagekoppelingen op voor de dashboardweergave.
    public function index(): View
    {
        // Samenvattende cijfers die in de dashboard-cards worden getoond.
        $stats = [
            'students' => Student::query()->count('*'),
            'companies' => Company::query()->count('*'),
            'active_internships' => Internship::query()->active()->count('*'), // Eloquent scope in plaats van raw where voor leesbaarheid.
            'average_review_score' => round((float) Review::avg('score'), 1),
        ];

        // Recente records voor snelle visuele controle door de gebruiker.
        $recentInternships = Internship::with(['student', 'company'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentInternships'));
    }
}
