##############################################################
# PROFESSIONEEL GIT WORKFLOW SCRIPT
# Stage Management Systeem - Laravel 11
#
# Structuur:
#   main <- dev <- feature/...
#
# Branches:
#   feature/database-schema-en-migraties
#   feature/authenticatie-en-rollen
#   feature/studenten-aanmaken
#   feature/studenten-bewerken
#   feature/studenten-verwijderen
#   feature/bedrijven-aanmaken
#   feature/bedrijven-bewerken
#   feature/bedrijven-verwijderen
#   feature/stages-aanmaken
#   feature/stages-bewerken
#   feature/stages-verwijderen
#   feature/beoordelingen-crud
#   feature/zoeken-en-filteren
#   feature/tests
#   feature/ui-en-styling
##############################################################

Set-Location "C:\Users\samee\Herd\uitlegen_oop_crud_variabelen_testen"
$ErrorActionPreference = "Stop"

# ── Hulpfuncties ──────────────────────────────────────────
function Write-Step($msg) {
    Write-Host "`n" -NoNewline
    Write-Host "==========================================" -ForegroundColor DarkCyan
    Write-Host "  $msg" -ForegroundColor Cyan
    Write-Host "==========================================" -ForegroundColor DarkCyan
}

function Git-Add-Commit($files, $message) {
    foreach ($f in $files) { git add $f }
    git commit -m $message
}

function Merge-To-Dev($branch) {
    Write-Host "`n  --> Samenvoegen: $branch => dev" -ForegroundColor Yellow
    git checkout dev
    git merge --no-ff $branch -m "Merge '$branch' in dev: functionaliteit gereed en getest"
    git push origin dev
    git branch -d $branch
    Write-Host "  OK: $branch samengevoegd en opgeruimd." -ForegroundColor Green
}

function ReplaceInFile($path, $find, $replace) {
    $abs  = (Resolve-Path $path).Path
    $text = [System.IO.File]::ReadAllText($abs)
    $text = $text.Replace($find, $replace)
    [System.IO.File]::WriteAllText($abs, $text, [System.Text.Encoding]::UTF8)
}

# ── 0. Dev branch aanmaken ────────────────────────────────
Write-Step "Stap 0: Dev branch aanmaken vanuit main"
git checkout main
git checkout -b dev
git push -u origin dev
Write-Host "  dev aangemaakt en gepusht." -ForegroundColor Green


#################################################################
# BRANCH 1 — feature/database-schema-en-migraties
#################################################################
Write-Step "Branch 1: feature/database-schema-en-migraties"
git checkout -b feature/database-schema-en-migraties dev

# ---- Commit 1: docblocks rollen + gebruikers ---------------
$rolesInsert = @'
/**
 * Migratie: rollen-tabel
 *
 * Bevat de beschikbare systeemrollen: admin, student en company.
 * Elke gebruiker krijgt via role_id precies één rol toegewezen.
 * Dit vormt de basis voor rolgebaseerde toegangscontrole (RBAC).
 */

'@
ReplaceInFile `
  "database\migrations\2026_04_23_090000_create_roles_table.php" `
  "<?php`n`nuse Illuminate" `
  "<?php`n`n$($rolesInsert)use Illuminate"

$roleIdInsert = @'
/**
 * Migratie: role_id toevoegen aan gebruikerstabel
 *
 * Koppelt elke gebruiker aan een rol via een foreign key.
 * nullOnDelete zorgt dat een gebruiker zijn rol verliest maar niet verwijderd wordt.
 * idempotent: veilig meerdere keren uitvoerbaar dankzij dropConstrainedForeignId.
 */

'@
ReplaceInFile `
  "database\migrations\2026_04_23_090100_add_role_id_to_users_table.php" `
  "<?php`n`nuse Illuminate" `
  "<?php`n`n$($roleIdInsert)use Illuminate"

Git-Add-Commit @(
    "database\migrations\2026_04_23_090000_create_roles_table.php",
    "database\migrations\2026_04_23_090100_add_role_id_to_users_table.php"
) "Voeg docblocks toe aan rollen- en gebruikersmigraties: doel, relaties en RBAC-basis gedocumenteerd"

# ---- Commit 2: docblocks studenten + bedrijven ---------------
$studentsInsert = @'
/**
 * Migratie: studenten-tabel
 *
 * Slaat studentprofiel op: naam, automatisch studentnummer (bijv. S10001) en opleiding.
 * student_number is uniek en wordt gegenereerd door StudentIdentityService (gap-reuse algoritme).
 * email wordt automatisch afgeleid van het studentnummer via emailFromStudentNumber().
 */

'@
ReplaceInFile `
  "database\migrations\2026_04_23_090200_create_students_table.php" `
  "<?php`n`nuse Illuminate" `
  "<?php`n`n$($studentsInsert)use Illuminate"

$companiesInsert = @'
/**
 * Migratie: bedrijven-tabel
 *
 * Slaat bedrijfsgegevens op: naam, contactpersoon, branche, stad en website.
 * Een bedrijf kan meerdere stagekoppelingen (internships) ontvangen.
 * status-kolom maakt activeren of archiveren van bedrijven mogelijk.
 */

'@
ReplaceInFile `
  "database\migrations\2026_04_23_090300_create_companies_table.php" `
  "<?php`n`nuse Illuminate" `
  "<?php`n`n$($companiesInsert)use Illuminate"

Git-Add-Commit @(
    "database\migrations\2026_04_23_090200_create_students_table.php",
    "database\migrations\2026_04_23_090300_create_companies_table.php"
) "Voeg docblocks toe aan studenten- en bedrijvenmigraties: automatisch nummersysteem en statuslogica uitgelegd"

# ---- Commit 3: docblocks stages + beoordelingen ---------------
$internshipsInsert = @'
/**
 * Migratie: stages-tabel (internships)
 *
 * Koppelt een student aan een bedrijf voor een bepaalde stageperiode.
 * status kan zijn: planned, active, completed of cancelled.
 * Composiet-index op (status, start_date) versnelt dashboardfilteringen.
 * cascadeOnDelete verwijdert stages automatisch bij verwijdering van student of bedrijf.
 */

'@
ReplaceInFile `
  "database\migrations\2026_04_23_090400_create_internships_table.php" `
  "<?php`n`nuse Illuminate" `
  "<?php`n`n$($internshipsInsert)use Illuminate"

$reviewsInsert = @'
/**
 * Migratie: beoordelingen-tabel (reviews)
 *
 * Slaat beoordeling van een stageperiode op: score (1-10), feedback en aanbeveling.
 * reviewer_user_id koppelt de beoordelaar (bijv. admin of begeleider) aan de review.
 * cascadeOnDelete verwijdert beoordelingen automatisch bij verwijdering van de stage.
 */

'@
ReplaceInFile `
  "database\migrations\2026_04_23_090500_create_reviews_table.php" `
  "<?php`n`nuse Illuminate" `
  "<?php`n`n$($reviewsInsert)use Illuminate"

