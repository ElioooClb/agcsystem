<?php

namespace App\Services;

use App\Models\Chantier;
use Illuminate\Support\Facades\Event;

class WorksiteStagingService
{
    private array $stages = [
        'STAGE_1A' => 'initial',
        'STAGE_1B' => 'pendingApproval',
        'STAGE_1C' => 'archived',
    ];

    private array $allowedForwardTransitions = [
        'STAGE_1A' => ['STAGE_1B'],
        'STAGE_1B' => ['STAGE_1C'],
        'STAGE_1C' => [],
    ];

    private array $allowedBackwardTransitions = [
        'STAGE_1A' => [],
        'STAGE_1B' => ['STAGE_1A'],
        'STAGE_1C' => ['STAGE_1B'],
    ];

    private Chantier $workSite;

    public function __construct(Chantier $workSite)
    {
        $this->workSite = $workSite;
    }

    public function getCurrentStage(): string
    {
        return $this->workSite->stage_state;
    }

    public function canTransitionForward(): bool
    {
        $currentStage = $this->getCurrentStage();
        return !empty($this->allowedForwardTransitions[$currentStage]);
    }

    public function canTransitionBackward(): bool
    {
        $currentStage = $this->getCurrentStage();
        return !empty($this->allowedBackwardTransitions[$currentStage]);
    }

    public function getNextStage(): ?string
    {
        $currentStage = $this->getCurrentStage();
        return $this->allowedForwardTransitions[$currentStage][0] ?? null;
    }

    public function getPreviousStage(): ?string
    {
        $currentStage = $this->getCurrentStage();
        return $this->allowedBackwardTransitions[$currentStage][0] ?? null;
    }

    public function transitionForward(): bool
    {
        if (!$this->canTransitionForward()) {
            return false;
        }

        $nextStage = $this->getNextStage();
        if (!$nextStage) {
            return false;
        }

        $oldStage = $this->getCurrentStage();
        $this->workSite->stage_state = $nextStage;
        $this->workSite->save();

        // Émettre un événement pour permettre l'envoi de mails
        Event::dispatch('worksite.stage.changed', [
            'worksite' => $this->workSite,
            'oldStage' => $oldStage,
            'newStage' => $nextStage,
            'direction' => 'forward'
        ]);

        return true;
    }

    public function transitionBackward(): bool
    {
        if (!$this->canTransitionBackward()) {
            return false;
        }

        $previousStage = $this->getPreviousStage();
        if (!$previousStage) {
            return false;
        }

        $oldStage = $this->getCurrentStage();
        $this->workSite->stage_state = $previousStage;
        $this->workSite->save();

        // Émettre un événement pour permettre l'envoi de mails
        Event::dispatch('worksite.stage.changed', [
            'worksite' => $this->workSite,
            'oldStage' => $oldStage,
            'newStage' => $previousStage,
            'direction' => 'backward'
        ]);

        return true;
    }
}
