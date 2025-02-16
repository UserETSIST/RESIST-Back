<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Illuminate\Database\QueryException;
use Exception;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Call the User model's register function
            $user = User::register($validated);

            return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered',
                'error' => $e->getMessage(), // Optional: Hide this in production
            ], 409);  // Conflict
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while registering the user',
                'error' => $e->getMessage(), // Optional: Hide this in production
            ], 500);
        }
    }

    /**
     * Login a user and generate a token.
     */
    public function login(Request $request)
{
    try {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Call the login method in the User model
        $result = User::login($validated['email'], $validated['password']);

        if (!$result) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'is_admin' => $result['is_admin'],  // Include admin status in the response
        ], 200);
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'errors' => $e->errors(),
        ], 401);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred during login',
            'error' => $e->getMessage(),  // Optional: hide this in production
        ], 500);
    }
}


    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while logging out',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request)
    {
        try {
            return response()->json($request->user());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving user profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listAllUsers(Request $request)
    {
        try {
            // Ensure the user is authenticated and an admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only admins can access this resource.',
                ], 403);  // Forbidden
            }

            // Call the User model's listAllUsers method
            $users = User::listAllUsers();

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),  // Optional: hide this in production
            ], 500);
        }
    }


}