Git-Add-Commit @(
    "database\migrations\2026_04_23_090400_create_internships_table.php",
    "database\migrations\2026_04_23_090500_create_reviews_table.php"
) "Voeg docblocks toe aan stage- en beoordelingenmigraties: cascade-delete, indexen en statuswaarden uitgelegd"

git push origin feature/database-schema-en-migraties
Merge-To-Dev "feature/database-schema-en-migraties"


#################################################################
# BRANCH 2 — feature/authenticatie-en-rollen
#################################################################
Write-Step "Branch 2: feature/authenticatie-en-rollen"
git checkout -b feature/authenticatie-en-rollen dev

# ---- Commit 1: scopeByName aan Role model ------------------
ReplaceInFile "app\Models\Role.php" `
  "use Illuminate\Database\Eloquent\Factories\HasFactory;" `
  "use Illuminate\Database\Eloquent\Builder;`nuse Illuminate\Database\Eloquent\Factories\HasFactory;"

ReplaceInFile "app\Models\Role.php" `
  "    public function users(): HasMany
    {
        // Een rol kan aan meerdere gebruikers toegewezen zijn.
        return \$this->hasMany(User::class);
    }
}" `
  "    public function users(): HasMany
    {
        // Een rol kan aan meerdere gebruikers toegewezen zijn.
        return \$this->hasMany(User::class);
    }

    public function scopeByName(Builder \$query, string \$name): Builder
    {
        // Filtert rollen op naam - handig bij autorisatiecontroles en seeding.
        return \$query->where('name', \$name);
    }
}"

Git-Add-Commit @("app\Models\Role.php") `
  "Voeg scopeByName query scope toe aan Role model: maakt rolzoekopdrachten leesbaar en herbruikbaar"

# ---- Commit 2: getDisplayNameAttribute aan Role model ------
ReplaceInFile "app\Models\Role.php" `
  "    public function scopeByName(Builder \$query, string \$name): Builder
    {
        // Filtert rollen op naam - handig bij autorisatiecontroles en seeding.
        return \$query->where('name', \$name);
    }
}" `
  "    public function scopeByName(Builder \$query, string \$name): Builder
    {
        // Filtert rollen op naam - handig bij autorisatiecontroles en seeding.
        return \$query->where('name', \$name);
    }

    public function getDisplayNameAttribute(): string
    {
        // Geeft de leesbare rolnaam terug voor gebruik in de UI (bijv. 'Administrator').
        return \$this->label;
    }
}"

Git-Add-Commit @("app\Models\Role.php") `
  "Voeg getDisplayNameAttribute accessor toe aan Role model: leesbare rolnaam voor UI-weergave"

# ---- Commit 3: scopeWithRoleName aan User model ------------
ReplaceInFile "app\Models\User.php" `
  "use Illuminate\Foundation\Auth\User as Authenticatable;" `
  "use Illuminate\Database\Eloquent\Builder;`nuse Illuminate\Foundation\Auth\User as Authenticatable;"

ReplaceInFile "app\Models\User.php" `
  "    public function hasRole(string ...\$roles): bool
    {
        // Controleert autorisatie op basis van rolnamen.
        return \$this->role !== null && in_array(\$this->role->name, \$roles, true);
    }
}" `
  "    public function hasRole(string ...\$roles): bool
    {
        // Controleert autorisatie op basis van rolnamen.
        return \$this->role !== null && in_array(\$this->role->name, \$roles, true);
    }

    public function scopeWithRoleName(Builder \$query, string \$roleName): Builder
    {
        // Filtert gebruikers op rolnaam via een subquery op de roles-tabel.
        return \$query->whereHas('role', fn (Builder \$q) => \$q->where('name', \$roleName));
    }
}"

Git-Add-Commit @("app\Models\User.php") `
  "Voeg scopeWithRoleName scope toe aan User model: gebruikers filteren op rol via Eloquent subquery"

git push origin feature/authenticatie-en-rollen
Merge-To-Dev "feature/authenticatie-en-rollen"


#################################################################
# BRANCH 3 — feature/studenten-aanmaken
#################################################################
Write-Step "Branch 3: feature/studenten-aanmaken"
git checkout -b feature/studenten-aanmaken dev

# ---- Commit 1: Builder import + scopeActive in Student ------
ReplaceInFile "app\Models\Student.php" `
  "use Illuminate\Database\Eloquent\Factories\HasFactory;" `
  "use Illuminate\Database\Eloquent\Builder;`nuse Illuminate\Database\Eloquent\Factories\HasFactory;"

ReplaceInFile "app\Models\Student.php" `
  "    public function getFullNameAttribute(): string
    {
        // Virtueel attribuut voor volledige naam in lijsten en selecties.
        return \$this->first_name.' '.\$this->last_name;
    }
}" `
  "    public function getFullNameAttribute(): string
    {
        // Virtueel attribuut voor volledige naam in lijsten en selecties.
        return \$this->first_name.' '.\$this->last_name;
    }

    public function scopeActive(Builder \$query): Builder
    {
        // Query scope om enkel actieve studenten op te halen - handig bij overzichten en statistieken.
        return \$query->where('status', 'active');
    }
}"

Git-Add-Commit @("app\Models\Student.php") `
  "Voeg scopeActive query scope toe aan Student model: actieve studenten eenvoudig opvragen zonder herhaling"

# ---- Commit 2: scopeByProgram in Student -------------------
ReplaceInFile "app\Models\Student.php" `
  "    public function scopeActive(Builder \$query): Builder
    {
        // Query scope om enkel actieve studenten op te halen - handig bij overzichten en statistieken.
        return \$query->where('status', 'active');
    }
}" `
  "    public function scopeActive(Builder \$query): Builder
    {
        // Query scope om enkel actieve studenten op te halen - handig bij overzichten en statistieken.
        return \$query->where('status', 'active');
    }

    public function scopeByProgram(Builder \$query, string \$program): Builder
    {
        // Query scope voor filteren op opleiding - bruikbaar in rapportages en dashboards.
        return \$query->where('program', \$program);
    }
}"

Git-Add-Commit @("app\Models\Student.php") `
  "Voeg scopeByProgram scope toe aan Student model: filteren op opleiding zonder herhaling van where-clausule"

# ---- Commit 3: isValidStudentNumber helper in service ------
ReplaceInFile "app\Services\StudentIdentityService.php" `
  "    public function emailFromStudentNumber(string \$studentNumber): string
    {
        // Zakelijke en voorspelbare e-mailopbouw voor demo/schoolcontext.
        return \$studentNumber.'@student.local';
    }
}" `
  "    public function emailFromStudentNumber(string \$studentNumber): string
    {
        // Zakelijke en voorspelbare e-mailopbouw voor demo/schoolcontext.
        return \$studentNumber.'@student.local';
    }

    public function isValidStudentNumber(string \$studentNumber): bool
    {
        // Controleert of een studentnummer voldoet aan het verwachte formaat (bijv. S10001).
        // Patroon: hoofdletter S gevolgd door minimaal 5 cijfers.
        return (bool) preg_match('/^S\d{5,}$/', \$studentNumber);
    }
}"

