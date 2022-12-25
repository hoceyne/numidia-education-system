<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    //
    public function index($id = null)
    {
        if ($id) {
            $departement = Departement::find($id);
            $departement['groups'] = [];
            foreach ($departement->groups() as $group) {
                # code...
                array_push($departement['groups'], $group);
            }

            return response()->json($departement, 200);
        } else {
            $departements = Departement::all();


            return response()->json($departements, 200);
        }
    }

    public function create(Request $request)
    {
        $departement = Departement::create([
            'education' => $request->education,
            'sepciality' => $request->sepciality,
            'year' => $request->year,

        ]);
        $branch = Branch::find($request->branch_id);
        $departement->branch()->save($branch);
        $departement->save();

        return response()->json(200);
    }

    public function delete($id)
    {

        $departement = Departement::find($id);

        $departement->delete();

        return response()->json(200);
    }

    public function update(Request $request, $id)
    {

        $old_departement = Departement::find($id);
        $old_departement->delete();

        $departement = Departement::updateOrCreate(['id' => $id], [
            'education' => $request->education,
            'sepciality' => $request->sepciality,
            'year' => $request->year,
        ]);
        $branch = Branch::find($request->branch_id);
        $departement->branch()->save($branch);

        $departement->save();

        return response()->json(200);
    }
}
