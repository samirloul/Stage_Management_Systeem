<x-layouts.stage title="Student toevoegen">
    <section class="section-head">
        <h1>Nieuwe student</h1>
        <a class="btn btn-ghost" href="{{ route('students.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('students.store') }}" class="form-grid">
            @csrf
            @include('students.partials.form')
            <button type="submit" class="btn btn-submit">Opslaan</button>
        </form>
    </section>
</x-layouts.stage>