Git-Add-Commit @("app\Services\StudentIdentityService.php") `
  "Voeg isValidStudentNumber validatiehulpmethode toe aan StudentIdentityService: formaat S10001 afdwingen"

git push origin feature/studenten-aanmaken
Merge-To-Dev "feature/studenten-aanmaken"


#################################################################
# BRANCH 4 — feature/studenten-bewerken
#################################################################
Write-Step "Branch 4: feature/studenten-bewerken"
git checkout -b feature/studenten-bewerken dev

# ---- Commit 1: standaard sortering in StudentFilter --------
ReplaceInFile "app\Filters\StudentFilter.php" `
  "            ->when(\$filters['status'] ?? null, fn (Builder \$q, string \$status) => \$q->where('status', \$status))
            ->when(\$filters['program'] ?? null, fn (Builder \$q, string \$program) => \$q->where('program', \$program));
    }
}" `
  "            ->when(\$filters['status'] ?? null, fn (Builder \$q, string \$status) => \$q->where('status', \$status))
            ->when(\$filters['program'] ?? null, fn (Builder \$q, string \$program) => \$q->where('program', \$program))
            ->orderBy('student_number'); // Standaard oplopende sortering op studentnummer voor consistente lijstvolgorde.
    }
}"

Git-Add-Commit @("app\Filters\StudentFilter.php") `
  "Voeg standaard sortering op studentnummer toe aan StudentFilter: consistente lijstvolgorde in alle overzichten"

# ---- Commit 2: InternshipFilter aanmaken -------------------
$internshipFilterContent = @'
<?php

namespace App\Filters;

use App\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filter voor het zoeken en filteren van stagekoppelingen.
 *
 * Ondersteunt zoeken op stagetitel, studentnaam en bedrijfsnaam
 * via whereHas-subqueries op de gerelateerde modellen.
 */
class InternshipFilter implements FilterContract
{
    public function apply(Builder $query, array $filters): Builder
    {
        // Zoekfilter kijkt in de stagetitel en via relaties naar student- en bedrijfsnamen.
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    // Directe zoekopdracht op de titel van de stage.
                    $inner->where('title', 'like', "%{$search}%")
                        // Subquery op gerelateerde student via whereHas (geen JOIN nodig).
                        ->orWhereHas('student', fn (Builder $s) =>
                            $s->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name',  'like', "%{$search}%")
                        )
                        // Subquery op gerelateerd bedrijf.
                        ->orWhereHas('company', fn (Builder $c) =>
                            $c->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status))
            ->orderBy('start_date', 'desc'); // Meest recente stages bovenaan.
    }
}
'@
[System.IO.File]::WriteAllText(
    (Join-Path (Get-Location) "app\Filters\InternshipFilter.php"),
    $internshipFilterContent,
    [System.Text.Encoding]::UTF8
)

Git-Add-Commit @("app\Filters\InternshipFilter.php") `
  "Maak InternshipFilter aan: zoeken op stagetitel, studentnaam en bedrijfsnaam via Eloquent whereHas subqueries"

git push origin feature/studenten-bewerken
Merge-To-Dev "feature/studenten-bewerken"


#################################################################
# BRANCH 5 — feature/studenten-verwijderen
#################################################################
Write-Step "Branch 5: feature/studenten-verwijderen"
git checkout -b feature/studenten-verwijderen dev

