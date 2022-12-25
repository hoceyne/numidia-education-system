<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Departement;
use App\Models\Group;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentController extends Controller
{
    public function sessions($id = null)
    {

        if ($id) {
            $session = Session::find($id);
            $session['teacher'] = $session->teacher();
            $session['group'] = $session->group();
            return response()->json($session, 200);
        } else {
            $user = User::find(Auth::user()->id);
            $supervisor = $user->supervisor();
            $all_sessions = [];
            foreach ($supervisor->students() as $student) {

                foreach ($student->groups() as $group) {
                    # code..
                    $sessions = $group->sessions();
                    $temp = [];
                    foreach ($sessions as $session) {

                        if ($session->state == 'approved') {
                            $session['teacher'] = $session->teacher();
                            array_push($temp, $session);
                        }
                    }
                }
                $all_sessions[$student->id]['sessions'] = $temp;
                $all_sessions[$student->id]['group'] = $group;
            }


            return response()->json($all_sessions, 200);
        }
    }

    public function add_student(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $supervisor = $user->supervisor();
        $password = Str::random(10);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'gender' => $request->gender,
            'password' => Hash::make($password),
        ]);

        $student = new Student();
        $user->student()->save($student);
        $supervisor->students()->save($student);
        return response()->json(200);
    }

    public function students($id = null)
    {
        if (!$id) {
            $user = User::find(Auth::user()->id);
            $supervisor = $user->supervisor();
            $students = $supervisor->students();
            foreach ($students as $key => $value) {
                # code...
                $students[$key] = $value->user();
            }
        } else {
            $students = Student::where('id', $id)->first()->user();
        }
        return $students;
    }
}
