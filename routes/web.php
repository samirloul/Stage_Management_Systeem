<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Basis pagina's die elke ingelogde/verifieerde gebruiker mag zien.
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('internships', [InternshipController::class, 'index'])->name('internships.index');
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');

    Route::middleware('role:admin')->group(function () {
        // Alleen admin mag kern-CRUD beheren voor studenten/bedrijven/stages.
        Route::resource('students', StudentController::class)->except(['show']);
        Route::resource('companies', CompanyController::class)->except(['show']);

        Route::get('internships/create', [InternshipController::class, 'create'])->name('internships.create');
        Route::post('internships', [InternshipController::class, 'store'])->name('internships.store');
        Route::get('internships/{internship}/edit', [InternshipController::class, 'edit'])->name('internships.edit');
        Route::put('internships/{internship}', [InternshipController::class, 'update'])->name('internships.update');
        Route::delete('internships/{internship}', [InternshipController::class, 'destroy'])->name('internships.destroy');

        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    Route::middleware('role:admin,company')->group(function () {
        // Bedrijven en admin mogen beoordelingen aanmaken en bijwerken.
        Route::get('reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
        Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    });
});

require __DIR__.'/settings.php';
