<?php

namespace App\Http\Controllers;

use App\Models\Loadout;
use App\Models\Parameter;
use Illuminate\Http\Request;

class LoadoutController extends Controller
{
    public function index()
    {
        $loadouts = Loadout::all();
        $parameters = Parameter::all();
        return view('parameters.manage', compact('loadouts', 'parameters'));
    }

    public function show($id)
    {
        $loadout = Loadout::with('parameters')->find($id);

        if (!$loadout) {
            return response()->json(['error' => 'Loadout not found'], 404);
        }

        return response()->json($loadout);
    }

    public function store(Request $request)
    {
        $parameters = $request->input('parameters');

        // Vérifier si les paramètres sont un tableau avant de les compter
        $parametersCount = is_array($parameters) ? count($parameters) : 0;

        // Vérifier si le nombre de paramètres est dans l'intervalle souhaité
        if ($parametersCount < 1 || $parametersCount > 8) {
            return redirect()->route('parameters.manage')
                ->with('error', 'Pour créer un loadout, vous devez sélectionner au moins un paramètre et au maximum 8.');
        }

        $loadout = Loadout::create($request->all());
        $loadout->parameters()->attach($parameters);
        return redirect()->route('parameters.manage');
    }

    public function update(Request $request, Loadout $loadout)
    {
        // Vérifier si le loadout est utilisé par un chantier
        if ($loadout->chantiers()->exists()) {
            return redirect()->route('parameters.manage')
                ->with('error', 'Le modèle ne peut pas être modifié car il est associé à un chantier.');
        }

        $loadout->update($request->all());
        $loadout->parameters()->sync($request->parameters);
        return redirect()->route('parameters.manage');
    }

    public function destroy(Loadout $loadout)
    {
        // Vérifier si le loadout est utilisé par un chantier
        if ($loadout->chantiers()->exists()) {
            return redirect()->route('parameters.manage')
                ->with('error', 'Le modèle ne peut pas être supprimé car il est associé à un chantier.');
        }

        $loadout->delete();
        return redirect()->route('parameters.manage');
    }
}
