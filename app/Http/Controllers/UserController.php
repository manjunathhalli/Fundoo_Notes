<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\User;

/**
 * This controller is for user registration, login, logout
 * and emailverify
 */
class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     */

    public function _construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a User.
     * path="api/register",
     * description="register the user for login",
     * required=("firs_tname","last_name", "email", "password", "confirm_password")
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:18',
            'last_name' => 'required|string|min:2|max:18',
            'email' => 'required|string|email|max:50',
            'password' => 'required|string|min:6|max:8',
            'confirm_password' => 'required|same:password',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]

        ));
        return response()->json([
            'message' => 'User Scceesfully Registerd',
        ], 201);
    }

    /**
     * Get a JWT via given credentials.
     * Login a user
     * path="api/login",
     * description="user login",
     * required=("email","password")
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:8'

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->crateNewToken($token);
    }

    /**
     * Get the token array structure.
     * function for creating the token
     */
    public function crateNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    /**
     * UserProfile
     * path="api/Profile"
     * description="user profile"
     * required="Token which is generated after login"
     */
    public function profile()
    {

        return response()->json(auth()->user());
    }

    /**
     * logout function
     * path="api/logout"
     * description="user logout"
     * required="Token which is generated after login to logout the user"
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'User logged out'

        ]);
    }
}
