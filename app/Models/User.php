<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
// Authenticatiemodel voor gebruikers, rollen en profielkoppelingen.
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function role(): BelongsTo
    {
        // Elke gebruiker heeft precies 1 rol.
        return $this->belongsTo(Role::class);
    }

    public function studentProfile(): HasOne
    {
        // Optionele koppeling wanneer gebruiker een student is.
        return $this->hasOne(Student::class);
    }

    public function companyProfile(): HasOne
    {
        // Optionele koppeling wanneer gebruiker een bedrijf is.
        return $this->hasOne(Company::class);
    }

    public function hasRole(string ...$roles): bool
    {
        // Controleert autorisatie op basis van rolnamen.
        return $this->role !== null && in_array($this->role->name, $roles, true);
    }
}
