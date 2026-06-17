<x-layouts.stage title="Studenten">
    <section class="section-head">
        <h1>Studenten beheren</h1>
        <a class="btn" href="{{ route('students.create') }}">Student toevoegen</a>
    </section>

    <section class="card">
        <form class="filter-grid" method="GET" action="{{ route('students.index') }}">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Zoek op naam, mail of studentnummer">
            <select name="status">
                <option value="">Alle statussen</option>
                @foreach(['active' => 'Actief', 'inactive' => 'Inactief', 'graduated' => 'Afgestudeerd'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="program">
                <option value="">Alle opleidingen</option>
                @foreach($programs as $program)
                    <option value="{{ $program }}" @selected(($filters['program'] ?? '') === $program)>{{ $program }}</option>
                @endforeach
            </select>
            <button class="btn" type="submit">Filter</button>
        </form>

        <table data-filter-table>
            <thead>
            <tr>
                <th>Nummer</th>
                <th>Naam</th>
                <th>Email</th>
                <th>Opleiding</th>
                <th>Status</th>
                <th>Acties</th>
            </tr>
            </thead>
            <tbody>
            @forelse($students as $student)
                <tr data-search="{{ strtolower($student->full_name.' '.$student->student_number.' '.$student->email) }}">
                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->program }}</td>
                    <td><span class="badge">{{ $student->status }}</span></td>
                    <td class="actions">
                        <a class="btn btn-ghost" href="{{ route('students.edit', $student) }}">Bewerken</a>
                        <form method="POST" action="{{ route('students.destroy', $student) }}" data-confirm-delete>
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Geen studenten gevonden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $students->links() }}
    </section>
</x-layouts.stage>
