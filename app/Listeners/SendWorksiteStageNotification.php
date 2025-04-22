<?php

namespace App\Listeners;

use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    public function handle(array $event): void
    {
        $worksite = $event['worksite'];
        $oldStage = $event['oldStage'];
        $newStage = $event['newStage'];
        $direction = $event['direction'];

        // Configuration de l'email en fonction du stage
        $config = $this->getEmailConfig($oldStage, $newStage, $direction);
        
        if (!$config) {
            return;
        }

        // Préparation des données du chantier
        $worksiteData = [
            'title' => $worksite->title,
            'idaff' => $worksite->idaff,
            'oldStage' => $oldStage,
            'newStage' => $newStage,
            'direction' => $direction
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
                    'to' => 'supervisor',
                    'template' => 'stage_1a_to_1b',
                    'subject' => 'Transition du chantier vers l\'étape d\'approbation'
                ]
            ],
            'STAGE_1B' => [
                'forward' => [
                    'to' => 'admin',
                    'template' => 'stage_1b_to_1c',
                    'subject' => 'Chantier archivé'
                ],
                'backward' => [
                    'to' => 'supervisor',
                    'template' => 'stage_1b_to_1a',
                    'subject' => 'Chantier retourné à l\'étape initiale'
                ]
            ],
            'STAGE_1C' => [
                'backward' => [
                    'to' => 'admin',
                    'template' => 'stage_1c_to_1b',
                    'subject' => 'Chantier réactivé'
                ]
            ]
        ];

        return $configs[$oldStage][$direction] ?? null;
    }
}
