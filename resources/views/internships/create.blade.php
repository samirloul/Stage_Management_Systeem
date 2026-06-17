<x-layouts.stage title="Stage toevoegen">
    <section class="section-head">
        <h1>Stage koppelen</h1>
        <a class="btn btn-ghost" href="{{ route('internships.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('internships.store') }}" class="form-grid">
            @csrf
            @include('internships.partials.form')
            <button type="submit" class="btn btn-submit">Opslaan</button>
        </form>
    </section>
</x-layouts.stage>
