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
        'name', 'email', 'password', 'is_admin', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
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

            if ($user && Hash::check($password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return [
                    'token' => $token,
                    'is_admin' => $user->is_admin  // Return if the user is an admin
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
}
