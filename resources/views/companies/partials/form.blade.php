<label>
    Bedrijfsnaam
    <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required>
</label>
<label>
    Contactpersoon
    <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person ?? '') }}" required>
</label>
<label>
    E-mailadres
    <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}" required>
</label>
<label>
    Telefoon
    <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}">
</label>
<label>
    Plaats
    <input type="text" name="city" value="{{ old('city', $company->city ?? '') }}" required>
</label>
<label>
    Sector
    <input type="text" name="industry" value="{{ old('industry', $company->industry ?? '') }}" required>
</label>
<label>
    Website
    <input type="url" name="website" value="{{ old('website', $company->website ?? '') }}">
</label>
<label>
    Status
    <select name="status" required>
        <option value="active" @selected(old('status', $company->status ?? 'active') === 'active')>Actief</option>
        <option value="inactive" @selected(old('status', $company->status ?? 'active') === 'inactive')>Inactief</option>
    </select>
</label>
