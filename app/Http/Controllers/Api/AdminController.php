<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Session;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function users($id = null)
    {
        if (!$id) {
            $users = User::all()->except(Auth::id());
        } else {
            $users = User::where('id', $id)->first();
        }
        return $users;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
        ]);
        $password = Str::random(10);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'gender' => $request->gender,
            'password' => Hash::make($password),
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

        return response()->json(200);
    }


    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_role' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->role != $request->user_role) {
            if ($user->role == 'teacher') {
                $user->teacher()->delete();
            } else if ($user->role == 'student') {
                $user->student()->delete();
            } else if ($user->role == 'admin') {
                $user->admin()->delete();
            } else if ($user->role == 'supervisor') {
                $user->supervisor()->delete();
            }


            if ($request->user_role == 'teacher') {
                $user->teacher()->save(new Teacher());
            } else if ($request->user_role == 'student') {
                $user->student()->save(new Student());
            } else if ($request->user_role == 'admin') {
                $user->admin()->save(new Admin());
            } else if ($request->user_role == 'supervisor') {
                $user->supervisor()->save(new Supervisor());
            }
        }

        $user->name = $request->name;
        $user->role = $request->user_role;
        $user->gender = $request->gender;

        $user->save();

        return response()->json(200);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        $user->forceDelete();
        return response()->json(200);
    }

    public function teachers($id = null)
    {
        if (!$id) {
            $teachers = Teacher::all();
            foreach ($teachers as $key => $value) {
                # code...
                $teachers[$key] = $value->user();
            }
        } else {
            $teachers = Teacher::where('id', $id)->first()->user();
        }
        return $teachers;
    }

    public function students($id = null)
    {
        if (!$id) {
            $students = Student::all();
            foreach ($students as $key => $value) {
                # code...
                $students[$key] = $value->user();
            }
        } else {
            $students = Student::where('id', $id)->first()->user();
        }
        return $students;
    }


    public function sessions()
    {
        $sessions = Session::all();
        foreach ($sessions as $key => $session) {
            # code...
            $session['teacher'] = $session->teacher();
            $session['students'] = $session->students();
        }

        return response()->json($sessions, 200);
    }

    public function create_session(Request $request)
    {
        $session = Session::create([
            'classroom' => $request->classroom,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        $session->teacher()->save(Teacher::find($request->teacher_id));
        foreach ($request->students as $student_id) {
            # code...
            $session->students()->save(Student::find($student_id));
        }

        $session->save();

        return response()->json(200);
    }

    public function delete_session($id)
    {

        $session = Session::find($id);

        $session->delete();

        return response()->json(200);
    }

    public function update_session(Request $request, $id)
    {

        $old_session = Session::find($id);
        $old_session->delete();

        $session = Session::create([
            'classroom' => $request->classroom,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        $session->teacher()->save(Teacher::find($request->teacher_id));
        foreach ($request->students as  $student_id) {
            # code...
            $session->students()->save(Student::find($student_id));
        }

        $session->save();

        return response()->json(200);
    }
    

    public function archive()
    {

        return response()->json(200);
    }
}
