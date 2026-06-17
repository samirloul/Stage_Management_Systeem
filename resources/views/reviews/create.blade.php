<x-layouts.stage title="Beoordeling toevoegen">
    <section class="section-head">
        <h1>Nieuwe beoordeling</h1>
        <a class="btn btn-ghost" href="{{ route('reviews.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('reviews.store') }}" class="form-grid">
            @csrf
            @include('reviews.partials.form')
            <button type="submit" class="btn btn-submit">Opslaan</button>
        </form>
    </section>
</x-layouts.stage>
