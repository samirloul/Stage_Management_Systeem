<x-layouts.stage title="Bedrijf bewerken">
    <section class="section-head">
        <h1>Bedrijf bewerken</h1>
        <a class="btn btn-ghost" href="{{ route('companies.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('companies.update', $company) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('companies.partials.form', ['company' => $company])
            <button type="submit" class="btn btn-submit">Wijzigingen opslaan</button>
        </form>
    </section>
</x-layouts.stage>
