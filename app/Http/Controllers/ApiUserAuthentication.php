<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiUserAuthentication extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(
            [
                ['error' => 'Unauthorized'],
                'message' => 'Incorrect email/password',
                
            ], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Return user info (excluding password) and token
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'message' => 'Authentication Successful',
        ], 200);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
{
    try {
        // Invalidate the token to log out the user making the request
        $token = JWTAuth::getToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 400); // Bad Request
        }

        JWTAuth::invalidate($token);

        return response()->json(['message' => 'Logged out successfully'], 200);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['error' => 'Invalid token'], 401); // Unauthorized
    } catch (\Exception $e) {
        return response()->json(['error' => 'Something went wrong, could not log out'], 500); // Internal Server Error
    }
}

}
