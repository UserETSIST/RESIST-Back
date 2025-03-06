<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class AuthController extends Controller
{


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
            'is_admin' => $result['is_admin'],
            'is_active' => $result['is_active'],
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




    public function sendResetLinkEmail(Request $request)
    {
        // Validate email format (but don't check existence yet)
        $request->validate([
            'email' => 'required|email',
        ]);

        // Manually check if the email exists in the users table
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this email address.',
            ], 404);
        }

        // Generate a new password reset token
        $token = Str::random(60); // Create a random token

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token), // Store hashed token
                'created_at' => now(),
            ]
        );

        // Return the token and a custom reset URL
        return response()->json([
            'success' => true,
            'message' => 'Password reset token generated successfully.',
            'reset_password_token' => $token,
            'email' => $request->email
        ], 200);
    }



    public function reset(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Retrieve the reset token from the database
        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        // If no token exists or it's invalid, return an error
        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the reset token from the database to prevent reuse
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
        ], 200);
    }


}
