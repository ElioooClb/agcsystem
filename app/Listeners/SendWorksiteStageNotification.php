<?php

namespace App\Listeners;

use App\Events\WorksiteStageUpdated;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class SendWorksiteStageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    private EmailService $emailService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Handle the event.
     */
    public function handle(WorksiteStageUpdated $event): void
    {
        $worksite = $event->worksite;
        $oldStage = $event->oldStage;
        $newStage = $event->newStage;
        $direction = $event->direction;

        // Configuration de l'email en fonction du stage
        $config = $this->getEmailConfig($oldStage, $newStage, $direction);

        if (!$config) {
            return;
        }

        // Préparation des données du worksite
        $worksiteData = [
            'title' => $worksite->title,
            'idaff' => $worksite->getIdAff(),
            'hours' => $worksite->hours,
            'materialamount' => $worksite->montantmateriel,
            'serviceamount' => $worksite->montantservice,
            'type' => $worksite->type(),
            'supervisor' => Auth::user()->name,
            'stageLabel' => $worksite->stages->label,
            'direction' => $direction,
            'updated_at' => $worksite->updated_at->format('d/m/Y H:i'),
        ];

        // Envoi de l'email
        $this->emailService->sendEmailNotification($config, $worksiteData);
    }

    /**
     * Détermine la configuration de l'email en fonction des stages
     */
    private function getEmailConfig(string $oldStage, string $newStage, string $direction): ?array
    {
        // Configuration par défaut pour les transitions
        $configs = [
            'STAGE_1A' => [
                'forward' => [
                    'to' => ['worker_02'],
                    'template' => 'emails.worksite.staging-notification',
                    'subject' => '🔔 Nouveau worksite à traiter (passage en validation)',
                ],
            ],
            'STAGE_1B' => [
                'forward' => [
                    'to' => ['worker_01'],
                    'template' => 'emails.worksite.staging-notification',
                    'subject' => '✅ worksite validé et archivé',
                ],
                'backward' => [
                    'to' => ['worker_01'],
                    'template' => 'emails.worksite.staging-notification',
                    'subject' => '⛔ Retour du worksite à l’étape initiale',
                ],
            ],
            'STAGE_1C' => [
                'backward' => [
                    'to' => ['worker_01', 'worker_02'],
                    'template' => 'emails.worksite.staging-notification',
                    'subject' => '🔄 Réactivation d’un worksite archivé',
                ],
            ],
        ];
        return $configs[$oldStage][$direction] ?? null;
    }
}
