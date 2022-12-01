<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
  
class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
          
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
        
            $user = Socialite::driver('google')->user();
         
            $finduser = User::where('google_id', $user->id)->first();
         
            if($finduser){
         
                Auth::login($finduser);
        
                $data = [
                    'id' => $finduser->id,
                    'role' => $finduser->role,
                    'token' => $finduser->createToken('API Token')->accessToken,
                ];
                return response()->json($data, 200);
         
            }else{
                $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'google_id'=> $user->id,
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