<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    function verify(Request $request)
    {

        $user = User::find($request->route('id'));
        if (!hash_equals(
            (string) $request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            abort(403);
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return response()->json('Email has been verified', 200);
    }

    function send(Request $request)
    {
        $user = User::find($request->id);
        $user->sendEmailVerificationNotification();

        return response()->json('Verification link sent!', 200);
    }
    function view()
    {
        return view('auth.verify-email');
    }
}
