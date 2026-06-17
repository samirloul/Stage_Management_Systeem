<x-layouts.stage title="Student bewerken">
    <section class="section-head">
        <h1>Student bewerken</h1>
        <a class="btn btn-ghost" href="{{ route('students.index') }}">Terug</a>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('students.update', $student) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('students.partials.form', ['student' => $student])
            <button type="submit" class="btn btn-submit">Wijzigingen opslaan</button>
        </form>
    </section>
</x-layouts.stage>