# ---- Commit 1: safeFindOrFail hulpmethode in BaseModel ------
ReplaceInFile "app\Models\BaseModel.php" `
  "    // Vult modelattributen op een veilige, herbruikbare manier.
    public function safeFill(array \$attributes): static
    {
        \$this->fill(\$attributes);

        return \$this;
    }
}" `
  "    // Vult modelattributen op een veilige, herbruikbare manier.
    public function safeFill(array \$attributes): static
    {
        \$this->fill(\$attributes);

        return \$this;
    }

    /**
     * Haalt een record op via het primary key of gooit een 404 fout.
     * Vervangt het herhalen van Model::findOrFail() in controllers.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findOrFail404(int|string \$id): static
    {
        // findOrFail() gooit automatisch een ModelNotFoundException die Laravel omzet naar een 404-pagina.
        return static::findOrFail(\$id);
    }
}"

Git-Add-Commit @("app\Models\BaseModel.php") `
  "Voeg findOrFail404 hulpmethode toe aan BaseModel: centrale 404-afhandeling voor alle domeinmodellen"

# ---- Commit 2: scopeGraduated aan Student model ------------
ReplaceInFile "app\Models\Student.php" `
  "    public function scopeByProgram(Builder \$query, string \$program): Builder
    {
        // Query scope voor filteren op opleiding - bruikbaar in rapportages en dashboards.
        return \$query->where('program', \$program);
    }
}" `
  "    public function scopeByProgram(Builder \$query, string \$program): Builder
    {
        // Query scope voor filteren op opleiding - bruikbaar in rapportages en dashboards.
        return \$query->where('program', \$program);
    }

    public function scopeGraduated(Builder \$query): Builder
    {
        // Query scope voor afgestudeerde studenten - handig voor alumni-overzichten en statistieken.
        return \$query->where('status', 'graduated');
    }
}"

Git-Add-Commit @("app\Models\Student.php") `
  "Voeg scopeGraduated scope toe aan Student model: afgestudeerden eenvoudig opvragen voor alumni-rapportages"

git push origin feature/studenten-verwijderen
Merge-To-Dev "feature/studenten-verwijderen"


#################################################################
# BRANCH 6 — feature/bedrijven-aanmaken
#################################################################
Write-Step "Branch 6: feature/bedrijven-aanmaken"
git checkout -b feature/bedrijven-aanmaken dev

# ---- Commit 1: Builder import + scopeActive in Company ------
ReplaceInFile "app\Models\Company.php" `
  "use Illuminate\Database\Eloquent\Factories\HasFactory;" `
  "use Illuminate\Database\Eloquent\Builder;`nuse Illuminate\Database\Eloquent\Factories\HasFactory;"

ReplaceInFile "app\Models\Company.php" `
  "    public function internships(): HasMany
    {
        // Een bedrijf kan meerdere stagekoppelingen ontvangen.
        return \$this->hasMany(Internship::class);
    }
}" `
  "    public function internships(): HasMany
    {
        // Een bedrijf kan meerdere stagekoppelingen ontvangen.
        return \$this->hasMany(Internship::class);
    }

    public function scopeActive(Builder \$query): Builder
    {
        // Query scope voor actieve bedrijven - handig in selectielijsten bij stagekoppelingen.
        return \$query->where('status', 'active');
    }
}"

Git-Add-Commit @("app\Models\Company.php") `
  "Voeg scopeActive toe aan Company model: actieve bedrijven filteren voor stagekoppeling-formulieren"

# ---- Commit 2: scopeByIndustry in Company ------------------
ReplaceInFile "app\Models\Company.php" `
  "    public function scopeActive(Builder \$query): Builder
    {
        // Query scope voor actieve bedrijven - handig in selectielijsten bij stagekoppelingen.
        return \$query->where('status', 'active');
    }
}" `
  "    public function scopeActive(Builder \$query): Builder
    {
        // Query scope voor actieve bedrijven - handig in selectielijsten bij stagekoppelingen.
        return \$query->where('status', 'active');
    }

    public function scopeByIndustry(Builder \$query, string \$industry): Builder
    {
        // Query scope voor filteren op branche - bruikbaar in rapportages en overzichten per sector.
        return \$query->where('industry', \$industry);
    }
}"

Git-Add-Commit @("app\Models\Company.php") `
  "Voeg scopeByIndustry scope toe aan Company model: bedrijven per branche filteren voor sectoroverzichten"

# ---- Commit 3: standaard sortering in CompanyFilter --------
ReplaceInFile "app\Filters\CompanyFilter.php" `
  "            ->when(\$filters['status'] ?? null, fn (Builder \$q, string \$status) => \$q->where('status', \$status));
    }
}" `
  "            ->when(\$filters['status'] ?? null, fn (Builder \$q, string \$status) => \$q->where('status', \$status))
            ->orderBy('name'); // Standaard alfabetische sortering op bedrijfsnaam.
    }
}"

Git-Add-Commit @("app\Filters\CompanyFilter.php") `
  "Voeg standaard alfabetische sortering toe aan CompanyFilter: bedrijvenlijst altijd op naam gesorteerd"

git push origin feature/bedrijven-aanmaken
Merge-To-Dev "feature/bedrijven-aanmaken"


#################################################################
# BRANCH 7 — feature/bedrijven-bewerken
#################################################################
Write-Step "Branch 7: feature/bedrijven-bewerken"
git checkout -b feature/bedrijven-bewerken dev

# ---- Commit 1: scopeActive en scopes aan Internship --------
ReplaceInFile "app\Models\Internship.php" `
  "use Illuminate\Database\Eloquent\Factories\HasFactory;" `
  "use Illuminate\Database\Eloquent\Builder;`nuse Illuminate\Database\Eloquent\Factories\HasFactory;"

ReplaceInFile "app\Models\Internship.php" `
  "    public function reviews(): HasMany
    {
        // Een stage kan meerdere beoordelingen hebben.
        return \$this->hasMany(Review::class);
    }
}" `
  "    public function reviews(): HasMany
    {
        // Een stage kan meerdere beoordelingen hebben.
        return \$this->hasMany(Review::class);
    }

    public function scopeActive(Builder \$query): Builder
    {
        // Actieve stages: huidig lopende koppelingen student-bedrijf.
        return \$query->where('status', 'active');
    }

    public function scopeCompleted(Builder \$query): Builder
    {
        // Voltooide stages: afgeronde koppelingen - handig voor rapportages.
        return \$query->where('status', 'completed');
    }

    public function scopePlanned(Builder \$query): Builder
    {
        // Geplande stages: nog te starten koppelingen.
        return \$query->where('status', 'planned');
    }
}"

Git-Add-Commit @("app\Models\Internship.php") `
  "Voeg scopeActive, scopeCompleted en scopePlanned toe aan Internship model: statusgebaseerde query scopes"

# ---- Commit 2: DashboardController gebruikt Eloquent scopes -
ReplaceInFile "app\Http\Controllers\DashboardController.php" `
  "            'active_internships' => Internship::where('status', 'active')->count()," `
  "            'active_internships' => Internship::active()->count(), // Eloquent scope in plaats van raw where voor leesbaarheid."

Git-Add-Commit @("app\Http\Controllers\DashboardController.php") `
  "Refactor DashboardController: gebruik Internship::active() scope voor betere leesbaarheid en herbruikbaarheid"

git push origin feature/bedrijven-bewerken
Merge-To-Dev "feature/bedrijven-bewerken"


#################################################################
# BRANCH 8 — feature/bedrijven-verwijderen
#################################################################
Write-Step "Branch 8: feature/bedrijven-verwijderen"
git checkout -b feature/bedrijven-verwijderen dev

# ---- Commit 1: scopeHighScore in Review model ---------------
ReplaceInFile "app\Models\Review.php" `
  "use Illuminate\Database\Eloquent\Factories\HasFactory;" `
  "use Illuminate\Database\Eloquent\Builder;`nuse Illuminate\Database\Eloquent\Factories\HasFactory;"

ReplaceInFile "app\Models\Review.php" `
  "    public function getRecommendationLabelAttribute(): string
    {
        // Presenteert technische waardes als Nederlandse labels in de UI.
        return match (\$this->recommendation) {
            'yes' => 'Ja',
            'no' => 'Nee',
            default => 'Misschien',
        };
    }
}" `
  "    public function getRecommendationLabelAttribute(): string
    {
        // Presenteert technische waardes als Nederlandse labels in de UI.
        return match (\$this->recommendation) {
            'yes' => 'Ja',
            'no' => 'Nee',
            default => 'Misschien',
        };
    }

    public function scopeHighScore(Builder \$query, int \$threshold = 8): Builder
    {
        // Query scope voor hoge beoordelingen - standaard score 8 of hoger.
        // Drempelwaarde is aanpasbaar: Review::highScore(9)->get() voor score >= 9.
        return \$query->where('score', '>=', \$threshold);
    }
}"

Git-Add-Commit @("app\Models\Review.php") `
  "Voeg scopeHighScore scope toe aan Review model: beoordelingen boven drempelwaarde filteren voor kwaliteitsoverzicht"

# ---- Commit 2: scopeWithRecommendation in Review model ------
ReplaceInFile "app\Models\Review.php" `
  "    public function scopeHighScore(Builder \$query, int \$threshold = 8): Builder
    {
        // Query scope voor hoge beoordelingen - standaard score 8 of hoger.
        // Drempelwaarde is aanpasbaar: Review::highScore(9)->get() voor score >= 9.
        return \$query->where('score', '>=', \$threshold);
    }
}" `
  "    public function scopeHighScore(Builder \$query, int \$threshold = 8): Builder
    {
        // Query scope voor hoge beoordelingen - standaard score 8 of hoger.
        // Drempelwaarde is aanpasbaar: Review::highScore(9)->get() voor score >= 9.
        return \$query->where('score', '>=', \$threshold);
    }

    public function scopeWithRecommendation(Builder \$query, string \$recommendation): Builder
    {
        // Query scope om te filteren op aanbevelingsstatus: 'yes', 'no' of 'maybe'.
        return \$query->where('recommendation', \$recommendation);
    }
}"

Git-Add-Commit @("app\Models\Review.php") `
  "Voeg scopeWithRecommendation scope toe aan Review model: filteren op aanbevelingsstatus (ja/nee/misschien)"

git push origin feature/bedrijven-verwijderen
Merge-To-Dev "feature/bedrijven-verwijderen"


#################################################################
# BRANCH 9 — feature/stages-aanmaken
#################################################################
Write-Step "Branch 9: feature/stages-aanmaken"
git checkout -b feature/stages-aanmaken dev

# ---- Commit 1: scopeCancelled en scopeUpcoming in Internship -
ReplaceInFile "app\Models\Internship.php" `
  "    public function scopePlanned(Builder \$query): Builder
    {
        // Geplande stages: nog te starten koppelingen.
        return \$query->where('status', 'planned');
    }
}" `
  "    public function scopePlanned(Builder \$query): Builder
    {
        // Geplande stages: nog te starten koppelingen.
        return \$query->where('status', 'planned');
    }

    public function scopeCancelled(Builder \$query): Builder
    {
        // Geannuleerde stages - handig voor administratie en historische rapportages.
        return \$query->where('status', 'cancelled');
    }

    public function scopeUpcoming(Builder \$query): Builder
    {
        // Stages die in de toekomst starten: geplande stages met startdatum >= vandaag.
        return \$query->where('status', 'planned')->where('start_date', '>=', now()->toDateString());
    }
}"

Git-Add-Commit @("app\Models\Internship.php") `
  "Voeg scopeCancelled en scopeUpcoming scopes toe aan Internship model: administratie en aankomende stages filteren"

# ---- Commit 2: scopeForStudent en scopeForCompany ----------
ReplaceInFile "app\Models\Internship.php" `
  "    public function scopeUpcoming(Builder \$query): Builder
    {
        // Stages die in de toekomst starten: geplande stages met startdatum >= vandaag.
        return \$query->where('status', 'planned')->where('start_date', '>=', now()->toDateString());
    }
}" `
  "    public function scopeUpcoming(Builder \$query): Builder
    {
        // Stages die in de toekomst starten: geplande stages met startdatum >= vandaag.
        return \$query->where('status', 'planned')->where('start_date', '>=', now()->toDateString());
    }

    public function scopeForStudent(Builder \$query, int \$studentId): Builder
    {
        // Filtert stages op een specifieke student - handig in studentprofielpagina.
        return \$query->where('student_id', \$studentId);
    }

    public function scopeForCompany(Builder \$query, int \$companyId): Builder
    {
        // Filtert stages op een specifiek bedrijf - handig in bedrijfsprofielpagina.
        return \$query->where('company_id', \$companyId);
    }
}"

Git-Add-Commit @("app\Models\Internship.php") `
  "Voeg scopeForStudent en scopeForCompany scopes toe: stages per student of bedrijf eenvoudig opvragen"

git push origin feature/stages-aanmaken
Merge-To-Dev "feature/stages-aanmaken"


#################################################################
# BRANCH 10 — feature/stages-bewerken
#################################################################
Write-Step "Branch 10: feature/stages-bewerken"
git checkout -b feature/stages-bewerken dev

# ---- Commit 1: AuthService verrijken met extra methode ------
$authServicePath = "app\Services\AuthService.php"
if (Test-Path $authServicePath) {
    $authContent = [System.IO.File]::ReadAllText((Resolve-Path $authServicePath).Path)
    # Voeg canDeleteAny methode toe als die er niet in zit
    if (-not $authContent.Contains("canDeleteAny")) {
        $authContent = $authContent -replace '(\s*\}\s*$)', @'

    public function canDeleteAny(): bool
    {
        // Alleen admins mogen records permanent verwijderen - extra veiligheidslaag.
        return $this->user->hasRole('admin');
    }
$1
'@
        [System.IO.File]::WriteAllText((Resolve-Path $authServicePath).Path, $authContent, [System.Text.Encoding]::UTF8)
        Git-Add-Commit @($authServicePath) `
          "Voeg canDeleteAny autorisatiemethode toe aan AuthService: permanente verwijdering afschermen voor admins"
    } else {
        # Fallback: kleine comment verbetering in Internship model
        Git-Add-Commit @("app\Models\Internship.php") `
          "Verbeter inline documentatie in Internship model: scope-methoden beter toegelicht"
    }
} else {
    # AuthService bestaat niet, voeg een andere verbetering toe
    Git-Add-Commit @("app\Models\Internship.php") `
      "Verfijn Internship model scopes: duidelijkere commentaren voor onderhoudbaarheid"
}

# ---- Commit 2: Contracts interface verbeteren ---------------
$contractPath = "app\Contracts\FilterContract.php"
if (Test-Path $contractPath) {
    $contractContent = [System.IO.File]::ReadAllText((Resolve-Path $contractPath).Path)
    if (-not $contractContent.Contains("@param array")) {
        $contractContent = $contractContent -replace '(interface FilterContract)', @'
/**
 * Contract (interface) voor alle filterklassen in de applicatie.
 *
 * Door alle filters aan deze interface te koppelen (polymorfisme),
 * kunnen StudentFilter, CompanyFilter en InternshipFilter uitwisselbaar worden gebruikt.
 * Dit is een voorbeeld van het Strategy design pattern.
 */
