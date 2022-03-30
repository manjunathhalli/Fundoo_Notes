<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * @OA\Post(
     *   path="/api/auth/register",
     *   summary="register",
     *   description="register the user for login",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"first_name","last_name","email", "password", "confirm_password"},
     *               @OA\Property(property="first_name", type="string"),
     *               @OA\Property(property="last_name", type="string"),
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="confirm_password", type="password")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="User Scceesfully Registerd"),
     * )  
    
    /**
     * Register a User.
     * path="api/register",
     * description="register the user for login",
     * required=("first_name","last_name", "email", "password", "confirm_password")
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
        Cache::remember('users', 3600, function () {
            return DB::table('users')->get();
        });
        return response()->json([
            'message' => 'User Successfully Registerd',
        ], 200);
    }


    /**
     * @OA\Post(
     *   path="/api/auth/login",
     *   summary="login",
     *   description=" login ",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="password", type="password"),
     *            ),
     *        ),
     *    ),
     * @OA\Response(response=200, description="Login successfull"),
     * @OA\Response(response=401, description="email not found register first"),
     * 
     * )
     * Takes the POST request and user credentials checks if it correct,
     * if so, returns JWT access token.
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        Cache::remember('users', 3600, function () {
            return DB::table('users')->get();
        });
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Log::error('User failed to login.', ['id' => $request->email]);
            return response()->json([
                'message' => 'email not found register first'
            ], 401);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        Log::info('Login Success : ' . 'Email Id :' . $request->email);
        return response()->json([
            'access_token' => $token,
            'message' => 'Login successfull'
        ], 200);
    }

    /**
     * Get the token array structure.
     * function for creating the token
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function crateNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    /**   
     *
     * Takes the GET request and JWT access token to show the user profile
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/api/auth/profile",
     *   summary="userProfile",
     *   description="userProfile ",
     *   @OA\RequestBody(      
     *    ),
     *   @OA\Response(response=404, description="Invalid authorization token"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     */
    public function profile()
    {

        return response()->json(auth()->user());
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/api/auth/logout",
     *   summary="logout",
     *   description=" logout ",
     *  @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"token",},
     *               @OA\Property(property="token", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="User successfully signed out"),
     * )
     */

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'User logged out'
        ], 201);
    }


    /**
     * This function will take image
     * as input and save in AWS S3
     * and will save link in database
     * @return \Illuminate\Http\JsonResponse
     */

    public function addProfileImage(Request $request)
    {

        $request->validate([

            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $user = JWTAuth::user();
        $user = User::where('email', $user->email)->first();

        if ($user) {
            $path = Storage::disk('s3')->put('images', $request->image);
            $url = env('AWS_URL') . $path;
            User::where('email', $user->email)
                ->update(['profilepic' => $url]);
            return response()->json(['message' => 'Profilepic Successsfully Added', 'URL' => $url], 201);
        } else {
            return response()->json(['message' => 'We cannot find a user'], 400);
        }
    }


    /**
     * This function will take image
     * as input and save in AWS S3
     * and will save link in database
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateProfileImage(Request $request)
    {
        $request->validate([

            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        $user = JWTAuth::user();

        $user = User::where('email', $user->email)->first();
        if ($user) {
            $profile_pic = $user->profilepic;
            if ($request->image) {
                $path = str_replace(env('AWS_URL'), '', $user->profilepic);

                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
                $path = Storage::disk('s3')->put('images', $request->image);
                $pathurl = env('AWS_URL') . $path;
                $user->profilepic = $pathurl;
                $user->save();
            }
            return response()->json([
                'piv' => $profile_pic,
                'message' => 'Profilepic Successsfully update', 'URL' => $pathurl
            ], 201);
        } else {
            return response()->json(['message' => 'We cannot find a user'], 400);
        }
    }

    /**
     * This function will remove image
     * from AWS S3
     * and will remove link from database
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteProfileImage()
    {
        $user = JWTAuth::user();

        $user = User::where('email', $user->email)->first();
        if ($user) {
            $profile_pic = $user->profilepic;
            $path = str_replace(env('AWS_URL'), '', $user->profilepic);

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            $user->delete($user->email);
            $user->save();
            return response()->json([
                'message' => 'Profilepic Deleted Successsfully '
            ], 201);
        } else {
            return response()->json(['message' => 'We cannot find a user'], 400);
        }
    }
}
