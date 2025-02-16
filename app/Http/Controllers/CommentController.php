<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    /**
     * List all comments for a given ticket.
     */
    public function listCommentsByTicket($ticketId)
    {
        try {
            // 1. Check if ticket_id is numeric
            if (!is_numeric($ticketId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ticket ID format. It must be a numeric value.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // 2. Check if ticket_id is positive
            if ($ticketId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ticket ID. It must be a positive integer.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // 3. Check if the ticket exists in the database
            if (!\App\Models\Ticket::find($ticketId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The specified ticket does not exist.',
                ], Response::HTTP_NOT_FOUND);
            }

            // 4. Retrieve comments for the given ticket
            $comments = Comment::listCommentsByTicket($ticketId);

            return response()->json([
                'success' => true,
                'data' => $comments,
                'message' => 'Comments retrieved successfully.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve comments.',
                'error' => $e->getMessage(),  // Optional: hide this in production
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Create a new comment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        try {
            $validated['user_id'] = $request->user()->id;  // Get the user ID from the authenticated user

            $comment = Comment::createComment($validated);

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Comment created successfully.',
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment.',
                'error' => $e->getMessage(),  // Hide this in production
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing comment.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        try {
            $comment = Comment::updateComment($id, $validated);

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Comment updated successfully.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment.',
                'error' => $e->getMessage(),  // Hide this in production
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
