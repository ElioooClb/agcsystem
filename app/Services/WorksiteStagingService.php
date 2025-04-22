<?php

namespace App\Services;

use App\Models\Chantier;
use App\Events\WorksiteStageUpdated;
use Illuminate\Support\Facades\Log;

class WorksiteStagingService
{
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

    private Chantier $worksite;

    public function __construct(Chantier $worksite)
    {
        $this->worksite = $worksite;
    }

    public function getCurrentStage(): string
    {
        return $this->worksite->stage_state;
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
        $this->worksite->stage_state = $nextStage;
        $this->worksite->save();

        // Émettre un événement pour permettre l'envoi de mails
        event(new worksiteStageUpdated(
            $this->worksite,
            $oldStage,
            $this->worksite->stage_state,
            'forward'
        ));
        

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
        $this->worksite->stage_state = $previousStage;
        $this->worksite->save();

        // Émettre un événement pour permettre l'envoi de mails
        event(new worksiteStageUpdated(
            $this->worksite,
            $oldStage,
            $this->worksite->stage_state,
            'backward'
        ));
        

        return true;
    }
}
