<?php

namespace App\Http\Controllers;

use App\Repositories\EventRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class EventController
{
    protected $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }


    public function getAllEventsPaginated(Request $request)
    {
        try {
            // Get 'per_page' from query parameters or default to 10
            $perPage = $request->query('per_page', 10);  

            // Fetch paginated events from the repository
            $events = $this->eventRepo->getAllEventsPaginated((int)$perPage);

            // Return success response with paginated data
            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Events retrieved successfully with pagination',
            ], \Symfony\Component\HttpFoundation\Response::HTTP_OK);

        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving events',
                'error' => $e->getMessage(),
            ], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getFilteredEvents(Request $request)
    {
        {
            try {
                // Validate the request
                $validated = $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'jamming' => 'nullable|boolean',
                    'spoofing' => 'nullable|boolean',
                ]);

                // Fetch the events using the repository
                $events = $this->eventRepo->getEventsByConditions(
                    $validated['start_date'],
                    $validated['end_date'],
                    $request->input('jamming'),
                    $request->input('spoofing')
                );

                // Return the events in a successful response
                return response()->json([
                    'success' => true,
                    'data' => $events,
                    'message' => 'Events retrieved successfully',
                ], Response::HTTP_OK);
            } catch (ValidationException $e) {
                // Handle validation errors
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $e) {
                // Handle general errors
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while retrieving events',
                    'error' => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function getRecentEvents(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'days' => 'required|numeric',
                'jamming' => 'nullable|boolean',
                'spoofing' => 'nullable|boolean',
            ]);

            // Fetch events using the repository
            $events = $this->eventRepo->getRecentEvents(
                $validated['days'],
                $request->input('jamming'),
                $request->input('spoofing')
            );

            // Return success response
            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Events from the last specified days retrieved successfully',
            ], \Symfony\Component\HttpFoundation\Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            // Handle any other general errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving events',
                'error' => $e->getMessage(),  // Optional: Remove this in production for security reasons
            ], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }









}