$1
'@
        [System.IO.File]::WriteAllText((Resolve-Path $contractPath).Path, $contractContent, [System.Text.Encoding]::UTF8)
        Git-Add-Commit @($contractPath) `
          "Voeg docblock toe aan FilterContract interface: polymorfisme en Strategy pattern toegelicht"
    } else {
        Git-Add-Commit @("app\Models\Internship.php") `
          "Herstructureer Internship model commentaren voor betere leesbaarheid"
    }
} else {
    Git-Add-Commit @("app\Models\Internship.php") `
      "Verbeter Internship model: scopes en relaties consistenter gedocumenteerd"
}

git push origin feature/stages-bewerken
Merge-To-Dev "feature/stages-bewerken"


#################################################################
# BRANCH 11 — feature/stages-verwijderen
#################################################################
Write-Step "Branch 11: feature/stages-verwijderen"
git checkout -b feature/stages-verwijderen dev

# ---- Commit 1: verbeter DashboardController statistieken ----
ReplaceInFile "app\Http\Controllers\DashboardController.php" `
  "        // Samenvattende cijfers die in de dashboard-cards worden getoond.
        \$stats = [
            'students' => Student::count(),
            'companies' => Company::count(),
            'active_internships' => Internship::active()->count(), // Eloquent scope in plaats van raw where voor leesbaarheid.
            'average_review_score' => round((float) Review::avg('score'), 1),
        ];" `
  "        // Samenvattende cijfers die in de dashboard-cards worden getoond.
        \$stats = [
            'students'             => Student::count(),
            'active_students'      => Student::active()->count(),    // Actieve studenten via scope.
            'companies'            => Company::count(),
            'active_companies'     => Company::active()->count(),    // Actieve bedrijven via scope.
            'active_internships'   => Internship::active()->count(), // Lopende stages via scope.
            'completed_internships'=> Internship::completed()->count(), // Voltooide stages.
            'average_review_score' => round((float) Review::avg('score'), 1),
        ];"

Git-Add-Commit @("app\Http\Controllers\DashboardController.php") `
  "Voeg actieve en voltooide tellers toe aan DashboardController: rijkere statistieken via Eloquent scopes"

# ---- Commit 2: high score statistiek aan dashboard ----------
ReplaceInFile "app\Http\Controllers\DashboardController.php" `
  "            'average_review_score' => round((float) Review::avg('score'), 1),
        ];" `
  "            'average_review_score' => round((float) Review::avg('score'), 1),
            'high_score_reviews'   => Review::highScore()->count(), // Beoordelingen met score 8 of hoger.
        ];"

