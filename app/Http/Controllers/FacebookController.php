<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FacebookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            $finduser = User::where('facebook_id', $user->id)->first();

            if ($finduser) {

                Auth::login($finduser);

                $data = [
                    'id' => $finduser->id,
                    'role' => $finduser->role,
                    'token' => $finduser->createToken('API Token')->accessToken,
                ];
                return response()->json($data, 200);
            } else {
                $newUser = User::updateOrCreate(['email' => $user->email], [
                    'name' => $user->name,
                    'facebook_id' => $user->id,
                ]);

                Auth::login($newUser);

                $data = [
                    'id' => $newUser->id,
                    'role' => $newUser->role,
                    'token' => $newUser->createToken('API Token')->accessToken,
                ];
                return response()->json($data, 200);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
