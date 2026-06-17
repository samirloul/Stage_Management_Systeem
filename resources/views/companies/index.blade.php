<x-layouts.stage title="Bedrijven">
    <section class="section-head">
        <h1>Bedrijven beheren</h1>
        <a class="btn" href="{{ route('companies.create') }}">Bedrijf toevoegen</a>
    </section>

    <section class="card">
        <form class="filter-grid" method="GET" action="{{ route('companies.index') }}">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Zoek op naam, contactpersoon of mail">
            <select name="city">
                <option value="">Alle steden</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                @endforeach
            </select>
            <select name="industry">
                <option value="">Alle sectoren</option>
                @foreach($industries as $industry)
                    <option value="{{ $industry }}" @selected(($filters['industry'] ?? '') === $industry)>{{ $industry }}</option>
                @endforeach
            </select>
            <select name="status">
                <option value="">Alle statussen</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Actief</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactief</option>
            </select>
            <button class="btn" type="submit">Filter</button>
        </form>

        <table>
            <thead>
            <tr>
                <th>Naam</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Plaats</th>
                <th>Sector</th>
                <th>Acties</th>
            </tr>
            </thead>
            <tbody>
            @forelse($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->contact_person }}</td>
                    <td>{{ $company->email }}</td>
                    <td>{{ $company->city }}</td>
                    <td>{{ $company->industry }}</td>
                    <td class="actions">
                        <a class="btn btn-ghost" href="{{ route('companies.edit', $company) }}">Bewerken</a>
                        <form method="POST" action="{{ route('companies.destroy', $company) }}" data-confirm-delete>
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Geen bedrijven gevonden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $companies->links() }}
    </section>
</x-layouts.stage>
