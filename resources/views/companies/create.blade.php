<x-layouts.stage title="Bedrijf toevoegen">
    <section class="section-head">
        <h1>Nieuw bedrijf</h1>
        <a class="btn btn-ghost" href="{{ route('companies.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('companies.store') }}" class="form-grid">
            @csrf
            @include('companies.partials.form')
            <button type="submit" class="btn btn-submit">Opslaan</button>
        </form>
    </section>
</x-layouts.stage>
