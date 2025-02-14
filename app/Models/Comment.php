<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Comment extends Model
{
    protected $fillable = [
        'comment', 'is_admin', 'ticket_id', 'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * List all comments of a given ticket.
     */
    public static function listCommentsByTicket($ticketId)
    {
        try {
            return self::where('ticket_id', $ticketId)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to list comments: " . $e->getMessage());
        }
    }

    /**
     * List all comments.
     */
    public static function listAllComments()
    {
        try {
            return self::with(['user', 'ticket'])->orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            throw new Exception("Failed to list all comments: " . $e->getMessage());
        }
    }

    /**
     * Create a new comment.
     */
    public static function createComment(array $data)
    {
        try {
            return self::create($data);
        } catch (Exception $e) {
            throw new Exception("Failed to create comment: " . $e->getMessage());
        }
    }

    /**
     * Update a comment.
     */
    public static function updateComment($id, array $data)
    {
        try {
            $comment = self::findOrFail($id);
            $comment->update($data);
            return $comment;
        } catch (Exception $e) {
            throw new Exception("Failed to update comment: " . $e->getMessage());
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);  // A comment belongs to one user
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);  // A comment belongs to one ticket
    }
}
