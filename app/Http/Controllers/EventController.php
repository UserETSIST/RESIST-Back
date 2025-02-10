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


    public function getFilteredEvents(Request $request)
    {
        {
            try {
                // Validate the request
                $validated = $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'jammer' => 'nullable|boolean',
                    'spoofer' => 'nullable|boolean',
                ]);

                // Fetch the events using the repository
                $events = $this->eventRepo->getEventsByConditions(
                    $validated['start_date'],
                    $validated['end_date'],
                    $request->input('jammer'),
                    $request->input('spoofer')
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









}
