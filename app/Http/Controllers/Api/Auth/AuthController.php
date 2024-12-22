<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\AuthRequest\LoginRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\AuthRequest\RegisterRequest;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

/**
 * Class AuthController
 *
 * This controller manages the user authentication actions, such as logging in, registering,
 * logging out, refreshing tokens, and fetching the authenticated user's data.
 * It interacts with the AuthService to handle the underlying business logic.
 *
 * @package App\Http\Controllers\Api\Auth
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     * The service responsible for handling authentication logic.
     */
    protected $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService The service that handles authentication actions.
     * This is injected via the constructor to ensure that we follow the Dependency Injection pattern.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the login request.
     *
     * This method processes the login attempt by validating the user credentials
     * and generating an authentication token if successful.
     *
     * @param LoginRequest $request The validated request data containing the email and password.
     * @return \Illuminate\Http\JsonResponse The response containing the authentication token or an error message.
     */
    public function login(LoginRequest $request)
    {
        $validationdata=$request->validated();
        // Call the AuthService to handle the login logic
        $response = $this->authService->login($validationdata);

        // Return the response (token or error message)
        return response()->json($response);
    }

    /**
     * Handle the registration request.
     *
     * This method processes a new user registration, validates the request data,
     * and creates a new user. Afterward, it returns the authentication token for the new user.
     *
     * @param RegisterRequest $request The validated request data.
     * @return \Illuminate\Http\JsonResponse The response containing the authentication token or an error message.
     */
    public function register(RegisterRequest $request)
    {
        $validationdata=$request->validated();
        // Call the AuthService to handle the user registration
        $response = $this->authService->register($validationdata);

        // Return the response (success or error)
        return response()->json($response, $response['status']);
    }

    /**
     * Handle the logout request.
     *
     * This method logs the user out by invalidating the JWT token.
     *
     * @return \Illuminate\Http\JsonResponse The response confirming the user has logged out successfully.
     */
    public function logout()
    {
        // Call the AuthService to handle the logout logic
        $response = $this->authService->logout();
        // Return the response (confirmation or error)
        return response()->json($response, $response['status']);
    }

    /**
     * Handle the token refresh request.
     *
     * This method refreshes the user's authentication token and returns the new token.
     * It is useful when the user's current token is nearing expiration.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the new authentication token.
     */
    public function refresh()
    {
        // Call the AuthService to handle token refresh
        $response = $this->authService->refresh();

        // Return the new token or error message
        return response()->json($response);
    }

    /**
     * Get the authenticated user.
     *
     * This method retrieves the currently authenticated user based on the JWT token.
     * If the token is invalid or expired, it returns an error response.
     *
     * @param Request $request The HTTP request instance, used to get the token from headers.
     * @return \Illuminate\Http\JsonResponse The response containing the authenticated user's data or an error message.
     */
    public function me(Request $request)
    {
        try {
            // Retrieve the authenticated user based on the JWT token
            $user = JWTAuth::parseToken()->authenticate();

            // Check if user exists
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Return the user's data with a 200 OK response
            return response()->json(['user' => $user], 200);
        } catch (JWTException $e) {
            // If any issue occurs with the token (invalid, expired, etc.)
            return response()->json(['error' => 'Token is invalid'], 401);
        }
    }
}
