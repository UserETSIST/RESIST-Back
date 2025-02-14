<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Ticket extends Model
{
    protected $fillable = [
        'subject', 'description', 'status', 'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * List all tickets.
     */
    public static function listAllTickets()
    {
        try {
            return self::with('user')->orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            throw new Exception("Failed to list tickets: " . $e->getMessage());
        }
    }

    /**
     * Create a new ticket.
     */
    public static function createTicket(array $data)
    {
        try {
            return self::create($data);
        } catch (Exception $e) {
            throw new Exception("Failed to create ticket: " . $e->getMessage());
        }
    }

    /**
     * Update a ticket.
     */
    public static function updateTicket($id, array $data)
    {
        try {
            $ticket = self::findOrFail($id);
            $ticket->update($data);
            return $ticket;
        } catch (Exception $e) {
            throw new Exception("Failed to update ticket: " . $e->getMessage());
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);  // A ticket belongs to one user
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);  // A ticket can have many comments
    }

    public static function getUserTickets($userId)
    {
        try {
            return self::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve user tickets: " . $e->getMessage());
        }
    }
}
