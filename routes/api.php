<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\NewsletterSubscriberController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return 'API';
});

// USER LOGIN - REGISTER - LOGOUT
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});

// NEWSLETTER SUBSCRIBERS
Route::prefix('newsletter')->group(function () {
    Route::get('/subscribers', [NewsletterSubscriberController::class, 'index']);
    Route::post('/subscribe', [NewsletterSubscriberController::class, 'subscribe']);
    Route::put('/unsubscribe', [NewsletterSubscriberController::class, 'unsubscribe']);
});


// CONTACT-US FORMS
Route::prefix('contact')->group(function () {
    Route::post('/contact-us', [ContactUsController::class, 'store']);
    Route::get('/contact-us', [ContactUsController::class, 'index']);
});


// EVENTS
Route::post('/filtered-events', [EventController::class, 'getFilteredEvents']);
Route::get('/recent-events', [EventController::class, 'getRecentEvents']);



