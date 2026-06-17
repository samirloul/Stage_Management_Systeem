<x-layouts.stage title="Dashboard - Stage Management">
    <section class="hero">
        <div>
            <h1>Stage Management Dashboard</h1>
            <p>Overzicht van studenten, bedrijven, stagekoppelingen en beoordelingen.</p>
        </div>
    </section>

    <section class="stats-grid">
        <article class="card stat-card">
            <h2>Studenten</h2>
            <p class="number">{{ $stats['students'] }}</p>
        </article>
        <article class="card stat-card">
            <h2>Bedrijven</h2>
            <p class="number">{{ $stats['companies'] }}</p>
        </article>
        <article class="card stat-card">
            <h2>Actieve stages</h2>
            <p class="number">{{ $stats['active_internships'] }}</p>
        </article>
        <article class="card stat-card">
            <h2>Gem. score</h2>
            <p class="number">{{ $stats['average_review_score'] ?: 'N/B' }}</p>
        </article>
    </section>

    <section class="card">
        <div class="section-head">
            <h2>Recente stagekoppelingen</h2>
            @if(auth()->user()?->hasRole('admin'))
                <a class="btn" href="{{ route('internships.create') }}">Nieuwe koppeling</a>
            @endif
        </div>

        <table>
            <thead>
            <tr>
                <th>Titel</th>
                <th>Student</th>
                <th>Bedrijf</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse($recentInternships as $internship)
                <tr>
                    <td>{{ $internship->title }}</td>
                    <td>{{ $internship->student->full_name }}</td>
                    <td>{{ $internship->company->name }}</td>
                    <td><span class="badge">{{ $internship->status }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nog geen stagekoppelingen gevonden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.stage>
