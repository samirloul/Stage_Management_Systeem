<x-layouts.stage title="Stage bewerken">
    <section class="section-head">
        <h1>Stage bewerken</h1>
        <a class="btn btn-ghost" href="{{ route('internships.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('internships.update', $internship) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('internships.partials.form', ['internship' => $internship])
            <button type="submit" class="btn btn-submit">Wijzigingen opslaan</button>
        </form>
    </section>
</x-layouts.stage>
