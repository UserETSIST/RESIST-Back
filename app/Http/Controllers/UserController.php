<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;


class UserController
{

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'is_admin' => 'required',
                'is_active' => 'required',
                'password' => 'required|string|min:8',
            ]);

            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only admins can access this resource.',
                ], 403);  // Forbidden
            }

            // Call the User model's register function
            $user = User::register($validated);

            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'User created succesfully',
            ], 201);
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
