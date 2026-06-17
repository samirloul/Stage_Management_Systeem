@if(isset($student))
    <label>
        Studentnummer
        <input type="text" value="{{ $student->student_number }}" readonly>
    </label>
@else
    <p class="muted">Studentnummer wordt automatisch gegenereerd (bijv. S10001).</p>
@endif
<label>
    Voornaam
    <input type="text" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" required>
</label>
<label>
    Achternaam
    <input type="text" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" required>
</label>
@if(isset($student))
    <label>
        E-mailadres
        <input type="email" value="{{ $student->email }}" readonly>
    </label>
@else
    <p class="muted">E-mailadres wordt automatisch: studentnummer@student.local.</p>
@endif
<label>
    Telefoon
    <input type="text" name="phone" value="{{ old('phone', $student->phone ?? '') }}">
</label>
<label>
    Opleiding
    <input type="text" name="program" value="{{ old('program', $student->program ?? '') }}" required>
</label>
<label>
    Startjaar
    <input type="number" name="start_year" value="{{ old('start_year', $student->start_year ?? date('Y')) }}" required>
</label>
<label>
    Status
    <select name="status" required>
        @foreach(['active' => 'Actief', 'inactive' => 'Inactief', 'graduated' => 'Afgestudeerd'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $student->status ?? 'active') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</label>
