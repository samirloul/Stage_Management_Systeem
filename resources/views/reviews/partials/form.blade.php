<label class="full-width">
    Stage
    <select name="internship_id" required>
        <option value="">Kies een stage</option>
        @foreach($internships as $internship)
            <option value="{{ $internship->id }}" @selected((string)old('internship_id', $review->internship_id ?? '') === (string)$internship->id)>
                {{ $internship->title }} - {{ $internship->student->full_name }} / {{ $internship->company->name }}
            </option>
        @endforeach
    </select>
</label>
<label>
    Score (1 t/m 10)
    <input type="number" name="score" value="{{ old('score', $review->score ?? 7) }}" min="1" max="10" required>
</label>
<label>
    Datum beoordeling
    <input type="date" name="review_date" value="{{ old('review_date', isset($review) ? $review->review_date->format('Y-m-d') : date('Y-m-d')) }}" required>
</label>
<label>
    Aanbeveling
    <select name="recommendation" required>
        @foreach(['yes' => 'Ja', 'no' => 'Nee', 'maybe' => 'Misschien'] as $value => $label)
            <option value="{{ $value }}" @selected(old('recommendation', $review->recommendation ?? 'maybe') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</label>
<label class="full-width">
    Feedback
    <textarea name="feedback" rows="5" required>{{ old('feedback', $review->feedback ?? '') }}</textarea>
</label>
