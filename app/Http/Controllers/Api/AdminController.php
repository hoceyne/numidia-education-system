<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Departement;
use App\Models\Group;
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

    public function parents($id = null)
    {
        if (!$id) {
            $parents = Supervisor::all();
            foreach ($parents as $key => $value) {
                # code...
                $parents[$key] = $value->user();
            }
        } else {
            $parents = Supervisor::where('id', $id)->first();
            $temp = [];
            foreach ($parents->students() as $student) {
                $student['user_info'] = $student->user();
                array_push($student);
                # code...
            }
            $parents['students'] = $temp;
        }
        return $parents;
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

    public function groups($id = null)
    {
        if (!$id) {
            $groups = Group::all();
        } else {
            $groups = Group::where('id', $id);
        }
        return $groups;
    }

    public function create_group(Request $request)
    {

        $teacher = Teacher::find($request->teacher_id);
        $departement = Departement::find($request->departement_id);
        $group = Group::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'members' => 0,
        ]);
        $teacher->groups()->save($group);
        $departement->groups()->save($group);

        return response()->json(200);
    }

    public function delete_group($id)
    {

        $group = Group::find($id);

        $group->delete();

        return response()->json(200);
    }

    public function update_group(Request $request, $id)
    {

        $group = Group::find($id);
        $group->name = $request->name;
        $group->capacity = $request->capacity;
        $group->members = $request->students->length();

        foreach ($request->students as $id) {
            # code...
            $group->students()->save(Student::find($id));
        }

        $group->save();

        return response()->json(200);
    }


    public function sessions($id = null)
    {
        if ($id) {
            $session = Session::find($id);
            $session['teacher'] = $session->teacher();
            $session['group'] = $session->group();
            return response()->json($session, 200);
        } else {
            $sessions = Session::all();
            foreach ($sessions as  $session) {
                # code...
                $session['teacher'] = $session->teacher();
                $session['group'] = $session->group();
            }

            return response()->json($sessions, 200);
        }
    }

    public function create_session(Request $request)
    {
        $session = Session::create([
            'classroom' => $request->classroom,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        $session->teacher()->save(Teacher::find($request->teacher_id));

        $session->group()->save(Group::find($request->group_id));

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

        $session->group()->save(Group::find($request->group_id));

        $session->save();

        return response()->json(200);
    }


    public function archive()
    {

        return response()->json(200);
    }
}
