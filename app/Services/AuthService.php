<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public function canManageStudents(User $user): bool
    {
        // Alleen admin mag studenten beheren.
        return $user->hasRole('admin');
    }

    public function canManageCompanies(User $user): bool
    {
        // Alleen admin mag bedrijven beheren.
        return $user->hasRole('admin');
    }

    public function canViewInternships(User $user): bool
    {
        // Alle drie rollen mogen stageoverzichten bekijken.
        return $user->hasRole('admin', 'student', 'company');
    }

    public function canWriteReview(User $user): bool
    {
        // Beoordelingen schrijven is beperkt tot admin en bedrijf.
        return $user->hasRole('admin', 'company');
    }

    public function canDeleteAny(User $user): bool
    {
        // Alleen admins mogen records permanent verwijderen - extra veiligheidslaag.
        return $user->hasRole('admin');
    }

}
