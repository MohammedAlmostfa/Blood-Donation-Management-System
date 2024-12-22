<?php

namespace App\Services;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthService
 *
 * This service class handles the core authentication logic for user login, registration, logout,
 * and token refresh operations. It interacts with the `Auth` facade to perform authentication actions
 * and generate tokens.
 *
 * @package App\Services
 */
class AuthService
{
    /**
     * Attempt to log in the user with the provided credentials.
     *
     * This method checks if the credentials provided (email and password) are valid.
     * If they are valid, it returns a new JWT token for the user. If invalid, it returns an error response.
     *
     * @param array $credentials The user's email and password.
     * @return array The response containing the authentication token or an error message with the status code.
     */
    public function login($credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($this->respondWithToken($token));
    }

    /**
     * Register a new user and log them in immediately.
     *
     * This method creates a new user record in the database, generates a JWT token for the user,
     * and returns a success message along with the token.
     *
     * @param array $data The user registration data, including name, email, and password.
     * @return array The response containing a success message, the authentication token, and the status code.
     */
    public function register($data)
    {

        // Create a new user record in the database

        $user = User::create([
            'first_name' => $data['first_name'],
              'last_name' => $data['last_name'],
            'email' => $data['email']??null,
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],

        ]);


        // Log the user in and generate a token
        $token = Auth::login($user);

        // Return a success response with the token
        return [
            'message' => 'User created successfully',
            'token' => $token,
            'status' => 201, // HTTP status code for "Created"
        ];
    }

    /**
     * Log out the currently authenticated user.
     *
     * This method logs out the user by invalidating the current session and token.
     * The user is effectively logged out and cannot access protected routes until they log in again.
     *
     * @return array The response confirming the user has logged out with the status code.
     */
    public function logout()
    {
        // Log the user out by invalidating their token
        Auth::logout();

        // Return a response confirming the logout
        return ['message' => 'Successfully logged out', 'status' => 200];
    }

    /**
     * Refresh the authentication token for the currently authenticated user.
     *
     * This method generates a new authentication token and provides updated information.
     * It is typically used when the user's current token is about to expire.
     *
     * @return array The response containing the new authentication token and related information.
     */
    public function refresh()
    {
        // Refresh the token and return the new token details
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Format the response with the authentication token and user details.
     *
     * This method prepares the response format for returning the token, token type,
     * token expiration time, and user details.
     *
     * @param string $token The JWT authentication token.
     * @return array The formatted response including the token, token type, expiration time, and user details.
     */
    protected function respondWithToken($token)
    {
        // Return a structured response with token details
        return [
            'access_token' => $token,         // The JWT token
            'token_type' => 'bearer',         // The type of token (bearer token)
            'expires_in' => Auth::factory()->getTTL() * 60,  // Token expiration time in seconds
            'user' => Auth::user(),           // The authenticated user's data
        ];
    }
}
