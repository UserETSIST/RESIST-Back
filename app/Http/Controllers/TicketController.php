<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class TicketController extends Controller
{
    /**
     * List all tickets.
     */
    public function index(Request $request)
    {
        try {
            // Ensure the authenticated user is an admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only admins can list all tickets.',
                ], 403);  // Forbidden
            }

            $tickets = Ticket::listAllTickets();

            return response()->json([
                'success' => true,
                'data' => $tickets,
                'message' => 'Tickets retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tickets',
                'error' => $e->getMessage(),  // Optional: hide this in production
            ], 500);
        }
    }


    /**
     * Create a new ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $validated['user_id'] = $request->user()->id;
        $ticket = Ticket::createTicket($validated);

        return response()->json(['message' => 'Ticket created successfully', 'ticket' => $ticket], 201);
    }

    /**
     * Update a ticket.
     */
    public function update(Request $request, $id)
    {
        try {
            // Ensure the authenticated user is an admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only admins can update tickets.',
                ], 403);  // Forbidden
            }

            $validated = $request->validate([
                'subject' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'status' => 'sometimes|string|max:20',
            ]);

            $ticket = Ticket::updateTicket($id, $validated);

            return response()->json(['message' => 'Ticket updated successfully', 'ticket' => $ticket]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket',
                'error' => $e->getMessage(),  // Optional: hide this in production
            ], 500);
        }
    }

    public function myTickets(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            // Retrieve user-specific tickets from the model
            $tickets = Ticket::getUserTickets($user->id);

            return response()->json([
                'success' => true,
                'data' => $tickets,
                'message' => 'User-specific tickets retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user tickets',
                'error' => $e->getMessage(),  // Optional: hide this in production
            ], 500);
        }
    }
}
