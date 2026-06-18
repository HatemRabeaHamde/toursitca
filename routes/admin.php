<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\ExperienceOptionController;
use App\Http\Controllers\Admin\LandingContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'user.not_banned', 'role:admin'])
    ->group(function (): void {
        Route::redirect('/', '/admin/dashboard')->name('home');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/agencies', [AgencyController::class, 'index'])->name('agencies.index');
        Route::get('/agencies/create', [AgencyController::class, 'create'])->name('agencies.create');
        Route::post('/agencies', [AgencyController::class, 'store'])->name('agencies.store');
        Route::post('/agencies/{agency}/approve', [AgencyController::class, 'approve'])->name('agencies.approve');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/payment-status', [BookingController::class, 'updatePaymentStatus'])->name('bookings.payment-status.update');
        Route::get('/experiences', [ExperienceController::class, 'index'])->name('experiences.index');
        Route::get('/experiences/create', [ExperienceController::class, 'create'])->name('experiences.create');
        Route::post('/experiences', [ExperienceController::class, 'store'])->name('experiences.store');
        Route::get('/experiences/{experience}/edit', [ExperienceController::class, 'edit'])->name('experiences.edit');
        Route::patch('/experiences/{experience}', [ExperienceController::class, 'update'])->name('experiences.update');
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
        Route::get('/landing/categories', [LandingContentController::class, 'categories'])->name('landing.categories.index');
        Route::post('/landing/categories', [LandingContentController::class, 'storeCategory'])->name('landing.categories.store');
        Route::patch('/landing/categories/{category}', [LandingContentController::class, 'updateCategory'])->name('landing.categories.update');
        Route::delete('/landing/categories/{category}', [LandingContentController::class, 'destroyCategory'])->name('landing.categories.destroy');
        Route::get('/landing/destinations', [LandingContentController::class, 'destinations'])->name('landing.destinations.index');
        Route::post('/landing/destinations', [LandingContentController::class, 'storeDestination'])->name('landing.destinations.store');
        Route::patch('/landing/destinations/{destination}', [LandingContentController::class, 'updateDestination'])->name('landing.destinations.update');
        Route::delete('/landing/destinations/{destination}', [LandingContentController::class, 'destroyDestination'])->name('landing.destinations.destroy');
        Route::get('/landing/landmarks', [LandingContentController::class, 'landmarks'])->name('landing.landmarks.index');
        Route::post('/landing/landmarks', [LandingContentController::class, 'storeLandmark'])->name('landing.landmarks.store');
        Route::patch('/landing/landmarks/{landmark}', [LandingContentController::class, 'updateLandmark'])->name('landing.landmarks.update');
        Route::delete('/landing/landmarks/{landmark}', [LandingContentController::class, 'destroyLandmark'])->name('landing.landmarks.destroy');
        Route::get('/landing/faqs', [LandingContentController::class, 'faqs'])->name('landing.faqs.index');
        Route::post('/landing/faqs', [LandingContentController::class, 'storeFaq'])->name('landing.faqs.store');
        Route::patch('/landing/faqs/{faq}', [LandingContentController::class, 'updateFaq'])->name('landing.faqs.update');
        Route::delete('/landing/faqs/{faq}', [LandingContentController::class, 'destroyFaq'])->name('landing.faqs.destroy');
        Route::get('/landing/reels', [LandingContentController::class, 'reels'])->name('landing.reels.index');
        Route::post('/landing/reels', [LandingContentController::class, 'storeReel'])->name('landing.reels.store');
        Route::patch('/landing/reels/{reel}', [LandingContentController::class, 'updateReel'])->name('landing.reels.update');
        Route::delete('/landing/reels/{reel}', [LandingContentController::class, 'destroyReel'])->name('landing.reels.destroy');
        Route::get('/landing/testimonials', [LandingContentController::class, 'testimonials'])->name('landing.testimonials.index');
        Route::post('/landing/testimonials', [LandingContentController::class, 'storeTestimonial'])->name('landing.testimonials.store');
        Route::patch('/landing/testimonials/{testimonial}', [LandingContentController::class, 'updateTestimonial'])->name('landing.testimonials.update');
        Route::delete('/landing/testimonials/{testimonial}', [LandingContentController::class, 'destroyTestimonial'])->name('landing.testimonials.destroy');
    });
