<?php

namespace App\Http\Controllers;

use App\Filters\StudentFilter;
use App\Models\Student;
use App\Services\StudentIdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// Controller voor studentenbeheer inclusief automatische studentidentiteit.
class StudentController extends CrudController
{
    // Service-injectie voor algoritme dat studentnummer en studentmail bepaalt.
    public function __construct(private readonly StudentIdentityService $identityService)
    {
    }

    // Toont studentenlijst met zoek- en filteropties.
    public function index(Request $request, StudentFilter $filter): View
    {
        // Lees alleen toegestane filterinputs uit de request.
        $filters = $request->only(['search', 'status', 'program']);

        // Filterobject verwerkt zoekterm/status/opleiding in 1 centrale plek.
        $students = $filter
            ->apply(Student::query(), $filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Unieke opleidingen voor de filter-dropdown.
        $programs = Student::query()->select('program')->distinct()->orderBy('program')->pluck('program');

        return view('students.index', compact('students', 'filters', 'programs'));
    }

    // Toont formulier voor nieuwe student.
    public function create(): View
    {
        return view('students.create');
    }

    // Maakt student aan met automatisch nummer en e-mailadres.
    public function store(Request $request): RedirectResponse
    {
        // Valideer handmatige velden; studentnummer en email worden later automatisch gezet.
        $data = $this->validateStudentForStore($request);

        try {
            // Retry-loop voorkomt botsingen wanneer gelijktijdige aanvragen hetzelfde nummer proberen te claimen.
            for ($attempt = 0; $attempt < 3; $attempt++) {
                // Genereer eerstvolgend beschikbaar studentnummer (met hergebruik van gaten).
                $studentNumber = $this->identityService->nextStudentNumber();
                $data['student_number'] = $studentNumber;
                // E-mail volgt vaste patroonregel op basis van studentnummer.
                $data['email'] = $this->identityService->emailFromStudentNumber($studentNumber);

                try {
                    Student::create($data);
                    break;
                } catch (QueryException $e) {
                    // Retry when another request claimed the same generated number at the same time.
                    if ($e->getCode() !== '23000' || $attempt === 2) {
                        throw $e;
                    }
                }
            }

            return $this->successRedirect('students.index', 'Student succesvol toegevoegd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Opslaan is mislukt. Probeer het opnieuw.',
            ]);
        }
    }

    // Toont formulier om student te bewerken.
    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }

    // Werkt bestaande studentgegevens bij.
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Update mag studentnummer/email niet aanpassen; alleen profielvelden.
        $data = $this->validateStudentForUpdate($request);

        try {
            $student->update($data);

            return $this->successRedirect('students.index', 'Student succesvol bijgewerkt.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Bijwerken is mislukt. Probeer het opnieuw.',
            ]);
        }
    }

    // Verwijdert studentrecord.
    public function destroy(Student $student): RedirectResponse
    {
        try {
            $student->delete();

            return $this->successRedirect('students.index', 'Student verwijderd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'general' => 'Verwijderen is mislukt. Controleer gekoppelde data.',
            ]);
        }
    }

    // Validatie voor create-flow.
    private function validateStudentForStore(Request $request): array
    {
        return $request->validate(
            $this->studentRules(),
            $this->studentMessages(),
            $this->studentAttributes(),
        );
    }

    // Validatie voor update-flow.
    private function validateStudentForUpdate(Request $request): array
    {
        return $request->validate(
            $this->studentRules(),
            $this->studentMessages(),
            $this->studentAttributes(),
        );
    }

    // Centrale regels voor studentinvoer.
    private function studentRules(): array
    {
        // Regex op program blokkeert cijfers zodat opleiding alleen tekst blijft.
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\'-]+$/u'],
            'last_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\'-]+$/u'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
            'program' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\s\-\/&]+$/u'],
            'start_year' => ['required', 'integer', 'min:2010', 'max:2100'],
            'status' => ['required', Rule::in(['active', 'inactive', 'graduated'])],
        ];
    }

    // Centrale Nederlandstalige foutmeldingen.
    private function studentMessages(): array
    {
        return [
            'first_name.regex' => 'Voornaam mag alleen letters, spaties, apostrof en koppelteken bevatten.',
            'last_name.regex' => 'Achternaam mag alleen letters, spaties, apostrof en koppelteken bevatten.',
            'phone.regex' => 'Telefoonnummer heeft een ongeldig formaat.',
            'program.regex' => 'Opleiding mag geen cijfers bevatten, alleen letters en spaties.',
            'start_year.min' => 'Startjaar moet 2010 of later zijn.',
            'start_year.max' => 'Startjaar mag niet groter dan 2100 zijn.',
            'status.in' => 'Kies een geldige status voor de student.',
        ];
    }

    // Gebruikersvriendelijke veldnamen in validatiefouten.
    private function studentAttributes(): array
    {
        return [
            'first_name' => 'voornaam',
            'last_name' => 'achternaam',
            'phone' => 'telefoonnummer',
            'program' => 'opleiding',
            'start_year' => 'startjaar',
            'status' => 'status',
        ];
    }
}
