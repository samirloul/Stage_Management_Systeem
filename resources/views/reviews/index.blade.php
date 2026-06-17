<x-layouts.stage title="Beoordelingen">
    <section class="section-head">
        <h1>Beoordelingen</h1>
        @if(auth()->user()?->hasRole('admin', 'company'))
            <a class="btn" href="{{ route('reviews.create') }}">Beoordeling toevoegen</a>
        @endif
    </section>

    <section class="card">
        <p class="muted">Aanbeveling betekent: of de beoordelaar deze stage/student positief aanbeveelt (Ja), niet aanbeveelt (Nee), of twijfelt (Misschien).</p>

        <form class="filter-grid" method="GET" action="{{ route('reviews.index') }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Zoek op stage titel">
            <button class="btn" type="submit">Zoeken</button>
        </form>

        <table>
            <thead>
            <tr>
                <th>Stage</th>
                <th>Student</th>
                <th>Bedrijf</th>
                <th>Score</th>
                <th>Aanbeveling</th>
                <th>Feedback</th>
                <th>Acties</th>
            </tr>
            </thead>
            <tbody>
            @forelse($reviews as $review)
                <tr>
                    <td>{{ $review->internship->title }}</td>
                    <td>{{ $review->internship->student->full_name }}</td>
                    <td>{{ $review->internship->company->name }}</td>
                    <td>{{ $review->score }}/10</td>
                    <td><span class="badge">{{ $review->recommendation_label }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($review->feedback, 120) }}</td>
                    <td class="actions">
                        @if(auth()->user()?->hasRole('admin', 'company'))
                            <a class="btn btn-ghost" href="{{ route('reviews.edit', $review) }}">Bewerken</a>
                        @endif
                        @if(auth()->user()?->hasRole('admin'))
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" data-confirm-delete>
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">Verwijderen</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Geen beoordelingen gevonden.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $reviews->links() }}
    </section>
</x-layouts.stage>
