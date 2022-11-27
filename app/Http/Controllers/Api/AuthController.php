<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $remember = $request->remember_me;
            Auth::login($user, $remember);
            $data = [
                'token' => $user->createToken('API Token')->accessToken,
            ];

            return response()->json($data, 200);
        } else {
            return abort(403);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed',],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'profile_picture' => "default_profile_picture.jpeg",
        ]);


        if ($user->role == 'teacher') {
            $user->teacher()->save(new Teacher());
        } else if ($user->role == 'student') {
            $user->student()->save(new Student());
        } else if ($user->role == 'admin') {
            $user->admin()->save(new Admin());
        } else if ($user->role == 'supervisor') {
            $user->supervisor()->save(new Supervisor());
        }

        $user->save();
        Auth::login($user);
        $token =$user->createToken('API Token')->accessToken;
        return $token;
    }

    public function logout()
    {
        $user = Auth::user();
        if (Auth::check($user)) {
            $user->token()->revoke();
            $user->tokens()->delete();
            return response(200);
        } else {
            abort(403);
        }
    }

    public function forgotpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string'
        ]);
        $user = User::where('email', $request['email'])->first();
        if (!$user) {
            abort(404);
        }
        $password = Str::random(10);
        $user->password = Hash::make($password);
        $user->save();
        try {
            //code...
            // Email the user new password
        } catch (\Throwable $th) {
            //throw $th;
            abort(400);
        }

        return response(200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        if ($user->profile_picture !=  $request->profile_picture) {
            if ($user->profile_picture != "default_profile_picture.jpeg") {
                File::delete('files/profile_pictures/' . $user->profile_picture);
            }
            $file = $request->file('file');
            $filename = $user->id . '.' . $file->getClientOriginalExtension();
            $file->move('files/profile_pictures/', $filename);
        }

        $data = [
            'name' => $request->name,
            'gender' => $request->gender,
            'profile_picture' => $filename,
        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        User::where('id', $id)->update($data);

        return response(200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'gender' => $user->gender,
            'profile_picture' => $user->profile_picture,
        ];
        return response()->json($data, 200);
    }
}
