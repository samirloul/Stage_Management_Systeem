<x-layouts.stage title="Stages">
    <section class="section-head">
        <h1>Stagekoppelingen</h1>
        @if(auth()->user()?->hasRole('admin'))
            <a class="btn" href="{{ route('internships.create') }}">Nieuwe stage</a>
        @endif
    </section>

    <section class="card">
        <form class="filter-grid" method="GET" action="{{ route('internships.index') }}">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Zoek op titel, student of bedrijf">
            <select name="status">
                <option value="">Alle statussen</option>
                @foreach(['planned' => 'Gepland', 'active' => 'Actief', 'completed' => 'Afgerond', 'cancelled' => 'Geannuleerd'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="company_id">
                <option value="">Alle bedrijven</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string)($filters['company_id'] ?? '') === (string)$company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
            <button class="btn" type="submit">Filter</button>
        </form>

        <table>
            <thead>
            <tr>
                <th>Titel</th>
                <th>Student</th>
                <th>Bedrijf</th>
                <th>Periode</th>
                <th>Status</th>
                <th>Acties</th>
            </tr>
            </thead>
            <tbody>
            @forelse($internships as $internship)
                <tr>
                    <td>{{ $internship->title }}</td>
                    <td>{{ $internship->student->full_name }}</td>
                    <td>{{ $internship->company->name }}</td>
                    <td>{{ $internship->start_date->format('d-m-Y') }} t/m {{ $internship->end_date->format('d-m-Y') }}</td>
                    <td><span class="badge">{{ $internship->status }}</span></td>
                    <td class="actions">
                        @if(auth()->user()?->hasRole('admin'))
                            <a class="btn btn-ghost" href="{{ route('internships.edit', $internship) }}">Bewerken</a>
                            <form method="POST" action="{{ route('internships.destroy', $internship) }}" data-confirm-delete>
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Verwijderen</button>
                            </form>
                        @else
                            <span class="muted">Alleen lezen</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Geen stages gevonden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $internships->links() }}
    </section>
</x-layouts.stage>
