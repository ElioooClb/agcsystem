<?php
// DEBUT - [SPECGT9] - Service de gestion d'état des chantiers
// Bonne pratique : injection de dépendance, requête sql située dans le service et non le contrôleur.
namespace App\Services;

use Carbon\Carbon;
use App\Models\Chantier;
use App\Models\State;
use Illuminate\Http\JsonResponse;

/**
 * Work site states service
 * Version : 2.1
 * [SPECGT9] - Work site states management service
 */
class WorkSiteStatesService
{
    private Chantier $workSite;
    // Define the work site states
    private Array $workSiteStates = [
        "initial" => "ST_FACT1A",
        "billable" => "ST_FACT2A",
        "pendingArchiving" => "ST_FACT3A",
        "partiallyBilled" => "ST_FACT2B",
        "pendingApproval" => "ST_FACT1B",
        "archived" => "ST_FACT1C"
    ];
    // Define the allowed transitions to secure the state machine
    private Array $allowedForwardTransitions = [
        'initial' => ['billable', 'partiallyBilled'],
        'billable' => ['pendingArchiving'],
        'pendingArchiving' => ['archived'],
        'partiallyBilled' => ['pendingApproval'],
        'pendingApproval' => ['partiallyBilled', 'billable'],
        'archived' => [],
    ];
    // Define the backward transitions to secure the state machine
    private Array $allowedBackwardTransitions = [
        'initial' => [],
        'billable' => ['initial', 'partiallyBilled'],
        'pendingArchiving' => ['billable'],
        'partiallyBilled' => ['initial'],
        'pendingApproval' => ['partiallyBilled'],
        'archived' => ['pendingArchiving']
    ];

    /**
     * Initialize a work site by loading it from the database
     * Version 2.1
     * [SPECGT9]
     * @param int $id - Work site id
     * @return void
     */
    public function initWorkSite(int $id): void
    {
        $this->workSite = Chantier::find($id);
    }

    /**
     * Get the work site
     * Version 2.3
     * [UNIT-TEST]
     * @return Chantier Work site
     */
    public function getWorkSite(): Chantier
    {
        return $this->workSite;
    }

    /**
     * Get the state of a work site
     * Version 2.1
     * [SPECGT9]
     * @return State Work site state
     */
    public function getState(): State
    {
        return $this->workSite->states;
    }

    /**
     * Get the work site states
     * Version 2.1
     * [SPECGT9]
     * @return array Work site states list
     */
    public function getWorkSiteStates(): array
    {
        return $this->workSiteStates;
    }

    /**
     * Update the state of a work site
     * Version 2.1
     * [SPECGT9]
     * NB : 404 return the offline.html page
     * @param string $state - Work site state
     * @return JsonResponse Response of the state update request (success or errors)
     */
    public function updateState(string $state): JsonResponse
    {
        if (!$this->hasStateKey($state)) return response()->json(['errors' => 'L\état n\a pas été trouvé'], 403);
        if (!$this->isTransitionAllowed($state)) return response()->json(['errors' => 'La transition est interdite'], 403);
        if ($state === 'pendingArchiving') {
            if (!$this->workSite->invoices || empty($this->workSite->invoices->number)) {
                return response()->json(['errors' => 'Numéro de facture introuvable'], 403);
            }
        }

        if ($state === 'pendingApproval') {
            if (!$this->workSite->invoices) {
                return response()->json(['errors' => 'Facture introuvable'], 403);
            }
        }


        $this->workSite->state = $this->workSiteStates[$state];
        $this->workSite->save();
        return response()->json(['success' => 'État mis à jour'], 200);
    }

    /**
     * Downgrade the state of a work site
     * Version 2.1
     * [SPECGT9]
     * @param string $state - Work site state
     * @return JsonResponse Response of the state downgrade request (success or errors)
     */
    public function downgradeState(string $state): JsonResponse
    {
        if (!$this->hasStateKey($state)) return response()->json(['errors' => 'State not found'], 404);
        if (!$this->isBackwardTransitionAllowed($state)) return response()->json(['errors' => 'Transition not allowed'], 403);
        $this->workSite->state = $this->workSiteStates[$state];
        $this->workSite->save();
        return response()->json(['success' => 'State updated'], 200);
    }

    /**
     * Check if a transition is allowed
     * Version 2.1
     * [SPECGT9]
     * @param string $state - Work site state
     * @return bool True if the transition is allowed, false otherwise
     */
    private function isTransitionAllowed(string $state): bool
    {
        return in_array($state, $this->allowedForwardTransitions[$this->workSite->states->status] ?? []);
    }

    /**
     * Check if a backward transition is allowed
     * Version 2.1
     * [SPECGT9]
     * @param string $state - Work site state
     * @return bool True if the backward transition is allowed, false otherwise
     */
    private function isBackwardTransitionAllowed(string $state): bool
    {
        return in_array($state, $this->allowedBackwardTransitions[$this->workSite->states->status] ?? []);
    }

    /**
     * Check if a work site has a specific state
     * Version 2.1
     * [SPECGT9]
     * @param string $key - Work site state key
     * @return bool True if the work site has the state, false otherwise
     */
    private function hasStateKey(string $key): bool
    {
        return array_key_exists($key, $this->workSiteStates);
    }
}
// FIN - [SPECGT9] - Service de gestion d'état des chantiers