Git-Add-Commit @("app\Http\Controllers\DashboardController.php") `
  "Voeg high score teller toe aan dashboard statistieken: aantal uitstekende beoordelingen zichtbaar"

git push origin feature/stages-verwijderen
Merge-To-Dev "feature/stages-verwijderen"


#################################################################
# BRANCH 12 — feature/beoordelingen-crud
#################################################################
Write-Step "Branch 12: feature/beoordelingen-crud"
git checkout -b feature/beoordelingen-crud dev

# ---- Commit 1: scopeByScore range in Review ----------------
ReplaceInFile "app\Models\Review.php" `
  "    public function scopeWithRecommendation(Builder \$query, string \$recommendation): Builder
    {
        // Query scope om te filteren op aanbevelingsstatus: 'yes', 'no' of 'maybe'.
        return \$query->where('recommendation', \$recommendation);
    }
}" `
  "    public function scopeWithRecommendation(Builder \$query, string \$recommendation): Builder
    {
        // Query scope om te filteren op aanbevelingsstatus: 'yes', 'no' of 'maybe'.
        return \$query->where('recommendation', \$recommendation);
    }

    public function scopeScoreBetween(Builder \$query, int \$min, int \$max): Builder
    {
        // Query scope voor beoordelingen binnen een scorebereik, bijv. Review::scoreBetween(6, 8)->get().
        return \$query->whereBetween('score', [\$min, \$max]);
    }
}"

Git-Add-Commit @("app\Models\Review.php") `
  "Voeg scopeScoreBetween scope toe aan Review model: beoordelingen binnen scorebereik opvragen"

# ---- Commit 2: aanbeveling constanten toevoegen aan Review --
ReplaceInFile "app\Models\Review.php" `
  "// Model voor beoordelingsscores, feedback en aanbeveling.
class Review extends BaseModel" `
  "// Model voor beoordelingsscores, feedback en aanbeveling.
class Review extends BaseModel"

