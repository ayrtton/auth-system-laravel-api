<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request) {
        auth()->loginUsingId($request->route('id'));

        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException();
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect('https://login-signup-system.vercel.app/info/Email already verified.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect('https://login-signup-system.vercel.app/success/Email successfully verified!');
    }

    public function resend() {
        try {
            if (auth()->user()->hasVerifiedEmail()) {
                return response([
                    'message' => 'Email already verified.'
                ], 200);
            }
            
            auth()->user()->sendEmailVerificationNotification();

            return response([
                'message' => 'Email successfully verified.'
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
