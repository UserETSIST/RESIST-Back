<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    /**
     * List all comments for a given ticket.
     */
    public function listByTicket($ticketId)
    {
        $comments = Comment::listCommentsByTicket($ticketId);
        return response()->json($comments);
    }

    /**
     * List all comments.
     */
    public function index()
    {
        $comments = Comment::listAllComments();
        return response()->json($comments);
    }

    /**
     * Create a new comment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_admin' => 'required|boolean',
            'ticket_id' => 'required|exists:tickets,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $comment = Comment::createComment($validated);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'sometimes|string',
        ]);

        $comment = Comment::updateComment($id, $validated);

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment]);
    }
}