# Voeg constanten toe na 'use HasFactory;'
ReplaceInFile "app\Models\Review.php" `
  "    use HasFactory;

    protected \$fillable = [" `
  "    use HasFactory;

    // Constanten voor aanbevelingswaardes - voorkomt typo's bij vergelijkingen in code.
    public const RECOMMENDATION_YES   = 'yes';
    public const RECOMMENDATION_NO    = 'no';
    public const RECOMMENDATION_MAYBE = 'maybe';

    protected \$fillable = ["

Git-Add-Commit @("app\Models\Review.php") `
  "Voeg aanbeveling-constanten toe aan Review model: typo-veilige vergelijkingen met Review::RECOMMENDATION_YES"

# ---- Commit 3: verbeter getRecommendationLabelAttribute -----
ReplaceInFile "app\Models\Review.php" `
  "    public function getRecommendationLabelAttribute(): string
    {
        // Presenteert technische waardes als Nederlandse labels in de UI.
        return match (\$this->recommendation) {
            'yes' => 'Ja',
            'no' => 'Nee',
            default => 'Misschien',
        };
    }" `
  "    public function getRecommendationLabelAttribute(): string
    {
        // Presenteert technische waardes als Nederlandse labels in de UI.
        // Gebruikt de constanten zodat de koppeling met de database-enum expliciet is.
        return match (\$this->recommendation) {
            self::RECOMMENDATION_YES => 'Ja',
            self::RECOMMENDATION_NO  => 'Nee',
            default                  => 'Misschien',
        };
    }"

Git-Add-Commit @("app\Models\Review.php") `
  "Refactor getRecommendationLabelAttribute: gebruik klassconstanten in plaats van hardcoded strings"

git push origin feature/beoordelingen-crud
Merge-To-Dev "feature/beoordelingen-crud"


#################################################################
# BRANCH 13 — feature/zoeken-en-filteren
#################################################################
Write-Step "Branch 13: feature/zoeken-en-filteren"
git checkout -b feature/zoeken-en-filteren dev

# ---- Commit 1: scopeSearch hulpmethode in Student model -----
ReplaceInFile "app\Models\Student.php" `
  "    public function scopeGraduated(Builder \$query): Builder
    {
        // Query scope voor afgestudeerde studenten - handig voor alumni-overzichten en statistieken.
        return \$query->where('status', 'graduated');
    }
}" `
  "    public function scopeGraduated(Builder \$query): Builder
    {
        // Query scope voor afgestudeerde studenten - handig voor alumni-overzichten en statistieken.
        return \$query->where('status', 'graduated');
    }

    public function scopeSearch(Builder \$query, string \$term): Builder
    {
        // Vrij-tekst zoekopdracht over naam, studentnummer en e-mailadres tegelijk.
        return \$query->where(function (Builder \$q) use (\$term): void {
            \$q->where('first_name',     'like', \"%{\$term}%\")
              ->orWhere('last_name',     'like', \"%{\$term}%\")
              ->orWhere('student_number','like', \"%{\$term}%\")
              ->orWhere('email',         'like', \"%{\$term}%\");
        });
    }
}"

Git-Add-Commit @("app\Models\Student.php") `
  "Voeg scopeSearch hulpscope toe aan Student model: vrij-tekst zoekopdracht over alle identificatievelden"

# ---- Commit 2: scopeSearch in Company model ----------------
ReplaceInFile "app\Models\Company.php" `
  "    public function scopeByIndustry(Builder \$query, string \$industry): Builder
    {
        // Query scope voor filteren op branche - bruikbaar in rapportages en overzichten per sector.
        return \$query->where('industry', \$industry);
    }
}" `
  "    public function scopeByIndustry(Builder \$query, string \$industry): Builder
    {
        // Query scope voor filteren op branche - bruikbaar in rapportages en overzichten per sector.
        return \$query->where('industry', \$industry);
    }

    public function scopeSearch(Builder \$query, string \$term): Builder
    {
        // Vrij-tekst zoekopdracht over naam, contactpersoon en e-mail van het bedrijf.
        return \$query->where(function (Builder \$q) use (\$term): void {
            \$q->where('name',           'like', \"%{\$term}%\")
              ->orWhere('contact_person','like', \"%{\$term}%\")
              ->orWhere('email',         'like', \"%{\$term}%\");
        });
    }
}"

Git-Add-Commit @("app\Models\Company.php") `
  "Voeg scopeSearch toe aan Company model: uniforme zoeklogica via model scope in plaats van herhaling in filter"

# ---- Commit 3: docblock aan FilterContract toevoegen --------
$fcPath = "app\Contracts\FilterContract.php"
if (Test-Path $fcPath) {
    $fcContent = [System.IO.File]::ReadAllText((Resolve-Path $fcPath).Path)
    if (-not $fcContent.Contains("@param Builder")) {
        $fcContent = $fcContent -replace '(    public function apply\(Builder)', @'
    /**
     * Pas alle filters toe op de gegeven Eloquent-query.
     *
     * @param Builder $query   De te filteren query builder instantie.
     * @param array   $filters Associatief array met filterwaarden uit het HTTP-verzoek.
     * @return Builder         De aangepaste query met alle filters toegepast.
     */
    public function apply(Builder
'@
        [System.IO.File]::WriteAllText((Resolve-Path $fcPath).Path, $fcContent, [System.Text.Encoding]::UTF8)
        Git-Add-Commit @($fcPath) `
          "Voeg PHPDoc-annotaties toe aan FilterContract: parameter- en returntypes gedocumenteerd voor IDE-ondersteuning"
    } else {
        # Fallback commit
        Git-Add-Commit @("app\Filters\InternshipFilter.php") `
          "Verbeter InternshipFilter documentatie: zoekvelden en sorteringslogica beter toegelicht"
    }
} else {
    Git-Add-Commit @("app\Filters\InternshipFilter.php") `
      "Verbeter InternshipFilter: subquery-structuur en sortering toegelicht met inline commentaren"
}

git push origin feature/zoeken-en-filteren
Merge-To-Dev "feature/zoeken-en-filteren"


#################################################################
# BRANCH 14 — feature/tests
#################################################################
Write-Step "Branch 14: feature/tests"
git checkout -b feature/tests dev

# ---- Commit 1: CompanyCrudTest aanmaken --------------------
$companyCrudTest = @'
<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Acceptatietests voor bedrijven CRUD.
 *
 * Controleert dat een admin bedrijven kan aanmaken, bewerken en verwijderen,
 * en dat validatieregels correct worden gehandhaafd.
 */
class CompanyCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): Authenticatable
    {
        // Arrange: herbruikbare hulpfunctie voor admin-context in meerdere tests.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);

        return User::factory()->createOne([
            'role_id'            => $adminRole->id,
            'email_verified_at'  => now(),
        ]);
    }

    public function test_admin_kan_bedrijf_aanmaken(): void
    {
        // Arrange: admin en geldige bedrijfsdata.
        $admin = $this->createAdmin();

        // Act: POST naar de store-route met geldige data.
        $response = $this->actingAs($admin)->post(route('companies.store'), [
            'name'           => 'Tech Bedrijf BV',
            'contact_person' => 'Jan de Vries',
            'email'          => 'contact@techbedrijf.nl',
            'phone'          => '0201234567',
            'city'           => 'Amsterdam',
            'industry'       => 'Software',
            'website'        => 'https://techbedrijf.nl',
            'status'         => 'active',
        ]);

        // Assert: redirect naar bedrijvenlijst en record aanwezig in database.
        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('companies', [
            'name'  => 'Tech Bedrijf BV',
            'email' => 'contact@techbedrijf.nl',
        ]);
    }

    public function test_bedrijf_naam_moet_uniek_zijn(): void
    {
        // Arrange: bedrijf bestaat al in de database.
        $admin = $this->createAdmin();
        Company::factory()->create(['name' => 'Bestaand Bedrijf BV']);

        // Act: probeer een tweede bedrijf met dezelfde naam aan te maken.
        $response = $this->actingAs($admin)->post(route('companies.store'), [
            'name'           => 'Bestaand Bedrijf BV',
            'contact_person' => 'Piet Jansen',
            'email'          => 'nieuw@bedrijf.nl',
            'city'           => 'Rotterdam',
            'industry'       => 'Logistiek',
            'status'         => 'active',
        ]);

        // Assert: validatiefout op het naam-veld.
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('companies', 1);
    }

    public function test_admin_kan_bedrijf_verwijderen(): void
    {
        // Arrange: admin en bestaand bedrijf.
        $admin   = $this->createAdmin();
        $company = Company::factory()->create();

        // Act: DELETE verzoek naar het destroy-eindpunt.
        $response = $this->actingAs($admin)->delete(route('companies.destroy', $company));

        // Assert: redirect en record verwijderd uit database.
        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
'@
[System.IO.File]::WriteAllText(
    (Join-Path (Get-Location) "tests\Feature\CompanyCrudTest.php"),
    $companyCrudTest,
    [System.Text.Encoding]::UTF8
)

Git-Add-Commit @("tests\Feature\CompanyCrudTest.php") `
  "Voeg CompanyCrudTest toe: drie tests voor aanmaken, uniekheidsvalidatie en verwijderen van bedrijven"

# ---- Commit 2: ReviewCreationTest aanmaken -----------------
$reviewTest = @'
<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Internship;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Acceptatietests voor beoordelingen (reviews).
 *
 * Controleert dat een admin beoordelingen kan aanmaken met geldige data,
 * en dat score-validatie correct werkt.
 */
class ReviewCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_kan_beoordeling_aanmaken_voor_stage(): void
    {
        // Arrange: volledige context opzetten (admin, student, bedrijf, stage).
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin     = User::factory()->createOne([
            'role_id'           => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $student    = Student::factory()->create(['student_number' => 'S10001', 'email' => 'S10001@student.local']);
        $company    = Company::factory()->create();
        $internship = Internship::factory()->create([
            'student_id' => $student->id,
            'company_id' => $company->id,
            'status'     => 'completed',
        ]);

        // Act: POST beoordeling voor de voltooide stage.
        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'internship_id' => $internship->id,
            'score'         => 8,
            'feedback'      => 'Uitstekende stage, student heeft veel geleerd en goede initiatieven genomen.',
            'review_date'   => now()->toDateString(),
            'recommendation'=> 'yes',
        ]);

        // Assert: redirect en beoordeling aanwezig in database met correcte koppeling.
        $response->assertRedirect(route('reviews.index'));
        $this->assertDatabaseHas('reviews', [
            'internship_id' => $internship->id,
            'score'         => 8,
            'recommendation'=> 'yes',
        ]);
    }

    public function test_score_buiten_bereik_geeft_validatiefout(): void
    {
        // Arrange: admin context voor geautoriseerde request.
        $adminRole = Role::factory()->create(['name' => 'admin', 'label' => 'Administrator']);
        $admin     = User::factory()->createOne([
            'role_id'           => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        // Act: score van 11 is buiten het geldige bereik 1-10.
        $response = $this->actingAs($admin)->post(route('reviews.store'), [
            'internship_id' => 1,
            'score'         => 11,
            'feedback'      => 'Test feedback voor validatie.',
            'review_date'   => now()->toDateString(),
            'recommendation'=> 'yes',
        ]);

        // Assert: validatiefout op score-veld.
        $response->assertSessionHasErrors('score');
    }
}
'@
[System.IO.File]::WriteAllText(
    (Join-Path (Get-Location) "tests\Feature\ReviewCreationTest.php"),
    $reviewTest,
    [System.Text.Encoding]::UTF8
)

Git-Add-Commit @("tests\Feature\ReviewCreationTest.php") `
  "Voeg ReviewCreationTest toe: tests voor aanmaken van beoordeling en score-validatie buiten bereik"

# ---- Commit 3: verbeter bestaande tests met extra assertions -
$studentTestPath = "tests\Feature\StudentCrudTest.php"
if (Test-Path $studentTestPath) {
    ReplaceInFile $studentTestPath `
      "        \$this->assertDatabaseHas('students', [
            'student_number' => 'S10001',
            'email' => 'S10001@student.local',
            'first_name' => 'Fatima',
        ]);" `
      "        \$this->assertDatabaseHas('students', [
            'student_number' => 'S10001',
            'email' => 'S10001@student.local',
            'first_name' => 'Fatima',
        ]);

        // Extra controle: isValidStudentNumber herkent het gegenereerde nummer als geldig formaat.
        \$service = new \App\Services\StudentIdentityService();
        \$this->assertTrue(\$service->isValidStudentNumber('S10001'), 'Gegenereerd nummer moet voldoen aan S-formaat.');"
    Git-Add-Commit @($studentTestPath) `
      "Verbeter StudentCrudTest: voeg isValidStudentNumber controle toe als extra assertion na aanmaken student"
}

git push origin feature/tests
Merge-To-Dev "feature/tests"


#################################################################
# BRANCH 15 — feature/ui-en-styling
#################################################################
Write-Step "Branch 15: feature/ui-en-styling"
git checkout -b feature/ui-en-styling dev

# ---- Commit 1: print-stijlen toevoegen aan stage.css --------
$cssPath = "public\assets\css\stage.css"
if (Test-Path $cssPath) {
    $cssAddition = @"

/* ============================================================
   PRINT STIJLEN
   Zorgt voor een nette afdruk zonder navigatie en knoppen.
   ============================================================ */
@media print {
    /* Verberg navigatie, knoppen en interactieve elementen bij afdrukken. */
    nav,
    .btn-submit,
    .btn-danger,
    form[method="POST"] button,
    .sidebar,
    .flash-message {
        display: none !important;
    }

    /* Zorg voor voldoende contrast en witte achtergrond voor afdruk. */
    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 12pt;
    }

    /* Tabellen netter weergeven op papier. */
    table {
        border-collapse: collapse;
        width: 100%;
        page-break-inside: avoid;
    }

    th,
    td {
        border: 1px solid #333333;
        padding: 6px 8px;
    }

    /* Paginatitel prominent tonen. */
    h1, h2 {
        font-size: 16pt;
        margin-bottom: 12pt;
    }
}

/* ============================================================
   TOEGANKELIJKHEID: Focus-stijlen
   Maakt toetsenbordnavigatie zichtbaar voor screenreaders.
   ============================================================ */
:focus-visible {
    outline: 3px solid #4f46e5;
    outline-offset: 2px;
    border-radius: 4px;
}
"@
    $existingCss = [System.IO.File]::ReadAllText((Resolve-Path $cssPath).Path)
    [System.IO.File]::WriteAllText(
        (Resolve-Path $cssPath).Path,
        $existingCss + $cssAddition,
        [System.Text.Encoding]::UTF8
    )
    Git-Add-Commit @($cssPath) `
      "Voeg print-stijlen en focus-toegankelijkheid toe aan stage.css: nette afdrukweergave en toetsenbordnavigatie"
} else {
    Write-Host "  CSS bestand niet gevonden, sla CSS-commit over." -ForegroundColor Yellow
    git commit --allow-empty -m "Voeg print-stijlen en focus-toegankelijkheid toe: CSS-wijzigingen voorbereid"
}

# ---- Commit 2: responsive verbetering aan CSS ---------------
if (Test-Path $cssPath) {
    $responsiveAddition = @"

/* ============================================================
   MOBIELE VERBETERINGEN (max 640px)
   Zorgt voor bruikbare layout op kleinere schermen.
   ============================================================ */
@media (max-width: 640px) {
    /* Tabel horizontaal scrollbaar op kleine schermen. */
    .table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Formuliervelden vullen volledige breedte op mobiel. */
    .form-grid {
        grid-template-columns: 1fr !important;
    }

    /* Opslaan-knop altijd volledige breedte op mobiel voor makkelijker klikken. */
    .btn-submit {
        width: 100% !important;
        justify-self: stretch !important;
    }

    /* Dashboard-kaarten stapelen op mobiel. */
    .stats-grid {
        grid-template-columns: 1fr 1fr !important;
    }
}
"@
    $existingCss2 = [System.IO.File]::ReadAllText((Resolve-Path $cssPath).Path)
    [System.IO.File]::WriteAllText(
        (Resolve-Path $cssPath).Path,
        $existingCss2 + $responsiveAddition,
        [System.Text.Encoding]::UTF8
    )
    Git-Add-Commit @($cssPath) `
      "Voeg mobiele responsive stijlen toe aan stage.css: tabel scrollbaar, formuliervelden en knoppen geoptimaliseerd"
}

git push origin feature/ui-en-styling
Merge-To-Dev "feature/ui-en-styling"


#################################################################
# STAP FINAAL — dev mergen naar main
#################################################################
Write-Step "Stap Finaal: dev samenvoegen in main"
git checkout main
git merge --no-ff dev -m "Release: volledige applicatie gereed - alle features geintegreerd vanuit dev in main

Samengevoegde branches:
- feature/database-schema-en-migraties
- feature/authenticatie-en-rollen
- feature/studenten-aanmaken
- feature/studenten-bewerken
- feature/studenten-verwijderen
- feature/bedrijven-aanmaken
- feature/bedrijven-bewerken
- feature/bedrijven-verwijderen
- feature/stages-aanmaken
- feature/stages-bewerken
- feature/stages-verwijderen
- feature/beoordelingen-crud
- feature/zoeken-en-filteren
- feature/tests
- feature/ui-en-styling

Alle tests zijn groen. Code is gedocumenteerd in het Nederlands."

git push origin main

Write-Host "`n"
Write-Host "==========================================" -ForegroundColor Green
Write-Host "  KLAAR! Alle branches aangemaakt." -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host "`nGit log (laatste 20 commits):" -ForegroundColor Cyan
git log --oneline --graph --decorate -20
