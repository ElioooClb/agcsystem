<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chantier;
use App\Services\WorksiteStagingService;
use Illuminate\Support\Facades\Log;


class StateController extends Controller
{
    public function handleStage(Request $request)
    {
        $validatedData = $request->validate([
            'chantierId' => 'required|exists:chantiers,id',
            'direction' => 'required|string',
        ]);
        $chantier = Chantier::find($validatedData['chantierId']);
        $stageService = new WorksiteStagingService($chantier);

        if ($validatedData['direction'] === 'forward') {
            $hasSucceed = $stageService->transitionForward();
        } else if ($validatedData['direction'] === 'backward') {
            $hasSucceed = $stageService->transitionBackward();
        }

        return response()->json([
            'success' => $hasSucceed,
            'message' => $hasSucceed ? 'Stage mis à jour avec succès' : 'Une erreur est survenue lors de la mise à jour du stage',
        ]);
    }
}
