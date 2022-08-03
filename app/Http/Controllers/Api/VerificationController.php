<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            return response(['message' => 'Email already verified.']);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response(['message' => 'Email successfully verified.']);
    }

    public function resend(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response(['message' => 'Email already verified.']);
        }
        
        $request->user()->sendEmailVerificationNotification();

        if($request->wantsJson()) {
            return response(['message' => 'Email successfully verified.']);
        } 

        return back()->with('resend', true);
    }
}
