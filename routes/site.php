<?php

use App\Http\Controllers\Site\AvailabilityController;
use App\Http\Controllers\Site\BookingController;
use App\Http\Controllers\Site\BookingDetailController;
use App\Http\Controllers\Site\CheckoutController;
use App\Http\Controllers\Site\DestinationController;
use App\Http\Controllers\Site\DiscoverController;
use App\Http\Controllers\Site\ExperienceController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\HostsController;
use App\Http\Controllers\Site\TravellerReviewsController;
use App\Http\Controllers\Site\InterestController;
use App\Http\Controllers\Site\NewsletterController;
use App\Http\Controllers\Site\ReviewController;
use App\Http\Controllers\Site\TouristBookingController;
use App\Http\Controllers\Site\WishlistController;
use App\Http\Controllers\Site\WishlistPageController;
use Illuminate\Support\Facades\Route;

$localePattern = implode('|', array_keys(config('locales.supported', ['en' => []])));

Route::get('/', [HomeController::class, 'index'])->middleware('set.locale')->name('site.root');

Route::prefix('{locale}')
    ->where(['locale' => $localePattern])
    ->middleware('set.locale')
    ->group(function (): void {
        Route::get('/', [HomeController::class, 'index'])->name('site.home');
        Route::get('/morocco-compass', [HomeController::class, 'compass'])->name('site.home.compass');
        Route::get('/home-v2', fn (string $locale) => redirect()->route('site.home', ['locale' => $locale], 301));
        Route::get('/discover', [DiscoverController::class, 'index'])->name('site.discover.index');
        Route::get('/discover/destinations/{destination:slug}', [DestinationController::class, 'show'])->name('site.discover.destinations.show');
        Route::get('/discover/interests/{interest:slug}', [InterestController::class, 'show'])->name('site.discover.interests.show');
        Route::get('/hosts', [HostsController::class, 'index'])->name('site.hosts.index');
        Route::get('/hosts/{host:slug}', [HostsController::class, 'show'])->name('site.hosts.show');
        Route::get('/reviews', [TravellerReviewsController::class, 'index'])->name('site.reviews.index');

        // Living style guide for the v2 UI kit — never registered in production.
        if (app()->environment(['local', 'testing'])) {
            Route::view('/_ui-kit', 'site.dev.ui-kit')->name('site.dev.ui-kit');
        }
        Route::get('/experiences', [ExperienceController::class, 'index'])->name('site.experiences.index');
        Route::post('/newsletter', [NewsletterController::class, 'store'])->name('site.newsletter.store');
        Route::post('/experiences/{experience:slug}/quote', [AvailabilityController::class, 'quote'])->name('site.experiences.quote');
        Route::post('/checkout/start', [CheckoutController::class, 'start'])->name('site.checkout.start');
        Route::get('/checkout/{checkoutSession:uuid}/activity', [CheckoutController::class, 'activity'])->name('site.checkout.activity');
        Route::post('/checkout/{checkoutSession:uuid}/activity', [CheckoutController::class, 'storeActivity'])->name('site.checkout.activity.store');
        Route::get('/checkout/{checkoutSession:uuid}/contact', [CheckoutController::class, 'contact'])->name('site.checkout.contact');
        Route::post('/checkout/{checkoutSession:uuid}/login', [CheckoutController::class, 'login'])->name('site.checkout.login');
        Route::post('/checkout/{checkoutSession:uuid}/register', [CheckoutController::class, 'register'])->name('site.checkout.register');
        Route::post('/checkout/{checkoutSession:uuid}/contact', [CheckoutController::class, 'storeContact'])->name('site.checkout.contact.store');
        Route::get('/experiences/{experience}/book', [BookingController::class, 'create'])->name('site.bookings.create');
        Route::post('/experiences/{experience}/book', [BookingController::class, 'store'])->name('site.bookings.store');
        Route::get('/experiences/{experience:slug}', [ExperienceController::class, 'show'])->name('site.experiences.show');
        Route::get('/preview/{experience:slug}', [ExperienceController::class, 'previewShow'])->name('site.experiences.preview');
        Route::get('/bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('site.bookings.confirmation');

        Route::middleware('auth')->group(function (): void {
            Route::get('/dashboard/bookings', [TouristBookingController::class, 'index'])->name('site.dashboard.bookings');
            Route::post('/dashboard/bookings/{booking}/cancel', [TouristBookingController::class, 'cancel'])->name('site.dashboard.bookings.cancel');
            Route::get('/dashboard/bookings/{booking}', [BookingDetailController::class, 'show'])->name('site.dashboard.bookings.show');
            Route::get('/dashboard/reviews', [ReviewController::class, 'index'])->name('site.dashboard.reviews');
            Route::get('/dashboard/wishlist', [WishlistPageController::class, 'index'])->name('site.dashboard.wishlist');
            Route::get('/bookings/{booking}/review', [ReviewController::class, 'create'])->name('site.reviews.create');
            Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('site.reviews.store');
            Route::get('/bookings/{booking}/review/thanks', [ReviewController::class, 'thanks'])->name('site.reviews.thanks');
            Route::post('/wishlist/{experience}', [WishlistController::class, 'toggle'])->name('site.wishlist.toggle');
        });
    });
