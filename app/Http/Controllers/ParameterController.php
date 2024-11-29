<?php

namespace App\Http\Controllers;

use App\Models\Loadout;
use App\Models\Parameter;
use Illuminate\Http\Request;

class ParameterController extends Controller
{
    public function index()
    {
        $parameters = Parameter::all();
        $loadouts = Loadout::all();
        return view('parameters.manage', compact('parameters', 'loadouts'));
    }

    public function show()
    {
        return response()->json(Parameter::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|max:15',
        ]);

        Parameter::create($request->all());
        return redirect()->route('parameters.manage');
    }

    public function destroy(Parameter $parameter)
    {
        if ($parameter->loadouts()->exists()) {
            return redirect()->route('parameters.manage')
                ->with('error', 'Le paramètre ne peut pas être supprimé car il est associé à un loadout.');
        }

        $parameter->delete();
        return redirect()->route('parameters.manage');
    }
}
