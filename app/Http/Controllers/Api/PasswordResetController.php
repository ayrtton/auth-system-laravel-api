<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetMailRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendPasswordResetMail(PasswordResetMailRequest $request) {
        $email = $request->email;

        if(User::where('email', $email)->doesntExist()) {
            return response([
                'message' => 'Invalid email address.'
            ], 401);
        }

        $token = rand(10, 100000);

        try {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
            ]);

            Mail::to($email)->send(new PasswordResetMail($email, $token));

            return response([
                'message' => 'Password reset email sent successfully.',
            ], 200);

        } catch(Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function resetPassword(PasswordResetRequest $request) {
        $email = $request->email;
        $token = $request->token;
        $password = Hash::make($request->password);
        
        $emailCheck = DB::table('password_resets')->where('email', $email)->first();
        $tokenCheck = DB::table('password_resets')->where('token', $token)->first();

        if(!$emailCheck) {
            return response([
                'message' => "Email address not found.",
            ], 401);
        }

        if(!$tokenCheck) {
            return response([
                'message' => "Invalid token."
            ], 401);
        }

        DB::table('users')->where('email', $email)->update(['password' => $password]);
        DB::table('password_resets')->where('email', $email)->delete();

        return response([
            'message' => 'Your password has been successfully reset.'
        ], 200);
    }
}
