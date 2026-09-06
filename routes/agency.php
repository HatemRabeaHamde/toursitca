<?php

use App\Http\Controllers\Agency\AvailabilityController;
use App\Http\Controllers\Agency\BookingController;
use App\Http\Controllers\Agency\DashboardController;
use App\Http\Controllers\Agency\ExperienceController;
use App\Http\Controllers\Agency\ExperienceOptionController;
use App\Http\Controllers\Agency\PayoutController;
use App\Http\Controllers\Agency\ProfileController;
use App\Http\Controllers\Agency\RegistrationController;
use App\Http\Controllers\Agency\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/agency/register', [RegistrationController::class, 'create'])->name('agency.register');
    Route::post('/agency/register', [RegistrationController::class, 'store'])->name('agency.register.store');
});

Route::prefix('agency')
    ->as('agency.')
    ->middleware(['auth', 'user.not_banned'])
    ->group(function (): void {
        Route::view('/pending', 'agency.pending')->name('pending');

        Route::middleware('agency.approved')->group(function (): void {
            Route::redirect('/', '/agency/dashboard')->name('home');
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
            Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
            Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::get('/experiences', [ExperienceController::class, 'index'])->name('experiences.index');
            Route::get('/experiences/create', [ExperienceController::class, 'create'])->name('experiences.create');
            Route::post('/experiences', [ExperienceController::class, 'store'])->name('experiences.store');
            Route::get('/experiences/{experience}/edit', [ExperienceController::class, 'edit'])->name('experiences.edit');
            Route::patch('/experiences/{experience}', [ExperienceController::class, 'update'])->name('experiences.update');
            Route::delete('/experiences/{experience}', [ExperienceController::class, 'destroy'])->name('experiences.destroy');
            Route::get('/experiences/{experience}/options', [ExperienceOptionController::class, 'index'])->name('experiences.options.index');
            Route::post('/experiences/{experience}/options', [ExperienceOptionController::class, 'store'])->name('experiences.options.store');
            Route::patch('/experiences/{experience}/options/{option}', [ExperienceOptionController::class, 'update'])->name('experiences.options.update');
            Route::delete('/experiences/{experience}/options/{option}', [ExperienceOptionController::class, 'destroy'])->name('experiences.options.destroy');
            Route::post('/experiences/{experience}/publish', [ExperienceController::class, 'publish'])->name('experiences.publish');
            Route::post('/experiences/{experience}/unpublish', [ExperienceController::class, 'unpublish'])->name('experiences.unpublish');
            Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
            Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
            Route::patch('/availability/{availability}', [AvailabilityController::class, 'update'])->name('availability.update');
            Route::delete('/availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');
        });
    });
