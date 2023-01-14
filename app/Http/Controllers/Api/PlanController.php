<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Models\Plan;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    //
    public function index($id = null)
    {
        if ($id) {
            $plan = Plan::find($id);
            $plan['clients'] = [];
            foreach ($plan->clients as $client) {
                # code...
                $client['user_info'] = $client->user;
                array_push($plan['clients'], $client);
            }

            return response()->json($plan, 200);
        } else {
            $plans = Plan::all();
            foreach ($plans as  $plan) {
                # code...
                $plan['clients'] = [];
                foreach ($plan->clients as $client) {
                    # code...
                    $client['user_info'] = $client->user;
                    array_push($plan['clients'], $client);
                }
            }

            return response()->json($plans, 200);
        }
    }

    public function create(Request $request)
    {
        $plan = Plan::create([
            'price' => $request->price,
            'duration' => $request->duration,
            'benefits' => $request->benefits,
        ]);

        $plan->departement()->save(Departement::find($request->departement_id));
        $plan->teacher()->save(Teacher::find($request->teacher_id));

        $plan->save();

        return response()->json(200);
    }

    public function delete($id)
    {

        $plan = Plan::find($id);

        $plan->delete();

        return response()->json(200);
    }

    public function update(Request $request, $id)
    {

        $old_plan = Plan::find($id);
        $old_plan->delete();

        $plan = Plan::create([
            'price' => $request->price,
            'duration' => $request->duration,
            'benefits' => $request->benefits,
        ]);

        $plan->departement()->save(Departement::find($request->departement_id));
        $plan->teacher()->save(Teacher::find($request->teacher_id));
        
        $plan->save();

        return response()->json(200);
    }
}
