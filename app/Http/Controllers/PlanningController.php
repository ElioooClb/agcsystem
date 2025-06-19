<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chantier;
use Carbon\Carbon;

class PlanningController extends Controller
{
    public function jour(Request $request)
    {
        $date = $request->input('realisation_date', Carbon::tomorrow()->toDateString());

        $chantiers = Chantier::with(['events', 'taches']) // si 'taches' utilisé dans la vue
            ->whereDate('created_at', '<=', $date)
            ->whereDate('realisation_date', '>=', $date)
            ->get();

        return view('livewire.jour', compact('chantiers', 'date'));
    }
}
