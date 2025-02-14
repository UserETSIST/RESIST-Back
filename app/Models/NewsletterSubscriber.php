<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';
    protected $fillable = ['email', 'subscription_status', 'subscribed_at', 'unsubscribed_at'];
    public $timestamps = true;  // created_at and updated_at will be managed automatically

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];


    /**
     * Get all subscribers with pagination.
     */
    public static function getAllSubscribers($perPage = 10)
    {
        try {
            return self::where('subscription_status','subscribed')->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve subscribers: " . $e->getMessage());
        }
    }


    /**
     * Add a new subscriber.
     */
    public static function addSubscriber(array $data)
    {
        try {
            // Check if the email already exists
            $subscriber = self::where('email', $data['email'])->first();

            if ($subscriber) {
                // Update existing subscriber
                $subscriber->update([
                    'subscription_status' => 'subscribed',
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,  // Reset unsubscribed_at if it was previously set
                ]);
                return $subscriber;
            }

            // Create a new subscriber
            return self::create([
                'email' => $data['email'],
                'subscription_status' => 'subscribed',
                'subscribed_at' => now(),
            ]);

        } catch (Exception $e) {
            throw new Exception("Failed to add or update subscriber: " . $e->getMessage());
        }
    }


    /**
     * Unsubscribe a subscriber by email.
     */
    public static function unsubscribeByEmail($email)
    {
        try {
            $subscriber = self::where('email', $email)->first();

            if (!$subscriber) {
                throw new Exception("Subscriber not found.");
            }

            $subscriber->update([
                'subscription_status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            return $subscriber;
        } catch (Exception $e) {
            throw new Exception("Failed to unsubscribe: " . $e->getMessage());
        }
    }
}
