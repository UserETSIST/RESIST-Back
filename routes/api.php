<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\NewsletterSubscriberController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return 'API';
});

// USER AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users', [AuthController::class, 'listAllUsers']);
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
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/filtered-events', [EventController::class, 'getFilteredEvents']);
    Route::get('/recent-events', [EventController::class, 'getRecentEvents']);
});


//TICKETS
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/my-tickets', [TicketController::class, 'myTickets']);
});
