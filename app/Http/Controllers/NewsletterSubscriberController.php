<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class NewsletterSubscriberController extends Controller
{
    // List all subscribers
    public function index()
    {
        $subscribers = NewsletterSubscriber::where('subscription_status', 'subscribed')->get(['*']);
        return response()->json($subscribers);
    }

    // Store a new subscriber
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|unique:newsletter_subscribers',
        ]);

        $subscriber = NewsletterSubscriber::create([
            'email' => $validated['email'],
            'subscribed_at' => now(),
        ]);

        return response()->json(['message' => 'Subscription successful!', 'subscriber' => $subscriber], 201);
    } catch (\Illuminate\Database\QueryException $e) {
        // Handle database-related errors, like unique constraint violations
        if ($e->getCode() === '23000') {  // SQLSTATE 23000 is for integrity constraint violations
            return response()->json(['message' => 'The email is already subscribed.'], 409);
        }
        return response()->json(['message' => 'An unexpected database error occurred.'], 500);
    } catch (\Exception $e) {
        // Catch any other unexpected exceptions
        return response()->json(['message' => 'An unexpected error occurred.'], 500);
    }
}


    // Show a specific subscriber by ID
    public function show($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json(['message' => 'Subscriber not found'], 404);
        }

        return response()->json($subscriber);
    }

    // Unsubscribe a user
    public function unsubscribe($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json(['message' => 'Subscriber not found'], 404);
        }

        $subscriber->update([
            'subscription_status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return response()->json(['message' => 'Unsubscribed successfully']);
    }
}

