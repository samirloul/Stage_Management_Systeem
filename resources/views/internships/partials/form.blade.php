<label>
    Student
    <select name="student_id" required>
        <option value="">Kies een student</option>
        @foreach($students as $student)
            <option value="{{ $student->id }}" @selected((string)old('student_id', $internship->student_id ?? '') === (string)$student->id)>
                {{ $student->full_name }} ({{ $student->student_number }})
            </option>
        @endforeach
    </select>
</label>
<label>
    Bedrijf
    <select name="company_id" required>
        <option value="">Kies een bedrijf</option>
        @foreach($companies as $company)
            <option value="{{ $company->id }}" @selected((string)old('company_id', $internship->company_id ?? '') === (string)$company->id)>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
</label>
<label>
    Titel van stage
    <input type="text" name="title" value="{{ old('title', $internship->title ?? '') }}" required>
</label>
<label class="full-width">
    Beschrijving
    <textarea name="description" rows="4">{{ old('description', $internship->description ?? '') }}</textarea>
</label>
<label>
    Startdatum
    <input type="date" name="start_date" value="{{ old('start_date', isset($internship) ? $internship->start_date->format('Y-m-d') : '') }}" @if(!isset($internship)) min="{{ now()->format('Y-m-d') }}" @endif required>
</label>
<label>
    Einddatum
    <input type="date" name="end_date" value="{{ old('end_date', isset($internship) ? $internship->end_date->format('Y-m-d') : '') }}" @if(!isset($internship)) min="{{ now()->format('Y-m-d') }}" @endif required>
</label>
<label>
    Uren per week
    <input type="number" name="hours_per_week" value="{{ old('hours_per_week', $internship->hours_per_week ?? 32) }}" min="8" max="40" required>
</label>
<label>
    Begeleider
    <input type="text" name="mentor_name" value="{{ old('mentor_name', $internship->mentor_name ?? '') }}">
</label>
<label>
    Status
    <select name="status" required>
        @foreach(['planned' => 'Gepland', 'active' => 'Actief', 'completed' => 'Afgerond', 'cancelled' => 'Geannuleerd'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $internship->status ?? 'planned') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</label>
<p class="muted">Validatieregel: Gepland start niet in het verleden, actief betekent dat vandaag binnen de periode valt, afgerond betekent dat de einddatum niet in de toekomst ligt.</p>
