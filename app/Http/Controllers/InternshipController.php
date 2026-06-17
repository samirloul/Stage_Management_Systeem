<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// Controller voor stagekoppelingen tussen student en bedrijf.
class InternshipController extends CrudController
{
    // Toont overzicht met filters op titel, status en bedrijf.
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status', 'company_id']);

        $internships = Internship::with(['student', 'company'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhereHas('student', fn ($studentQuery) => $studentQuery->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))
                        ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn ($q, string $status) => $q->where('status', $status))
            ->when($filters['company_id'] ?? null, fn ($q, string $companyId) => $q->where('company_id', (int) $companyId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $companies = Company::orderBy('name')->get();

        return view('internships.index', compact('internships', 'filters', 'companies'));
    }

    // Toont formulier voor nieuwe stagekoppeling.
    public function create(): View
    {
        $students = Student::orderBy('first_name')->get();
        $companies = Company::orderBy('name')->get();

        return view('internships.create', compact('students', 'companies'));
    }

    // Maakt een nieuwe stagekoppeling aan met validatie en foutafhandeling.
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateInternship($request);

        try {
            Internship::create($data);

            return $this->successRedirect('internships.index', 'Stage succesvol gekoppeld.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Opslaan van de stage is mislukt.',
            ]);
        }
    }

    // Toont formulier om een bestaande stage te bewerken.
    public function edit(Internship $internship): View
    {
        $students = Student::orderBy('first_name')->get();
        $companies = Company::orderBy('name')->get();

        return view('internships.edit', compact('internship', 'students', 'companies'));
    }

    // Slaat wijzigingen op voor een bestaande stage.
    public function update(Request $request, Internship $internship): RedirectResponse
    {
        $data = $this->validateInternship($request);

        try {
            $internship->update($data);

            return $this->successRedirect('internships.index', 'Stage succesvol bijgewerkt.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Bijwerken van de stage is mislukt.',
            ]);
        }
    }

    // Verwijdert een stagekoppeling.
    public function destroy(Internship $internship): RedirectResponse
    {
        try {
            $internship->delete();

            return $this->successRedirect('internships.index', 'Stage verwijderd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'general' => 'Verwijderen van de stage is mislukt.',
            ]);
        }
    }

    // Combineert basisvalidatie met extra status-afhankelijke businessregels.
    private function validateInternship(Request $request): array
    {
        // Stap 1: standaard veldvalidatie (type, lengte, required, relatiebestaan, etc.).
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')],
            'company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'description' => ['nullable', 'string', 'min:10', 'max:3000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'hours_per_week' => ['required', 'integer', 'min:8', 'max:40'],
            'mentor_name' => ['nullable', 'string', 'min:2', 'max:120', 'regex:/^[\pL\s\'-]+$/u'],
            'status' => ['required', Rule::in(['planned', 'active', 'completed', 'cancelled'])],
        ], [
            'end_date.after_or_equal' => 'Einddatum moet gelijk of later zijn dan de startdatum.',
            'mentor_name.regex' => 'Naam van begeleider mag alleen letters, spaties, apostrof en koppelteken bevatten.',
            'hours_per_week.min' => 'Uren per week moet minimaal 8 zijn.',
            'hours_per_week.max' => 'Uren per week mag maximaal 40 zijn.',
            'status.in' => 'Kies een geldige status voor de stage.',
        ], [
            'student_id' => 'student',
            'company_id' => 'bedrijf',
            'title' => 'titel',
            'description' => 'beschrijving',
            'start_date' => 'startdatum',
            'end_date' => 'einddatum',
            'hours_per_week' => 'uren per week',
            'mentor_name' => 'begeleider',
            'status' => 'status',
        ]);

        // Stap 2: businessregels die afhangen van meerdere velden tegelijk (status + periode).
        $validator->after(function ($validator) use ($request): void {
            $status = (string) $request->input('status');
            $startDate = $request->date('start_date');
            $endDate = $request->date('end_date');

            if ($startDate === null || $endDate === null) {
                return;
            }

            $today = now()->startOfDay();

            // Gepland mag niet in het verleden starten.
            if ($status === 'planned' && $startDate->lt($today)) {
                $validator->errors()->add('start_date', 'Een geplande stage mag niet in het verleden starten.');
            }

            // Actief betekent: vandaag valt binnen [start, eind].
            if ($status === 'active' && ($startDate->gt($today) || $endDate->lt($today))) {
                $validator->errors()->add('status', 'Bij status actief moet vandaag binnen de stageperiode vallen.');
            }

            // Afgerond betekent: stage is al klaar, dus einddatum niet in de toekomst.
            if ($status === 'completed' && $endDate->gt($today)) {
                $validator->errors()->add('end_date', 'Een afgeronde stage moet een einddatum in het verleden of vandaag hebben.');
            }

            if (in_array($status, ['planned', 'active'], true) && $endDate->lt($today)) {
                $validator->errors()->add('end_date', 'Een geplande of actieve stage mag geen volledig verlopen periode hebben.');
            }
        });

        return $validator->validate();
    }
}
