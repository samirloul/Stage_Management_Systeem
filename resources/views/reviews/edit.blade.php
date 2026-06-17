<x-layouts.stage title="Beoordeling bewerken">
    <section class="section-head">
        <h1>Beoordeling bewerken</h1>
        <a class="btn btn-ghost" href="{{ route('reviews.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('reviews.update', $review) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('reviews.partials.form', ['review' => $review])
            <button type="submit" class="btn btn-submit">Wijzigingen opslaan</button>
        </form>
    </section>
</x-layouts.stage>
