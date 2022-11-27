<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;

class ClientController extends Controller
{
    public function sessions($id)
    {

        $user = User::find($id);
        $student = $user->student();
        $sessions = $student->sessions()->where('state', 'approved');
        foreach ($sessions as $session) {
            # code...

            $session['teacher'] = $session->teacher();
        }

        return response()->json($sessions, 200);
    }

    public function show($id)
    {
        $session = Session::find($id);
        $session['teacher'] = $session->teacher();
        $session['students'] = $session->students();
        return response()->json($session, 200);
    }
}
