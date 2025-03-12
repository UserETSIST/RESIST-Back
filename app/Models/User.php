<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'is_admin',
        'is_active',
        'email_is_verified'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'email_is_verified' => 'boolean',
    ];

    /**
     * List all users.
     */
    public static function listAllUsers()
    {
        try {
            return self::orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            throw new Exception("Failed to list users: " . $e->getMessage());
        }
    }

    /**
     * Register a new user.
     */
    public static function register(array $data)
    {
        try {
            $data['password'] = bcrypt($data['password']);
            return self::create($data);
        } catch (Exception $e) {
            throw new Exception("Failed to register user: " . $e->getMessage());
        }
    }

    /**
     * Login user.
     */
    public static function login($email, $password)
    {
        try {
            $user = self::where('email', $email)->first();

            if ($user->is_active && Hash::check($password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return [
                    'token' => $token,
                    'is_admin' => $user->is_admin,
                    'is_active' => $user->is_active
                ];
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Failed to login: " . $e->getMessage());
        }
    }

    /**
     * Logout user.
     */
    public static function logout($user)
    {
        try {
            $user->tokens()->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to logout: " . $e->getMessage());
        }
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);  // A user can have many tickets
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);  // A user can have many comments
    }

    /**
     * Soft delete a user by setting is_active to false.
     */
    public static function softDeleteUser($id)
    {
        try {
            // Find the user by ID
            $user = self::findOrFail($id);

            // Set the user as inactive (soft delete)
            $user->update([
                'is_active' => false
            ]);

            return [
                'success' => true,
                'message' => 'User deactivated successfully.'
            ];
        } catch (Exception $e) {
            throw new Exception("Failed to delete user: " . $e->getMessage());
        }
    }


    /**
     * Update user data
     */
    public static function updateUser($id, array $data)
    {
        try {
            // Find the user
            $user = self::findOrFail($id);

            // If password is provided, hash it
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Update user fields (only provided values)
            $user->fill($data);
            $user->save(); // Force an update even if nothing changed

            return $user;
        } catch (Exception $e) {
            throw new Exception("Failed to update user: " . $e->getMessage());
        }
    }
}
