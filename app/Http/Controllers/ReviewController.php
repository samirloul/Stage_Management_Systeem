<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// Controller voor beoordelingen op stages.
class ReviewController extends CrudController
{
    // Toont beoordelingenlijst met zoekfilter op stage.
    public function index(Request $request): View
    {
        // Eager loading voorkomt extra queries per tabelrij (performance).
        $reviews = Review::with(['internship.student', 'internship.company', 'reviewer'])
            ->when($request->string('search')->toString(), function ($query, string $search): void {
                // Zoeken gebeurt op stagetitel via relationele whereHas-filter.
                $query->whereHas('internship', fn ($internshipQuery) => $internshipQuery->where('title', 'like', "%{$search}%"));
            })
            ->latest('review_date')
            ->paginate(10)
            ->withQueryString();

        return view('reviews.index', [
            'reviews' => $reviews,
            'search' => $request->string('search')->toString(),
        ]);
    }

    // Toont formulier voor nieuwe beoordeling.
    public function create(): View
    {
        $internships = Internship::with(['student', 'company'])->orderBy('title')->get();

        return view('reviews.create', compact('internships'));
    }

    // Slaat beoordeling op en koppelt automatisch de ingelogde beoordelaar.
    public function store(Request $request): RedirectResponse
    {
        // Valideer invoer voordat de beoordeling wordt opgeslagen.
        $data = $this->validateReview($request);
        // Reviewer-id komt uit de actieve sessie; gebruiker hoeft dit niet handmatig in te vullen.
        $data['reviewer_user_id'] = $request->user()?->id;

        try {
            Review::create($data);

            return $this->successRedirect('reviews.index', 'Beoordeling succesvol toegevoegd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Opslaan van de beoordeling is mislukt.',
            ]);
        }
    }

    // Toont formulier om beoordeling te bewerken.
    public function edit(Review $review): View
    {
        $internships = Internship::with(['student', 'company'])->orderBy('title')->get();

        return view('reviews.edit', compact('review', 'internships'));
    }

    // Werkt beoordeling bij.
    public function update(Request $request, Review $review): RedirectResponse
    {
        $data = $this->validateReview($request);

        try {
            $review->update($data);

            return $this->successRedirect('reviews.index', 'Beoordeling bijgewerkt.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Bijwerken van de beoordeling is mislukt.',
            ]);
        }
    }

    // Verwijdert beoordeling.
    public function destroy(Review $review): RedirectResponse
    {
        try {
            $review->delete();

            return $this->successRedirect('reviews.index', 'Beoordeling verwijderd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'general' => 'Verwijderen van de beoordeling is mislukt.',
            ]);
        }
    }

    // Centrale validatieregels voor beoordelingen.
    private function validateReview(Request $request): array
    {
        // before_or_equal:today blokkeert beoordelingen met toekomstdata.
        return $request->validate([
            'internship_id' => ['required', 'integer', Rule::exists('internships', 'id')],
            'score' => ['required', 'integer', 'min:1', 'max:10'],
            'feedback' => ['required', 'string', 'min:10', 'max:5000'],
            'review_date' => ['required', 'date', 'before_or_equal:today'],
            'recommendation' => ['required', Rule::in(['yes', 'no', 'maybe'])],
        ], [
            'score.min' => 'Score moet minimaal 1 zijn.',
            'score.max' => 'Score mag maximaal 10 zijn.',
            'feedback.min' => 'Feedback moet minimaal 10 tekens bevatten.',
            'review_date.before_or_equal' => 'Beoordelingsdatum mag niet in de toekomst liggen.',
            'recommendation.in' => 'Kies een geldige aanbeveling.',
        ], [
            'internship_id' => 'stage',
            'score' => 'score',
            'feedback' => 'feedback',
            'review_date' => 'beoordelingsdatum',
            'recommendation' => 'aanbeveling',
        ]);
    }
}
