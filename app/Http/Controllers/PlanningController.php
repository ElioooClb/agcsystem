<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chantier;
use Carbon\Carbon;

class PlanningController extends Controller
{
    public function jour(Request $request)
    {
        // Récupère la date demandée, sinon demain par défaut
        $date = $request->input('date', Carbon::tomorrow()->toDateString());

        // Filtrage des chantiers par date (à adapter selon ton schéma)
        $chantiers = Chantier::whereDate('date', $date)->get();

        return view('livewire.jour', compact('chantiers', 'date'));
    }
}
