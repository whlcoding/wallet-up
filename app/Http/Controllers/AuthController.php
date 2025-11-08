<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'Welcome to the Wallet UP API'
        ], 200);
    }

    /**
     * logout
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // $request->user()->currentAccessToken()->delete();
        // $request->user()->tokens()->delete();

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'errors' => ['You are not logged in'],
                'redirect' => 'login'
            ], 404);
        }

        $user->currentAccessToken()->delete();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }

    /**
     * login
     *
     * @param  Request $args
     * @return JsonResponse
     */
    public function login(Request $args): JsonResponse
    {
        $validation = Validator::make($args->only(['email', 'password']), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:245']
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()->all()
            ], 422);
        }

        $credentials = $args->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'errors' => ['Invalid credentials']
            ], 401);
        }

        $token = Auth::user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token_access' => $token,
            'token_type' => 'Bearer',
            'user' => Auth::user(),  // Just for testing, create a dto to return only the necessary data
            'wallets' => Auth::user()->wallets
        ], 200);
    }

    /**
     * register
     *
     * @param  Request $args
     * @return JsonResponse
     */
    public function signup(Request $args): JsonResponse
    {
        $validation = Validator::make($args->only(['first_name', 'last_name', 'email', 'password', 'password_confirmation']), [
            'first_name' => ['required', 'string', 'max:245'],
            'last_name' => ['required', 'string', 'max:245'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:245'],
            'password_confirmation' => ['required', 'string', 'max:245', 'same:password']
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()->all()
            ], 422);
        }

        $user = User::where('email', $args->email)->first();

        if ($user) {
            return response()->json([
                'errors' => ['Email already exists']
            ], 422);
        }

        $user = User::create([
            'first_name' => $args->first_name,
            'last_name' => $args->last_name,
            'email' => $args->email,
            'password' => Hash::make($args->password)
        ]);

        (new WalletService)->createFirstWallet($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Your account has been created successfully',
            'token_access' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }
}
