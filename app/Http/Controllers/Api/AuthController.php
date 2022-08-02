<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $user->createToken('User Token')->accessToken;

                return response([
                    'message' => 'Login Successful',
                    'token' => $token,
                    'user' => $user
                ], 200);
            }
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }

        return response([
            'message' => 'Your email or password is invalid, please try again.',
        ], 401);
    }

    public function register(RegisterRequest $request) {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('User Token')->accessToken;

            return response([
                'message' => 'Registration successful.',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();

        $response = ['message' => 'Logout successful.'];

        return response($response, 200);
    }
}
