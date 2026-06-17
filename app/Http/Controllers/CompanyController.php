<?php

namespace App\Http\Controllers;

use App\Filters\CompanyFilter;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// Controller voor beheer van bedrijven (volledige CRUD + filters).
class CompanyController extends CrudController
{
    // Toont lijst met zoek- en filtermogelijkheden.
    public function index(Request $request, CompanyFilter $filter): View
    {
        // Alleen relevante queryparameters worden gebruikt voor filteren.
        $filters = $request->only(['search', 'city', 'industry', 'status']);

        // Filterobject verwerkt de zoek-/filterlogica op een herbruikbare manier.
        $companies = $filter
            ->apply(Company::query(), $filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Deze lijsten voeden de filter-dropdowns in de view.
        $cities = Company::query()->select('city')->distinct()->orderBy('city')->pluck('city');
        $industries = Company::query()->select('industry')->distinct()->orderBy('industry')->pluck('industry');

        return view('companies.index', compact('companies', 'filters', 'cities', 'industries'));
    }

    // Toont formulier voor nieuw bedrijf.
    public function create(): View
    {
        return view('companies.create');
    }

    // Slaat een nieuw bedrijf op na validatie.
    public function store(Request $request): RedirectResponse
    {
        // Eerst server-side valideren voordat data wordt opgeslagen.
        $data = $this->validateCompany($request);

        try {
            // Eloquent create gebruikt intern veilige prepared statements via PDO.
            Company::create($data);

            return $this->successRedirect('companies.index', 'Bedrijf succesvol toegevoegd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Opslaan is mislukt. Probeer het opnieuw.',
            ]);
        }
    }

    // Toont formulier om bestaand bedrijf te bewerken.
    public function edit(Company $company): View
    {
        return view('companies.edit', compact('company'));
    }

    // Werkt bestaand bedrijf bij met gevalideerde input.
    public function update(Request $request, Company $company): RedirectResponse
    {
        // Unique-regels houden rekening met het huidige record via ignore(id).
        $data = $this->validateCompany($request, $company->id);

        try {
            $company->update($data);

            return $this->successRedirect('companies.index', 'Bedrijf succesvol bijgewerkt.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'general' => 'Bijwerken is mislukt. Probeer het opnieuw.',
            ]);
        }
    }

    // Verwijdert bedrijf (met foutafhandeling bij gekoppelde data).
    public function destroy(Company $company): RedirectResponse
    {
        try {
            $company->delete();

            return $this->successRedirect('companies.index', 'Bedrijf verwijderd.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'general' => 'Verwijderen is mislukt. Controleer gekoppelde stages.',
            ]);
        }
    }

    // Centrale validatieset met regels, foutmeldingen en gebruikersvriendelijke veldnamen.
    private function validateCompany(Request $request, ?int $companyId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:180', Rule::unique('companies', 'name')->ignore($companyId)],
            'contact_person' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\s\'-]+$/u'],
            'email' => ['required', 'email:rfc', Rule::unique('companies', 'email')->ignore($companyId)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
            'city' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s\'-]+$/u'],
            'industry' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[\pL\s\-\/&]+$/u'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            // Duidelijke custom foutteksten voor eindgebruiker.
            'contact_person.regex' => 'Contactpersoon mag alleen letters, spaties, apostrof en koppelteken bevatten.',
            'phone.regex' => 'Telefoonnummer heeft een ongeldig formaat.',
            'city.regex' => 'Plaats mag alleen letters en spaties bevatten.',
            'industry.regex' => 'Sector mag geen cijfers bevatten, alleen letters en spaties.',
            'status.in' => 'Kies een geldige status voor het bedrijf.',
        ], [
            // Veldnamen worden leesbaar gemaakt in validatiemeldingen.
            'name' => 'bedrijfsnaam',
            'contact_person' => 'contactpersoon',
            'email' => 'e-mailadres',
            'phone' => 'telefoonnummer',
            'city' => 'plaats',
            'industry' => 'sector',
            'website' => 'website',
            'status' => 'status',
        ]);
    }
}
