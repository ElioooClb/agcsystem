<?php

namespace App\Http\Controllers;

use App\Models\Loadout;
use App\Models\Parameter;

class ManageController
{
    public function index()
    {
        $parameters = Parameter::all();
        $loadouts = Loadout::all();
        return view('parameters.manage', compact('parameters', 'loadouts'));
    }
}
