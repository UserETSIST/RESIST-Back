<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Exception;

class Comment extends Model
{
    protected $fillable = [
        'comment', 'ticket_id', 'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * List all comments of a given ticket with user details.
     *
     * @param int $ticketId
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public static function listCommentsByTicket($ticketId)
    {
        try {
            return self::where('ticket_id', $ticketId)
                // ->with('user')  // Include the user relationship
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to list comments: " . $e->getMessage());
        }
    }

    /**
     * List all comments with associated users and tickets.
     *
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    public static function listAllComments()
    {
        try {
            return self::with(['user', 'ticket'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to list all comments: " . $e->getMessage());
        }
    }

    /**
     * Create a new comment with ticket validation.
     *
     * @param array $data
     * @return Comment
     * @throws Exception
     */
    public static function createComment(array $data)
    {
        try {
            // Ensure the ticket exists before creating a comment
            if (!Ticket::find($data['ticket_id'])) {
                throw new Exception("Invalid ticket_id: The associated ticket does not exist.");
            }

            return self::create($data);
        } catch (Exception $e) {
            throw new Exception("Failed to create comment: " . $e->getMessage());
        }
    }

    /**
     * Update an existing comment.
     *
     * @param int $id
     * @param array $data
     * @return Comment
     * @throws Exception
     */
    public static function updateComment($id, array $data)
    {
        try {
            $comment = self::findOrFail($id);  // Throws ModelNotFoundException if not found
            $comment->update($data);
            return $comment;
        } catch (Exception $e) {
            throw new Exception("Failed to update comment: " . $e->getMessage());
        }
    }

    /**
     * Relationship: A comment belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: A comment belongs to one ticket.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
