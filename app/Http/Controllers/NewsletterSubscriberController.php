<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller;

class NewsletterSubscriberController extends Controller
{
    /**
     * Subscribe a new user to the newsletter.
     */
    public function subscribe(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email|',
            ]);

            // Add the subscriber using the model method
            NewsletterSubscriber::addSubscriber($validated);

            return response()->json([
                'success' => true,
                'message' => 'You have been successfully subscribed to the newsletter.',
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while subscribing',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Unsubscribe a user from the newsletter by email.
     */
    public function unsubscribe(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email',
            ]);

            // Unsubscribe the user using the model method
            $subscriber = NewsletterSubscriber::unsubscribeByEmail($validated['email']);

            return response()->json([
                'success' => true,
                'message' => 'You have been successfully unsubscribed from the newsletter.',
                'data' => $subscriber,
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unsubscribing',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
 * List all newsletter subscribers.
 */
public function index(Request $request)
{
    try {
        $perPage = $request->query('per_page', 10);  // Default to 10 subscribers per page
        $subscribers = NewsletterSubscriber::getAllSubscribers($perPage);

        return response()->json([
            'success' => true,
            'data' => $subscribers,
            'message' => 'Subscribers retrieved successfully',
        ], Response::HTTP_OK);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving subscribers',
            'error' => $e->getMessage(),  // Optional: hide in production
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
