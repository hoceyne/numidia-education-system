<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
  
class SocialController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectTo($driver)
    {
        return response()->json([
            'url' => Socialite::driver($driver)->stateless()->redirect()->getTargetUrl(),
        ]);
}

    
          
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleCallback($driver)
    {
        try {
        
            $user = Socialite::driver($driver)->stateless()->user();
         
//             $finduser = User::where($driver.'_id', $user->id)->first();
         
//             if($finduser){
         
//                 Auth::login($finduser);
        
//                 $data = [
//                     'id' => $finduser->id,
//                     'role' => $finduser->role,
//                     'token' => $finduser->createToken('API Token')->accessToken,
//                 ];
//                 return response()->json($data, 200);
         
//             }else{
//                 $newUser = User::updateOrCreate(['email' => $user->email],[
//                         'name' => $user->name,
//                         $driver.'_id'=> $user->id,
//                     ]);
         
//                 Auth::login($newUser);
        
//                 $data = [
//                     'id' => $newUser->id,
//                     'role' => $newUser->role,
//                     'token' => $newUser->createToken('API Token')->accessToken,
//                 ];
//                 return response()->json($data, 200);
//             }
            return redirect('http://localhost:3000/login');
            return response()->json($user,200);
        
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
