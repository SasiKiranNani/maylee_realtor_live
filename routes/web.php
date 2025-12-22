<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\SubscriptionController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('our-services', function() {
    return view('frontend.our-service');
})->name('our-service');
// Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about-us');
Route::get('/about-us', function () {
    return view('frontend.about-us');
})->name('frontend.about-us');
// Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('/privacy-policy', function () {
    return view('frontend.privacy-policy');
})->name('privacyPolicy');
// Route::get('/terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('termsAndConditions');
Route::get('/terms-and-conditions', function () {
    return view('frontend.terms-and-conditions');
})->name('termsAndConditions');

Route::get('/neighbourhood', [HomeController::class, 'neighbourhood'])->name('neighbourhood');
Route::get('/neighbourhood-details/{city}', [HomeController::class, 'neighbourhoodDetails'])->name('neighbourhood-details');

// routes/web.php
Route::get('/properties/map-data', [PropertyController::class, 'mapData'])
    ->name('properties.map-data');
Route::get('/lease', [PropertyController::class, 'properties'])->name('lease');
Route::get('/buy', [PropertyController::class, 'properties'])->name('buy');
Route::get('/property/{listingKey}', [PropertyController::class, 'propertyDetails'])->name('buy.details');
Route::get('/lease/{listingKey}', [PropertyController::class, 'propertyDetails'])->name('lease.details');
Route::get('/sold/{listingKey}', [PropertyController::class, 'propertyDetails'])->name('sold.details');


Route::get('/get-available-time-slots', [PropertyController::class, 'getAvailableTimeSlots'])->name('get-available-time-slots');
Route::post('/tour-bookings', [PropertyController::class, 'storeTourBooking'])->name('tour-bookings.store');

Route::get('/sell/{search?}', [PropertyController::class, 'sell'])->name('sell');
Route::post('/sell', [PropertyController::class, 'sellStore'])->name('sell.store');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/subscribe', [App\Http\Controllers\SubscriptionController::class, 'store'])->name('subscribe');

// Login
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

// Register
Route::post('/register', [AuthController::class, 'register'])->name('user.register');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');

// Forgot Password
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('user.password.email');

// Reset Password (link will redirect here)
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('user.password.update');

// Use standard auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
     Route::get('/wishlist', [PropertyController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle', [PropertyController::class, 'toggleWishlist'])->name('wishlist.toggle');
    
     Route::get('/user-profile', [UserProfileController::class, 'index'])->name('user.profile');
    Route::post('/user-profile/update', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/user-profile/avatar', [UserProfileController::class, 'updateAvatar'])->name('user.profile.avatar');
    Route::post('/user-profile/cover', [UserProfileController::class, 'updateCover'])->name('user.profile.cover');
    Route::post('/user-profile/password', [UserProfileController::class, 'updatePassword'])->name('user.profile.password');
});
