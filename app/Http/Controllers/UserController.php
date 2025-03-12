<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


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
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete a user by ID.
     */
    public function deactivateUser(Request $request, $id)
    {
        try {
            // ✅ 1. Ensure user is authenticated and an admin
            $admin = $request->user(); // Authenticated user

            if (!$admin || !$admin->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only admin users can perform this action.'
                ], Response::HTTP_FORBIDDEN);
            }

            // ✅ 2. Validate ID format and type
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // ✅ 3. Check if the user exists
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            // ✅ 4. Soft delete the user
            $result = User::softDeleteUser($id);

            return response()->json($result, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate user.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {

            Log::info('Incoming update request:', [
                'user_id' => $id,
                'request_data' => $request->all(),
                'authenticated_user' => $request->user()
            ]);


            // ✅ 1. Ensure the authenticated user is an admin
            $admin = $request->user();

            if (!$admin || !$admin->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only admin users can perform this action.'
                ], Response::HTTP_FORBIDDEN);
            }

            // ✅ 2. Validate the input data
            $validatedData = $request->validate([
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8|confirmed',
                'is_admin' => 'sometimes|boolean',
                'is_active' => 'sometimes|boolean',
                'email_is_verified' => 'sometimes|boolean',
            ]);

            // ✅ 3. Call the model function to update the user
            $user = User::updateUser($id, $validatedData);

            // ✅ 4. Return a success response with updated data
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            // ✅ 5. Catch and return any errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
